<?php
namespace AppBundle\Model;

use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class Organization extends BaseResource
{
    protected $pathName = 'organizations';

    /**
     * GET managers of Organization
     *
     * @param string $id       ID of organization
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getManagers(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25): array
    {
        return $this->getCollection($this->pathName.'/'.$id.'/managers', $verbose, $offset, $pageSize);
    }

    /**
     * GET members of Organization
     *
     * @param string $id       ID of organization
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getMembers(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25): array
    {
        return $this->getCollection($this->pathName.'/'.$id.'/members', $verbose, $offset, $pageSize);
    }

    /**
     * GET roles of Organization
     *
     * @param string $id       ID of organization
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getRoles(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25): array
    {
        return $this->getCollection($this->pathName.'/'.$id.'/roles', $verbose, $offset, $pageSize);
    }

    /**
     * GET entitlements of Organization
     *
     * @param string $id       ID of organization
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getEntitlements(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25): array
    {
        return $this->getCollection($this->pathName.'/'.$id.'/entitlements', $verbose, $offset, $pageSize);
    }


    /**
     * GET entitlement packs of Organization
     *
     * @param string $id       ID of organization
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getEntitlementPacks(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25): array
    {
        return $this->getCollection($this->pathName.'/'.$id.'/entitlementpacks', $verbose, $offset, $pageSize);
    }
}