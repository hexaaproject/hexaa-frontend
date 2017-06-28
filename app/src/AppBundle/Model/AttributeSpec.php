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
}
