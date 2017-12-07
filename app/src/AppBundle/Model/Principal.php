<?php
namespace AppBundle\Model;

use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class Principal
 * @package AppBundle\Model
 */
class Principal extends AbstractBaseResource
{
    protected $pathName = 'principal';

    /**
     * GET the current Principal
     *
     * @param string $verbose    One of minimal, normal or expanded
     * @param string $hexaatoken hexaa api token
     * @return array
     */
    public function getSelf(string $verbose = "normal", $hexaatoken = null)
    {
        if ($hexaatoken) {
            $this->token = $hexaatoken;
        }

        return $this->getSingular($this->pathName.'/self', $verbose);
    }

    /**
     * GET info about Principal
     *
     * @param string $id      Id of principal
     * @param string $verbose One of minimal, normal or expanded
     * @return array
     */
    public function getPrincipalInfo(string $id, string $verbose = "normal")
    {
        return $this->getSingular('principals'.'/'.$id.'/id', $verbose);
    }


    /**
     * GET attribute values of the current Principal
     *
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getAttributeValues(string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection($this->pathName.'/attributevalueprincipal', $verbose, $offset, $pageSize);
    }

    /**
     * GET All principals
     *
     * @param string $admin
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getAllPrincipals(string $admin = "true", string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollectionAdmin('principals', $admin, $verbose, $offset, $pageSize);
    }

    /**
     * Delete principal
     *
     * @param string $admin
     * @param string $pid
     * @return array
     */
    public function deletePrincipal(string $admin, string $pid)
    {
        if ($admin == "1") {
            $admin = "true";
        }
        $id = (int) ($pid);
        $path = 'principals/'.$id.'/id';

        $response = $this->client->delete(
            $path,
            [
                'headers' => $this->getHeaders(),
                'query' => array(
                    'admin' => $admin,
                ),
            ]
        );

        return $response;
    }

    /**
     * Edit principal properties
     *
     * @param string $admin
     * @param string $pid
     * @param array  $data
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function editPrincipal(string $admin, string $pid, array $data)
    {
        $response = null;
        $id = (int) ($pid);
        $path = 'principals/'.$id;

       /* if ($admin == "1") {
            $admin = "true";
            $response = $this->putCallAdmin($path, $data, $admin);
        } else {
            $response = $this->putCall($path, $data);
        }*/

        $response = $this->patchCall($path, $data);

        return $response;
    }

    /**
     * Principal is admin or not?
     *
     * @param string $verbose One of minimal, normal or expanded
     * @return array
     */
    public function isAdmin(string $verbose = "normal")
    {
        return $this->getSingular($this->pathName.'/isadmin', $verbose);
    }

    /**
     * Get the history of the principal
     * @param string $verbose
     * @param int    $offset
     * @param int    $pageSize
     * @return array
     */
    public function getHistory(string $verbose = "normal", int $offset = 0, int $pageSize = 500)
    {
        //$id = (int) ($pid);
        return $this->getCollection($this->pathName.'/news', $verbose, $offset, $pageSize);
    }

    /**
    * List organizations where user is manager
    *
    * @param string $verbose One of minimal, normal or expanded
    * @return bool
    */
    public function orgsWhereUserIsManager(string $verbose = "normal")
    {
        return $this->getSingular('manager/organizations', $verbose);
    }

    /**
    * List organizations where user is manager
    *
    * @param string $verbose One of minimal, normal or expanded
    * @return bool
    */
    public function servsWhereUserIsManager(string $verbose = "normal")
    {
        return $this->getSingular('manager/services', $verbose);
    }

    /**
    * Get the history of the principal
    * @param string $verbose
    * @return array
    */
    public function getEntitlements(string $verbose = "normal")
    {
        return $this->getCollection($this->pathName.'/entitlements', $verbose);
    }
}
