<?php
/**
 * Created by PhpStorm.
 * User: baloo
 * Date: 2016.11.23.
 * Time: 10:39
 */

namespace Hexaa\Newui\Model;


use GuzzleHttp\Client;

abstract class BaseResource
{
    protected static $pathName;
    public static function cget(Client $client) {
        $response = $client->get(self::$pathName);
        return json_decode($response->getBody(), true);
    }
}