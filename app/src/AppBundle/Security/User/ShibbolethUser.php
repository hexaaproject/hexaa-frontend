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
    private $eppn;
    private $displayName;
    private $email;

    private $client;
    private $token = null;
    private $tokenAcquiredAt;
    private $hexaaScopedKey;

    public function __construct($shibAttributeMap, $hexaaScopedKey, $base_uri)
    {
        // dump($shibAttributeMap);
        foreach (array('eppn', 'displayName', 'email') as $key) {
            if (array_key_exists($shibAttributeMap[$key], $_SERVER)) {        
                $this->$key = $_SERVER[$shibAttributeMap[$key]];
            }
        }
        $this->base_uri=$base_uri;
        $this->hexaaScopedKey=$hexaaScopedKey;
        $this->client=new Client(array('base_uri' => $base_uri, 'headers' => array('X-HEXAA-AUTH' => $this->getToken())));
        // dump($this->client); exit;

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
        // dump($this->getToken());exit;

        return $this->client;
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


    private function getToken():string
    {
        if ($this->token !== null) {
            $now = new \DateTime();
            $diff = $now->diff($this->tokenAcquiredAt, true);
            if ($diff->h == 0 && $diff->d == 0 && $diff->m == 0 && $diff->y == 0) {
                return $this->token;
            } else {
                $this->requestNewToken();
            }
        } else {
            $this->requestNewToken();
        }
        return $this->token ?? '';
    }

    private function requestNewToken()
    {
        $client = new Client(array('base_uri' => $this->base_uri));
        // Create api key
        $time = new \DateTime();
        date_timezone_set($time, new \DateTimeZone('UTC'));
        $stamp = $time->format('Y-m-d H:i');
        $apiKey = hash('sha256', $this->hexaaScopedKey . $stamp);
        try {
            $response = $client->post('tokens', [
                'json' => [
                    'fedid' => $this->getEppn(),
                    'email' => $this->getEmail(),
                    'display_name' => $this->getDisplayName(),
                    'apikey' => $apiKey
                ]
            ]);
            $this->token = json_decode($response->getBody(), true)['token'];
        } catch (ClientException $e) {
            $this->token = null;
            // TODO: pretty error handling
            echo('<br>___.--===(ClientException)===--.___<br>');
            echo('Message: ' . $e->getMessage() . '<br>');
            echo('Call: ' . $e->getRequest()->getUri() . '<br>');
            echo('Request method: ' . $e->getRequest()->getMethod() . ', body: <br>');
            echo($e->getRequest()->getBody() . '<br>');
            echo('Response code: ' . $e->getResponse()->getStatusCode() . ', body: <br>');
            echo($e->getResponse()->getBody() . '<br>');
        } catch (ServerException $e) {
            $this->token = null;
            // TODO: pretty error handling
            echo('<br>___.--===(ServerException)===--.___<br>');
            echo('Message: ' . $e->getMessage() . '<br>');
            echo('Call: ' . $e->getRequest()->getUri() . '<br>');
            echo('Request method: ' . $e->getRequest()->getMethod() . ', body: <br>');
            echo($e->getRequest()->getBody() . '<br>');
            echo('Response code: ' . $e->getResponse()->getStatusCode() . ', body: <br>');
            echo($e->getResponse()->getBody() . '<br>');
        }
    }
}
