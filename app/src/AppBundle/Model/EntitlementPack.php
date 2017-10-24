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
        return $this->getCollection(
            $this->pathName.'/public',
            $verbose,
            $offset,
            $pageSize
        );
    }

    /**
     * @param string $id
     * @param string $verbose
     * @param int    $offset
     * @param int    $pageSize
     * @return array
     */
    public function getEntitlementsOfEntitlementpack(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection(
            $this->pathName.'/'.$id.'/entitlements',
            $verbose,
            $offset,
            $pageSize
        );
    }

    /**
     * @param string $id
     * @param string $verbose
     * @param int    $offset
     * @param int    $pageSize
     * @return array
     */
    public function getEntitlementsDetails(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection(
            $this->pathName.'/'.$id,
            $verbose,
            $offset,
            $pageSize
        );
    }

    /**
     * Add permission to permissionset
     * @param string $id     of entitlementpack
     * @param string $permid of entitlement
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function addPermissionToPermissionSet(string $id, string $permid)
    {
        return $this->putCall($this->pathName.'/'.$id.'/entitlements'.'/'.$permid, []);
    }

    /**
     *DELETE permission set
     *
     * @param  string $id ID of permission
     * @return response
     */
    public function deletePermissionSet(string $id)
    {
        $path = $this->pathName.'/'.$id;

        $response = $this->client->delete(
            $path,
            [
                'headers' => $this->getHeaders(),
            ]
        );

        return $response;
    }
}
