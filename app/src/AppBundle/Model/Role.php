<?php
namespace AppBundle\Model;

use AppBundle\Tools\Warning\MemberLessRoleWarning;
use AppBundle\Tools\Warning\PermissionLessRoleWarning;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of role
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
     */
    public function getEntitlements(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection($this->pathName.'/'.$id.'/entitlements', $hexaaAdmin, $verbose, $offset, $pageSize);
    }

    /**
     * GET principals of Role
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of role
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
     */
    public function getPrincipals(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection($this->pathName.'/'.$id.'/principals', $hexaaAdmin, $verbose, $offset, $pageSize);
    }

    /**
     * @param string $hexaaAdmin    Admin hat
     * @param string $id
     * @param string $entitlementId
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putEntitlements(string $hexaaAdmin, string $id, string $entitlementId)
    {
        return $this->putCall($this->pathName.'/'.$id.'/entitlements/'.$entitlementId, [], $hexaaAdmin);
    }

    /**
     * @param string $hexaaAdmin   Admin hat
     * @param string $id
     * @param array  $entitlements
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function setEntitlements(string $hexaaAdmin, string $id, array $entitlements)
    {
        return $this->putCall($this->pathName.'/'.$id.'/entitlement', $entitlements, $hexaaAdmin);
    }

    /**
     * @param string $hexaaAdmin  Admin hat
     * @param string $id
     * @param string $principalId
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putPrincipal(string $hexaaAdmin, string $id, string $principalId)
    {
        return $this->putCall($this->pathName.'/'.$id.'/principals/'.$principalId, [], $hexaaAdmin);
    }

    /**
     * @param string $hexaaAdmin  Admin hat
     * @param string $id
     * @param string $principalId
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deletePrincipal(string $hexaaAdmin, string $id, string $principalId)
    {
        return $this->deleteCall($this->pathName.'/'.$id.'/principals/'.$principalId, $hexaaAdmin);
    }

    /**
     * @param string $hexaaAdmin Admin hat
     * @param string $id
     * @param array  $principals
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function setPrincipals(string $hexaaAdmin, string $id, array $principals)
    {
        return $this->putCall($this->pathName.'/'.$id.'/principal', $principals, $hexaaAdmin);
    }

    /**
     * @param string $hexaaAdmin Admin hat
     * @param string $id
     *
     * @return ArrayCollection
     */
    public function getWarnings(string $hexaaAdmin, $id)
    {
        $warnings = new ArrayCollection();

        $entitlements = $this->getEntitlements($hexaaAdmin, $id);
        if (0 == $entitlements['item_number']) {
            $role = $this->get($hexaaAdmin, $id);
            $warning = new PermissionLessRoleWarning($role['name']);
            $warnings->add($warning);
        }

        $principals = $this->getPrincipals($hexaaAdmin, $id);
        if (0 == $principals['item_number']) {
            $role = $this->get($hexaaAdmin, $id);
            $warning = new MemberLessRoleWarning($role['name']);
            $warnings->add($warning);
        }

        return $warnings;
    }
}
