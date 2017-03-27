<?php
namespace AppBundle\Model;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class Invitation
 * @package AppBundle\Model
 */
class Invitation extends AbstractBaseResource
{
    protected $pathName = 'invitations';

    /**
     * POST send new invitation
     * @param string $data data
     * @return ResponseInterface
     */
    public function sendInvitation($data)
    {
        return $this->post($data);
    }

    /**
     * Accept the invitation of the token
     * @param string $token
     * @return array
     */
    public function accept(string $token)
    {
        $id = $token."/accept/token";

        return $this->get($id);
    }
}
