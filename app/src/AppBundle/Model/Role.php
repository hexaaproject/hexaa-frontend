<?php
namespace AppBundle\Model;

use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class Role
 * @package AppBundle\Model
 */
class Role extends AbstractBaseResource
{
    protected $pathName = 'roles';


    /**
     * GET entitlements of Role
     *
     * @param string $id       ID of role
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getEntitlements(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection($this->pathName.'/'.$id.'/entitlements', $verbose, $offset, $pageSize);
    }

    /**
     * @param string $id
     * @param string $entitlementId
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putEntitlements(string $id, string $entitlementId)
    {
        return $this->putCall($this->pathName.'/'.$id.'/entitlements/'.$entitlementId, []);
    }

    /**
     * @param string $id
     * @param string $principalId
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putPrincipal(string $id, string $principalId)
    {
        return $this->putCall($this->pathName.'/'.$id.'/principals/'.$principalId, []);
    }
}
