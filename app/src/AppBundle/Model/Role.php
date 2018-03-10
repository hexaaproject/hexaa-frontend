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
     * GET principals of Role
     *
     * @param string $id       ID of role
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getPrincipals(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection($this->pathName.'/'.$id.'/principals', $verbose, $offset, $pageSize);
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
     * @param array  $entitlements
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function setEntitlements(string $id, array $entitlements)
    {
        return $this->putCall($this->pathName.'/'.$id.'/entitlement', $entitlements);
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

    /**
     * @param string $id
     * @param string $principalId
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deletePrincipal(string $id, string $principalId)
    {
        return $this->deleteCall($this->pathName.'/'.$id.'/principals/'.$principalId, []);
    }

    /**
     * @param string $id
     * @param array  $principals
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function setPrincipals(string $id, array $principals)
    {
        return $this->putCall($this->pathName.'/'.$id.'/principal', $principals);
    }

    /**
     * @param string $id
     *
     * @return ArrayCollection
     */
    public function getWarnings($id)
    {
        $warnings = new ArrayCollection();

        $entitlements = $this->getEntitlements($id);
        if (0 == $entitlements['item_number']) {
            $role = $this->get($id);
            $warning = new PermissionLessRoleWarning($role['name']);
            $warnings->add($warning);
        }

        $principals = $this->getPrincipals($id);
        if (0 == $principals['item_number']) {
            $role = $this->get($id);
            $warning = new MemberLessRoleWarning($role['name']);
            $warnings->add($warning);
        }

        return $warnings;
    }
}
