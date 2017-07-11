<?php
namespace AppBundle\Model;

/**
 * Class Link
 * @package AppBundle\Model
 */
class Link extends AbstractBaseResource
{
    protected $pathName = 'links';

    /**
     * Generate new link token
     *
     * @param string $id ID of link
     * @return array
     */
    public function getNewLinkToken(string $id): array
    {
        return $this->getCollection($this->pathName.'/'.$id.'/token');
    }
}
