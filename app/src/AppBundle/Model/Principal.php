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
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @param bool   $admin
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
        if($admin == "1"){
            $admin = "true";
        }
        $id= (int)($pid);
        $path = 'principals/'.$id.'/id';

        $response = $this->client->delete(
            $path,
            [
                'headers' => $this->getHeaders(),
                'query' => array(
                    'admin' => $admin
                ),
            ]
        );

        return $response;
    }


    /**
     * Principal is admin or not?
     *
     * @param string $verbose    One of minimal, normal or expanded
     * @param string $hexaatoken hexaa api token
     * @return array
     */
    public function isAdmin(string $verbose = "normal")
    {
        return $this->getSingular($this->pathName.'/isadmin', $verbose);
    }

}
