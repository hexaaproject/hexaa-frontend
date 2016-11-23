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
    public static function cget(Client $client, int $offset = 0, int $pageSize = 25) {
        $response = $client->get(static::$pathName,[
            'query' => [
                'offset' => $offset,
                'limit' => $pageSize
        ]]);
        return json_decode($response->getBody(), true);
    }
}