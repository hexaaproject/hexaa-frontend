<?php
/**
 * Created by PhpStorm.
 * User: solazs
 * Date: 2017.02.23.
 * Time: 15:45
 */

namespace AppBundle\Model;

/**
 * Class AttributeValuePrincipal
 * @package AppBundle\Model
 */
class AttributeValuePrincipal extends AbstractBaseResource
{
    protected $pathName = 'attributevalueprincipals';

    /**
     * GET services linked to attribute value
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of service
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
     */
    public function getServicesLinkedToAttributeValue(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 1000)
    {
        return $this->getCollection(
            $this->pathName.'/'.$id.'/services',
            $hexaaAdmin,
            $verbose,
            $offset,
            $pageSize
        );
    }

    /**
     * Create attribute value for principal
     * @param string $hexaaAdmin  Admin hat
     * @param array  $services
     * @param string $value
     * @param int    $attrspecid
     * @param int    $principalid
     * @return ResponseInterface
     */
    public function postAttributeValue(string $hexaaAdmin, array $services, string $value, int $attrspecid, int $principalid)
    {
        $attributevalue = array();
        $attributevalue["value"] = $value;
        $attributevalue["services"] = $services;
        $attributevalue["attribute_spec"] = $attrspecid;
       // $attributevalue["principal"] = $principalid;
        $response = $this->postCall($this->pathName, $attributevalue, $hexaaAdmin);

        return $response;
    }
}
