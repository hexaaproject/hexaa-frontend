<?php
/**
 * Created by PhpStorm.
 * User: solazs
 * Date: 2017.03.13.
 * Time: 13:49
 */

namespace AppBundle\Model;


class EntitlementPack extends BaseResource
{
    protected $pathName = 'entitlementpacks';

    public function getPublic(string $verbose = 'normal', int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection($this->pathName.'/public', $verbose, $offset, $pageSize);
    }
}