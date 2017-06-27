<?php
namespace AppBundle\Model;

/**
 * Class AttributeSpec
 * @package AppBundle\Model
 */
class AttributeSpec extends AbstractBaseResource
{
    protected $pathName = 'attributespecs';
    
    public function getAttributeSpec(string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection($this->pathName,
                $verbose, $offset, $pageSize);
    }
}
