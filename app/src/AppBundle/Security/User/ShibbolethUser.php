<?php

namespace AppBundle\Security\User;

use AppBundle\Model\Principal;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use GuzzleHttp\Client;

/**
 * Class ShibbolethUser
 * @package AppBundle\Security\User
 */
class ShibbolethUser implements UserInterface, UserProviderInterface, \Serializable
{
    private $eppn = "";
    private $displayName = "";
    private $email = "";

    private $baseUri = "";
    private $token = null;
    private $tokenAcquiredAt;
    private $hexaaScopedKey;

    private $guzzleclient;

    private $principal;

    /**
     * ShibbolethUser constructor.
     * @param array     $shibAttributeMap
     * @param string    $hexaaScopedKey
     * @param string    $baseUri
     * @param Client    $guzzleclient
     * @param Principal $principal
     */
    public function __construct($shibAttributeMap, $hexaaScopedKey, $baseUri, Client $guzzleclient, Principal $principal)
    {
        foreach (array('eppn', 'displayName', 'email') as $key) {
            if (array_key_exists($shibAttributeMap[$key], $_SERVER)) {
                $this->$key = $_SERVER[$shibAttributeMap[$key]];
            }
        }
        $this->baseUri = $baseUri;
        $this->hexaaScopedKey = $hexaaScopedKey;
        $this->guzzleclient = $guzzleclient;
        $this->shibAttributeMap = $shibAttributeMap;
        $this->principal = $principal;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->eppn;
    }

    /**
     * @return string
     */
    public function getEppn(): string
    {
        return $this->eppn;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->guzzleclient;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return array('ROLE_USER');
    }

    /**
     * @return null
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * @return null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->eppn;
    }

    /**
     *
     */
    public function eraseCredentials()
    {
    }

    /**
     * @param string $username
     * @return $this
     */
    public function loadUserByUsername($username)
    {
        return $this;
    }

    /**
     * @param UserInterface $user
     * @return $this
     */
    public function refreshUser(UserInterface $user)
    {
        return $this;
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class === 'AppBundle\\Security\\User\\ShibbolethUser';
    }

    /** @see \Serializable::serialize()
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->eppn,
            // $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize()
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->eppn,
            // $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }

    /**
     * get token for hexaa-api
     * @return string
     */
    public function getToken():string
    {
        if (! $this->token) {
            $this->requestNewToken();
        } else {
            $principalResource = $this->principal;

/*            try {
                $principalResource->getSelf("normal", $this->token);
            } catch (ClientException $e) {
                if (401 == $e->getCode()) {
                    $this->requestNewToken();
                } else {
                    throw $e;
                }
            }
*/
            $now = new \DateTime();
            $diff = $now->diff($this->tokenAcquiredAt, true);
            if ($diff->h == 0 && $diff->d == 0 && $diff->m == 0 && $diff->y == 0) {
                return $this->token;
            } else {
                $this->requestNewToken();
            }
        }

        return $this->token;
    }

    /**
     *
     */
    private function requestNewToken()
    {
        $client   = $this->guzzleclient;

        // Create api key
        $time = new \DateTime();
        date_timezone_set($time, new \DateTimeZone('UTC'));
        $stamp = $time->format('Y-m-d H:i');
        $apiKey = hash('sha256', $this->hexaaScopedKey.$stamp);
            $response = $client->post(
                'tokens',
                array(
                    'json' => array(
                        'fedid' => $this->getEppn(),
                        'email' => $this->getEmail(),
                        'display_name' => $this->getDisplayName(),
                        'apikey' => $apiKey,
                    ),
                )
            );
            $this->token = json_decode($response->getBody(), true)['token'];
            $this->tokenAcquiredAt = $time;
    }
}
