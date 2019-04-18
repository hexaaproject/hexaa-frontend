<?php
/**
 * Copyright 2016-2018 MTA SZTAKI ugyeletes@sztaki.hu
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

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
     * @param string $hexaaAdmin Admin hat
     * @param string $data       data
     * @return ResponseInterface
     */
    public function sendInvitation(string $hexaaAdmin, $data)
    {
        return $this->post($hexaaAdmin, $data);
    }

    /**
     * Accept the invitation of the token
     * @param string $hexaaAdmin Admin hat
     * @param string $token
     * @return array
     */
    public function accept(string $hexaaAdmin, string $token)
    {
        $id = $token."/accept/token";

        return $this->get($hexaaAdmin, $id);
    }

    /**
     * Create invitation in hexaa
     *
     * @param string      $hexaaAdmin     Admin hat
     * @param int         $organizationId
     * @param Router      $router
     * @param int|null    $roleId
     * @param string|null $landingUrl
     * @return string invitationAcceptLink
     */
    public function createHexaaInvitation(string $hexaaAdmin, int $organizationId, Router $router, int $roleId = null, string $landingUrl = null)
    {
        $data['organization'] = $organizationId;
        $data['role'] = $roleId;
        $invite = $this->sendInvitation($hexaaAdmin, $data);

        $headers = $invite->getHeaders();

        $invitationId = basename(parse_url($headers['Location'][0], PHP_URL_PATH));
        $invitation = $this->get($hexaaAdmin, $invitationId);

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
