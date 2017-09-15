<?php
namespace AppBundle\Model;

use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class Principal
 * @package AppBundle\Model
 */
class Principals extends AbstractBaseResource
{
    protected $pathName = 'principals';

    /**
     * @param string $id principal ID
     * @return array
     */
    public function getById($id)
    {
        $verbose = "normal";

        return $this->getSingular($this->pathName.'/'.$id.'/id', $verbose);
    }
}
