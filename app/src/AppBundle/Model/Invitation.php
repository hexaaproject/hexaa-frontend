<?php
namespace AppBundle\Model;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class Invitation extends BaseResource
{
    protected $pathName = 'invitations';

    /**
     * POST send new invitation
     *
     */
    public function sendInvitation($data)
    {
        return $this->post($data);
    }

}