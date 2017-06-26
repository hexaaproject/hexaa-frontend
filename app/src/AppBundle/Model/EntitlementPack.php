<?php
/**
 * Created by PhpStorm.
 * User: solazs
 * Date: 2017.03.13.
 * Time: 13:49
 */

namespace AppBundle\Model;

/**
 * Class EntitlementPack
 * @package AppBundle\Model
 */
class EntitlementPack extends AbstractBaseResource
{
    protected $pathName = 'entitlementpacks';

    /**
     * @param string $verbose
     * @param int    $offset
     * @param int    $pageSize
     * @return array
     */
    public function getPublic(string $verbose = 'normal', int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection($this->pathName.'/public', $verbose, $offset, $pageSize);
    }
    
    public function getEntitlementsOfEntitlementpack(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection($this->pathName.'/'.$id.'/entitlements',
                $verbose, $offset, $pageSize);
    }
}
