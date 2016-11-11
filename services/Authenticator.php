<?php

/**
 * Authenticator class for HEXAA.
 *
 * User: solazs
 * Date: 2016.11.11.
 * Time: 15:29
 */
class Authenticator
{
    private $scopedKey;
    private $token = null;
    private $tokenAcquiredAt;
    private $user;
    private $client;

    public function __construct(array $config, Principal $user)
    {
        $this->scopedKey = $config['scopedKey'];
        $this->user = $user;
        if (array_key_exists('token_set_at', $_SESSION) && isset($_SESSION['token_set_at'])
            && array_key_exists('token', $_SESSION) && isset($_SESSION['token'])
        ) {
            $this->tokenAcquiredAt = new DateTime($_SESSION['token_set_at']);
            $this->token = $_SESSION['token'];
        }
        $this->client = new GuzzleHttp\Client([
            'base_uri' => $config['backendUrl'],
        ]);
    }

    public function getToken():string
    {
        if ($this->token !== null) {
            $now = new DateTime();
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
        // Create api key
        $time = new \DateTime();
        date_timezone_set($time, new \DateTimeZone('UTC'));
        $stamp = $time->format('Y-m-d H:i');
        $apiKey = hash('sha256', $this->scopedKey . $stamp);
        try {
            $response = $this->client->post('tokens', [
                'json' => [
                    'fedid' => $this->user->getEppn(),
                    'email' => $this->user->getEmail(),
                    'display_name' => $this->user->getDisplayName(),
                    'apikey' => $apiKey
                ]
            ]);
            $this->token = json_decode($response->getBody(), true)['token'];
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->token = null;
            // TODO: pretty error handling
            echo('<br>___.--===(ClientException)===--.___<br>');
            echo('Message: ' . $e->getMessage() . '<br>');
            echo('Call: ' . $e->getRequest()->getUri() . '<br>');
            echo('Request method: ' . $e->getRequest()->getMethod() . ', body: <br>');
            echo($e->getRequest()->getBody() . '<br>');
            echo('Response code: ' . $e->getResponse()->getStatusCode() . ', body: <br>');
            echo($e->getResponse()->getBody() . '<br>');
        } catch (\GuzzleHttp\Exception\ServerException $e) {
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