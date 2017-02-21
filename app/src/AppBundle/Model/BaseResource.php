<?php
namespace AppBundle\Model;

use GuzzleHttp\Client;

abstract class BaseResource
{
    protected static $pathName;
    protected static $client;
    protected static $token;


    public static function cget(int $offset = 0, int $pageSize = 25) {
        $response = static::$client->get(
            static::$pathName,
            array(
                'headers' => self::getHeaders(),
                'query' => array(
                    'offset' => $offset,
                    'limit' => $pageSize
                    )
                )
            );    
        return json_decode($response->getBody(), true);
    }
    
    public static function get(string $id) {
        $response = static::$client->get(
            static::$pathName.'/'.$id,
            array('headers' => self::getHeaders())
        );
        return json_decode($response->getBody(), true);
    }
    
    public static function rget(string $id, string $verbose = "normal") {
        $response = static::$client->get(static::$pathName.'/'.$id.'/'.'roles', [
            'headers' => self::getHeaders(),
            'query' => [
                'verbose' => $verbose          
        ]]);
        return json_decode($response->getBody(), true);
    }
  
    public static function serviceattributesget(string $id) {
        $response = static::$client->get(
            static::$pathName.'/'.$id.'/'.'attributespecs',
            array(
                'headers' => self::getHeaders(),
                )
            );
        return json_decode($response->getBody(), true);
    }
    
    public static function attributespecsget(string $verbose = "normal") {
        $response = static::$client->get('attributespecs', [
            'headers' => self::getHeaders(),
            'query' => [
                'verbose' => $verbose          
        ]]);
        return json_decode($response->getBody(), true);
    }
   
    public static function membersget(string $id) {
        $response = static::$client->get(
            static::$pathName.'/'.$id.'/'.'members',
            array(
                'headers' => self::getHeaders(),
                )
            );
        return json_decode($response->getBody(), true);
    }
    
    public static function principalinfo() {
       $response = static::$client->get(
            static::$pathName.'/'.'self',
            array(
                'headers' => self::getHeaders(),
                )
            );
       return json_decode($response->getBody(), true);
    }
    
    public static function attributeget(string $verbose = "normal") {
        $response = static::$client->get(static::$pathName.'/'.'attributevalueprincipal', [
            'headers' => self::getHeaders(),
            'query' => [
                'verbose' => $verbose          
        ]]);
        return json_decode($response->getBody(), true);
    }
    
    public static function entitlementsget(string $id, string $verbose = "normal") {
        $response = static::$client->get(static::$pathName.'/'.$id.'/'.'entitlements', [
            'headers' => self::getHeaders(),
            'query' => [
                'verbose' => $verbose          
        ]]);
        return json_decode($response->getBody(), true);
    }
    
    public static function entitlementpacksget(string $id, string $verbose = "normal") {
        $response = static::$client->get(static::$pathName.'/'.$id.'/'.'entitlementpacks', [
            'headers' => self::getHeaders(),
            'query' => [
                'verbose' => $verbose          
        ]]);
        return json_decode($response->getBody(), true);
    }


    public function getHeaders()
    {
        $config = static::$client->getConfig();
        $headers = $config["headers"];
        $headers['X-HEXAA-AUTH'] = static::$token;
        
        return $headers;
    }
}
