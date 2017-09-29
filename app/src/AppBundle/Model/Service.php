<?php

namespace AppBundle\Model;

use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class Service
 * @package AppBundle\Model
 */
class Service extends AbstractBaseResource
{
    protected $pathName = 'services';

    /**
     * GET attribute specifications of Service
     *
     * @param string $id       ID of service
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getAttributeSpecs(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection(
            $this->pathName.'/'.$id.'/attributespecs',
            $verbose,
            $offset,
            $pageSize
        );
    }

    /**
     * GET all services
     *
     * @param string $admin    Admin call to get all services
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getAll(string $admin = "true", string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollectionAdmin(
            $this->pathName,
            $admin,
            $verbose,
            $offset,
            $pageSize
        );
    }

    /**
     * GET managers of Service
     *
     * @param string $id       ID of service
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getManagers(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection(
            $this->pathName.'/'.$id.'/managers',
            $verbose,
            $offset,
            $pageSize
        );
    }

    /**
     * GET entitlements of Service
     *
     * @param string $id       ID of service
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getEntitlements(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection(
            $this->pathName.'/'.$id.'/entitlements',
            $verbose,
            $offset,
            $pageSize
        );
    }

    /**
     * GET entitlement packs of Service
     *
     * @param string $id       ID of service
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getEntitlementPacks(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection(
            $this->pathName.'/'.$id.'/entitlementpacks',
            $verbose,
            $offset,
            $pageSize
        );
    }

    /**
     * GET organizations link to Service
     *
     * @param string $id       ID of service
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getOrganizations(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection(
            $this->pathName.'/'.$id.'/organizations',
            $verbose,
            $offset,
            $pageSize
        );
    }

    /**
     * GET link requests to Service
     *
     * @param string $id       ID of service
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getLinkRequests(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection(
            $this->pathName.'/'.$id.'/link'.'/requests',
            $verbose,
            $offset,
            $pageSize
        );
    }


    /**
     *DELETE managers of Service
     *
     * @param string $id  ID of service
     * @param string $pid ID of principal
     * @return response
     */
    public function deleteMember(string $id, string $pid)
    {
        $path = $this->pathName.'/'.$id.'/managers/'.$pid;

        $response = $this->client->delete(
            $path,
            [
            'headers' => $this->getHeaders(),
            ]
        );

        return $response;
    }

    /**
     *DELETE attribute specifications of Service
     *
     * @param string $id   ID of service
     * @param string $asid ID of attribute specification
     * @return response
     */
    public function deleteAttributeSpec(string $id, string $asid)
    {
        $path = $this->pathName.'/'.$id.'/attributespecs/'.$asid;

        $response = $this->client->delete(
            $path,
            [
            'headers' => $this->getHeaders(),
            ]
        );

        return $response;
    }

    /**
     *Add attribute specification to Service
     *
     * @param string $id       ID of service
     * @param string $asid     ID of attribute specification
     * @param bool   $ispublic Attribute specification is public or not
     * @return response
     */
    public function addAttributeSpec(string $id, string $asid, bool $ispublic = true)
    {

        $path = $this->pathName.'/'.$id.'/attributespecs/'.$asid;

        $response = $this->putCall(
            $path,
            [
            'is_public' => $ispublic,
            ]
        );

        return $response;
    }

    /**
     * Create new Service
     *
     * @param string      $name
     * @param string|null $description
     * @param string|null $uri
     * @param string      $entityid
     * @return array expanded organization
     */
    public function create(string $name, string $description = null, string $uri = null, string $entityid)
    {
        $serviceData = array();
        $serviceData['name'] = $name;
        if ($description) {
            $serviceData['description'] = $description;
        }
        if ($uri) {
            $serviceData['uri'] = $uri;
        }
        $serviceData['entityid'] = $entityid;
        $response = $this->post($serviceData);
        $locations = $response->getHeader('Location');
        $location = $locations[0];
        $serviceId = preg_replace('#.*/#', '', $location);

        return $this->get($serviceId, "expanded");
    }

