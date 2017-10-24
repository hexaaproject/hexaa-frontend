<?php
namespace AppBundle\Model;

use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class Entitlement
 * @package AppBundle\Model
 */
class Entitlement extends AbstractBaseResource
{
    protected $pathName = 'entitlements';

    /**
     * @param string $id
     * @param string $verbose
     * @param int    $offset
     * @param int    $pageSize
     * @return array
     */
    public function getEntitlement(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection(
            $this->pathName.'/'.$id,
            $verbose,
            $offset,
            $pageSize
        );
    }

    /**
     *DELETE permission
     *
     * @param  string $id ID of permission
     * @return response
     */
    public function deletePermission(string $id)
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
