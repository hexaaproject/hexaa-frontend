<?php
/**
 * Created by PhpStorm.
 * User: baloo
 * Date: 2016.11.23.
 * Time: 10:32
 */

namespace Hexaa\Newui\Model;


class Organization extends BaseResource
{
    public function __construct()
    {
        self::$pathName = 'organizations';
    }
}