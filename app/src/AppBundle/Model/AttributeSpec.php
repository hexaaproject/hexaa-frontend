<?php
namespace AppBundle\Model;

/**
 * Class AttributeSpec
 * @package AppBundle\Model
 */
class AttributeSpec extends AbstractBaseResource
{
    protected $pathName = 'attributespecs';
    /**
     *GET attribute specifications
     *
     * @param string $verbose  verbose
     * @param int    $offset   offset
     * @param int    $pageSize pagesize
     * @return array
     */
    public function getAttributeSpec(string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection(
            $this->pathName,
            $verbose,
            $offset,
            $pageSize
        );
    }

    /**
     *Create attribute specification
     *
     * @param string $admin
     * @param array  $attributeSpec
     * @param string $verbose       verbose
     * @param int    $offset        offset
     * @param int    $pageSize      pagesize
     * @return response
     */
    public function createAttributeSpec(string $admin, array $attributeSpec, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        $response = $this->postCallAdmin($this->pathName, $admin, $attributeSpec);

        return $response;
    }
}
