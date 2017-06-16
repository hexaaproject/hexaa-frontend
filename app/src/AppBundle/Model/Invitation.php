<?php
namespace AppBundle\Model;

use Psr\Http\Message\ResponseInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

    /**
     * Create invitation in hexaa
     *
     * @param int         $organizationId
     * @param Router      $router
     * @param int|null    $roleId
     * @param string|null $landingUrl
     * @return string invitationAcceptLink
     */
    public function createHexaaInvitation(int $organizationId, Router $router, int $roleId = null, string $landingUrl = null)
    {
        $data['organization'] = $organizationId;
        $data['role'] = $roleId;
        $invite = $this->sendInvitation($data);

        $headers = $invite->getHeaders();

        $invitationId = basename(parse_url($headers['Location'][0], PHP_URL_PATH));
        $invitation = $this->get($invitationId);

        if (!empty($landingUrl)) {
            $landingUrl = urlencode($landingUrl);
        }
        $inviteLink = $router->generate(
            'app_organization_resolveinvitationtoken',
            array(
                "token" => $invitation['token'],
                "organizationid" => $organizationId,
                "landing_url" => $landingUrl,
            ),
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $inviteLink;
    }
}