    /**
     * Create new permission
     *
     * @param string      $prefix
     * @param string      $id
     * @param string      $name
     * @param string      $description
     * @param Entitlement $entitlement
     * @return ResponseInterface
     */
    public function createPermission(string $prefix, string $id, string $name, string $description = null, Entitlement $entitlement)
    {
        $withoutAccent = $this->removeAccents($name);
        //$withoutSpace = preg_replace('/\s+/', '', $withoutAccent);
        $modifiedName = preg_replace("/[^a-zA-Z0-9]+/", "", $withoutAccent);
        $response = $this->postCall($this->pathName.'/'.$id.'/entitlements', array("uri" => $prefix.":".$id.":".$modifiedName, "name" => $name, "description" => $description));
        $locations = $response->getHeader('Location');
        $location = $locations[0];
        //$name = preg_replace('', '', $name);
        $id = preg_replace('#.*/#', '', $location);

        return $entitlement->get($id, "expanded");
    }


    /**
     * Create new permissionset
     *
     * @param string          $id
     * @param string          $name
     * @param EntitlementPack $entitlementpack
     * @return ResponseInterface
     */
    public function createPermissionSet(string $id, string $name, EntitlementPack $entitlementpack)
    {
        $response = $this->postCall($this->pathName.'/'.$id.'/entitlementpacks', array("name" => $name, "type" => "public"));
        $locations = $response->getHeader('Location');
        $location = $locations[0];
        $id = preg_replace('#.*/#', '', $location);

        return $entitlementpack->get($id, "expanded");
    }

    /**
     * @param string $id
     * @param string $principalId
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putManager(string $id, string $principalId)
    {
        return $this->putCall($this->pathName.'/'.$id.'/managers/'.$principalId, []);
    }

    /**
     * Get the history of the service
     * @param string      $id
     * @param string      $verbose
     * @param int         $offset
     * @param int         $pageSize
     * @param string|null $tags
     * @return array
     */
    public function getHistory(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 500, string $tags = null)
    {
        return $this->getCollection($this->pathName.'/'.$id.'/news', $verbose, $offset, $pageSize, $tags);
    }

    /**
     * @param string $path
     * @param string $admin
     * @param string $verbose
     * @param int  $offset
     * @param int  $pageSize
     * @return array
     */
    protected function getCollectionAdmin(string $path, string $admin = "true", string $verbose = "normal", int $offset = 0, int $pageSize = 25): array
    {
        $response = $this->client->get(
            $path,
            array(
                'headers' => $this->getHeaders(),
                'query' => array(
                    'verbose' => $verbose,
                    'offset' => $offset,
                    'limit' => $pageSize,
                    'admin' => $admin,
                ),
            )
        );

        return json_decode($response->getBody(), true);
    }

    /**
     * Replace accents
     *
     * @param string  $string
     * @return string
     */
    private function removeAccents($string)
    {
        if (!preg_match('/[\x80-\xff]/', $string)) {
            return $string;
        }

        $chars = array(
            chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
            chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
            chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
            chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
            chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
            chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
            chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
            chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
            chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
            chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
            chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
            chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
            chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
            chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
            chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
            chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
            chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
            chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
            chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
            chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
            chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
            chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
            chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
            chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
            chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
            chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
            chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
            chr(195).chr(191) => 'y',
            chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
            chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
            chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
            chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
            chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
            chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
            chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
            chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
            chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
            chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
            chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
            chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
            chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
            chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
            chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
            chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
            chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
            chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
            chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
            chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
            chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
            chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
            chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
            chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
            chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
            chr(196).chr(178) => 'IJ', chr(196).chr(179) => 'ij',
            chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
            chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
            chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
            chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
            chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
            chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
            chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
            chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
            chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
            chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
            chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
            chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
            chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
            chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
            chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
            chr(197).chr(146) => 'OE', chr(197).chr(147) => 'oe',
            chr(197).chr(148) => 'R', chr(197).chr(149) => 'r',
            chr(197).chr(150) => 'R', chr(197).chr(151) => 'r',
            chr(197).chr(152) => 'R', chr(197).chr(153) => 'r',
            chr(197).chr(154) => 'S', chr(197).chr(155) => 's',
            chr(197).chr(156) => 'S', chr(197).chr(157) => 's',
            chr(197).chr(158) => 'S', chr(197).chr(159) => 's',
            chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
            chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
            chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
            chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
            chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
            chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
            chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
            chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
            chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
            chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
            chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
            chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
            chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
            chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
            chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
            chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
        );

        $string = strtr($string, $chars);

        return $string;
    }
}
