<?php

namespace AppBundle\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class ShibbolethUser implements UserInterface, UserProviderInterface, \Serializable
{
    private $eppn = "";
    private $displayName = "";
    private $email = "";

    private $client;
    private $base_uri = "";
    private $token = null;
    private $tokenAcquiredAt;
    private $hexaaScopedKey;

    private $session;
    private $guzzleclient;

    public function __construct($shibAttributeMap, $hexaaScopedKey, $base_uri, $session, $guzzleclient)
    {
        foreach (array('eppn', 'displayName', 'email') as $key) {
            if (array_key_exists($shibAttributeMap[$key], $_SERVER)) {        
                $this->$key = $_SERVER[$shibAttributeMap[$key]];
            }
        }
        $this->base_uri=$base_uri;
        $this->hexaaScopedKey=$hexaaScopedKey;
        $this->session = $session;
        $this->guzzleclient = $guzzleclient;
    }

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

    public function getClient()
    {
        $client=new Client(
            array(
                'base_uri' => $this->base_uri,
                'headers' => array(
                    'X-HEXAA-AUTH' => $this->getToken()
                    )
                )
            );
        $client = $this->guzzleclient;
        return $client;
    }


    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function getPassword()
    {
        return null;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->eppn;
    }

    public function eraseCredentials()
    {
    }

    public function loadUserByUsername($username)
    {
        return $this;
    }

    public function refreshUser(UserInterface $user)
    {
        return $this;
    }

    public function supportsClass($class)
    {
        return $class === 'AppBundle\\Security\\User\\ShibbolethUser';
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->eppn,
            // $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
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
     */
    public function getToken():string
    {
        if ($this->session->has('token')) {
            $now = new \DateTime();
            $diff = $now->diff($this->session->get('tokenAcquiredAt'), true);
            if ($diff->h == 0 && $diff->d == 0 && $diff->m == 0 && $diff->y == 0) {
                return $this->session->get('token');
            } else {
                $this->requestNewToken();
            }
        } else {
            $this->requestNewToken();
        }
        return $this->session->get('token') ?? '';
    }

    private function requestNewToken()
    {
        $client   = $this->guzzleclient;
        
        // Create api key
        $time = new \DateTime();
        date_timezone_set($time, new \DateTimeZone('UTC'));
        $stamp = $time->format('Y-m-d H:i');
        $apiKey = hash('sha256', $this->hexaaScopedKey . $stamp);
            $response = $client->post('tokens', [
                'json' => [
                    'fedid' => $this->getEppn(),
                    'email' => $this->getEmail(),
                    'display_name' => $this->getDisplayName(),
                    'apikey' => $apiKey
                ]
            ]);
            $this->session->set('token', json_decode($response->getBody(), true)['token']);
            $this->session->set('tokenAcquiredAt', $time);
    }
}
