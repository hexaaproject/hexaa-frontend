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

namespace AppBundle\Controller;

use AppBundle\Form\ConnectServiceRequest1Type;
use AppBundle\Form\ConnectServiceRequest2Type;
use AppBundle\Form\OrganizationPropertiesType;
use AppBundle\Form\OrganizationRoleType;
use AppBundle\Form\OrganizationRoleUpdateType;
use AppBundle\Form\OrganizationUserChangeRolesType;
use AppBundle\Form\OrganizationUserInvitationSendEmailType;
use AppBundle\Form\OrganizationUserInvitationType;
use AppBundle\Form\OrganizationType;
use AppBundle\Form\OrganizationUserMessageManagerType;
use AppBundle\Form\OrganizationUserMessageType;
use AppBundle\Form\ConnectServiceType;
use GuzzleHttp\Exception\ClientException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Devmachine\Bundle\FormBundle\Form\Type\TypeaheadType;
use WebDriver\Exception;
use AppBundle\Exception\BackendException;
use GuzzleHttp\Exception\RequestException;

/**
 * @Route("/organization")
 */
class OrganizationController extends BaseController
{
    /**
     * @Route("/")
     * @Template()
     * @return Response
     */
    public function indexAction()
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $organizations = $this->get('organization')->cget($hexaaAdmin);
        $services = $this->get('service')->cget($hexaaAdmin);


        return $this->render(
            'AppBundle:Organization:index.html.twig',
            array(
                'organizations' => $organizations,
                'services' => $services,
                'organizationsWhereManager' => $this->orgWhereManager(),
                'manager' => "false",
                'hexaaHat' => $this->get('session')->get('hexaaHat'),
            )
        );
    }

    /**
     * @Route("/create")
     * @Template()
     * @return Response
     * @param   Request $request request
     */
    public function createAction(Request $request)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $form = $this->createForm(OrganizationType::class, ['role' => 'default']);

        $form->handleRequest($request);
        $firstpageerror = false;
        $secondpageerror = false;

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                //$thirdpageerror = false;

                $dataToBackend = $data;
                $organizations = $this->get('organization')->cget($hexaaAdmin);

                foreach ($organizations['items'] as $organization) {
                    if (strtolower($organization['name']) == strtolower($dataToBackend["name"])) {
                        $form["name"]->addError(new FormError('Organization name is case insensitive! This name already exists!'));
                        $firstpageerror = "true";
                    }
                }

                if (strlen($dataToBackend['name']) < 3) {
                    $form["name"]->addError(new FormError('This name of the organization has to be at least three character long!'));
                    $firstpageerror = "true";
                }

                if (strlen($dataToBackend['role']) < 3) {
                    $form["role"]->addError(new FormError('This name of the role has to be at least three character long!'));
                    if ($firstpageerror != "true") {
                        $secondpageerror = "true";
                    }
                }

                foreach ($form->getErrors(true) as $error) {
                    throw new \Exception();
                }

                // create organization
                $organization = $this->get('organization')->create(
                    $hexaaAdmin,
                    $dataToBackend["name"],
                    $dataToBackend["description"]
                );

                // valami miatt erre szükség van, mert amúgy más értéket fog meghívni a createRole
                $orgid = $organization['id'];


                // create role
                $role = $this->get('organization')->createRole(
                    $hexaaAdmin,
                    $orgid,
                    $dataToBackend['role'],
                    $this->get('role')
                );
                // put creator to role
                $self = $this->get('principal')->getSelf($hexaaAdmin, "normal", $this->getUser()->getToken());
                $this->get('role')->putPrincipal($hexaaAdmin, $role['id'], $self['id']);

                // set role to default in organization
                $this->get('organization')->patch($hexaaAdmin, $orgid, ["default_role" => $role['id']]);

                // create invitations
                try {
                    if ($dataToBackend["invitation_emails"]) {
                        $this->sendInvitations($organization, $role, $dataToBackend["invitation_emails"]);
                    }
                } catch (\Exception $e) {
                    $this->get('session')->getFlashBag()->add('error', $e->getMessage());
                }

                // connect to service
                $token = $dataToBackend["service_token"]; //TODO issue #103
                try {
                    if ($dataToBackend["service_token"] !== null) {
                        $this->get('organization')->connectService($hexaaAdmin, $orgid, $token);
                    }
                  // $this->get('session')->getFlashBag()->add('success', 'Service connected successfully to the organization.');
                } catch (\Exception $e) {
                    $this->get('session')->getFlashBag()->add('error', $e->getMessage());
                }


                return $this->render('AppBundle:Organization:created.html.twig', [
                    'neworg' => $this->get('organization')->get($hexaaAdmin, $orgid, "expanded"),
                    "organizations" => $this->get('organization')->cget($hexaaAdmin),
                    "services" => $this->get('service')->cget($hexaaAdmin),
                    'organizationsWhereManager' => $this->orgWhereManager(),
                    "admin" => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
                    "firstpageerror" => $firstpageerror,
                    "secondpageerror" => $secondpageerror,
                    'manager' => "false",
                    'hexaaHat' => $this->get('session')->get('hexaaHat'),
                ]);
            }
        } catch (\Appbundle\Exception $exception) {
            $form->addError(new FormError($exception->getMessage()));
            $this->get('session')->getFlashBag()->add('error', $exception->getMessage());
       /*     $partaftermessage = explode("\"message\":", $message);
          dump($partaftermessage);
            $errormessage = $this->get_string_between($partaftermessage[1], "\"", "\"");
            //dump($errormessage);exit;
            if ($errormessage == "Token not found") {
                $form["service_token"]->addError(new FormError($errormessage));
                if ($firstpageerror != "true" and $secondpageerror != "true") {
                    $thirdpageerror = "true";
                }
            } else {
            }*/
        } catch (\Exception $exception) {
        }

        return $this->render('AppBundle:Organization:create.html.twig', [
            'form' => $form->createView(),
            "organizations" => $this->get('organization')->cget($hexaaAdmin),
            'organizationsWhereManager' => $this->orgWhereManager(),
            "services" => $this->get('service')->cget($hexaaAdmin),
            "admin" => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
            "firstpageerror" => $firstpageerror,
            "secondpageerror" => $secondpageerror,
            'manager' => "false",
            'hexaaHat' => $this->get('session')->get('hexaaHat'),
        ]);
    }

   /* /**
    * @param string $string
    * @param int    $start
    * @param int    $end
    * @return string
    */
  /*  public function getStringBetween($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }*/

    /**
    * @Route("/{id}/show")
     * @return Response
     * @param int     $id      Organization ID
     * @param Request $request
     */
    public function showAction($id, Request $request)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $organization = $this->getOrganization($id);
        $manager = $this->isManager($id);
        if ($hexaaAdmin == "true") {
            $manager = "true";
        }

        if ($manager) {
            $form = $this->createForm(
                ConnectServiceType::class
            );

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $token = $data["token"];
                try {
                    $this->get('organization')->connectService($hexaaAdmin, $id, $token);
                    $this->get('session')->getFlashBag()->add('success', 'Service connected successfully to the organization.');
                } catch (\Exception $e) {
                    $this->get('session')->getFlashBag()->add('error', $e->getMessage());
                }

                return $this->redirect($request->getUri());
            }

            return $this->render(
                'AppBundle:Organization:show.html.twig',
                array(
                    'entity_show_path' => $this->getEntityShowPath($organization, $manager),
                    'entity' => $organization,
                    'organizations' => $this->get('organization')->cget($hexaaAdmin),
                    'organizationsWhereManager' => $this->orgWhereManager(),
                    'manager' => $manager,
                    'tokenForm' => $form->createView(),
                    'services' => $this->get('service')->cget($hexaaAdmin),
                    "admin" => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
                    'submenu' => 'true',
                    'ismanager' => $manager,
                    'hexaaHat' => $this->get('session')->get('hexaaHat'),
                )
            );
        } else {
            return $this->render(
                'AppBundle:Organization:show.html.twig',
                array(
                    'entity_show_path' => $this->getEntityShowPath($organization, $manager),
                    'entity' => $organization,
                    'organizations' => $this->get('organization')->cget($hexaaAdmin),
                    'organizationsWhereManager' => $this->orgWhereManager(),
                    'manager' => $manager,
                    'services' => $this->get('service')->cget($hexaaAdmin),
                    "admin" => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
                    'submenu' => 'true',
                    'ismanager' => $manager,
                    'hexaaHat' => $this->get('session')->get('hexaaHat'),
                )
            );
        }
    }

    /**
     * @Route("/{id}/properties/{action}", defaults={"action" = null})
     * @Template()
     * @return Response
     * @param   Request     $request request
     * @param   int         $id      Organization ID
     * @param   string|null $action  Turn edit mode inmediatly on with `edit` value
     */
    public function propertiesAction(Request $request, int $id, string $action = null)
    {
        $organization = $this->getOrganization($id);
        $roles = $this->getRoles($organization);
        $defaultRoleName = "";
        $rolesForFieldSource = array();
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');

        foreach ($roles as $role) {
            if ($organization['default_role_id'] == $role['id']) {
                $defaultRoleName = $role['name'];
            }
            $rolesForFieldSource[] = array (
                'id' => $role['id'],
                'name' => $role['name'],
            );
        }
        $organization['default_role_name'] = $defaultRoleName;

        $propertiesbox = array(
            "Name" => "name",
            "Description" => "description",
            "Home page" => "url",
            "Default role" => "default_role_name",
        );

        $propertiesDatas = array();

        $propertiesDatas['name'] = $organization['name'];
        $propertiesDatas['description'] = $organization['description'];
        $propertiesDatas['url'] = $organization['url'];
        $propertiesDatas['default_role_id'] = $organization['default_role_id'];
        $propertiesDatas['roles'] = $rolesForFieldSource;

        $formProperties = $this->createForm(
            OrganizationPropertiesType::class,
            array(
                'properties' => $propertiesDatas,
            )
        );
        $error = false;

        $formProperties->handleRequest($request);
        if ($request->getMethod() == 'POST') {
            if (!$formProperties->isValid()) {
              $error = true;
            }
        }


        if ($formProperties->isSubmitted() && $formProperties->isValid()) {
            $data = $request->request->all();
            $error = false;
            $modified = array(
                'name' => $data['organization_properties']['name'],
                'default_role' => $data['organization_properties']['default_role_id'],
                'description' => $data['organization_properties']['description'],
                'url' => $data['organization_properties']['url'],
            );
            $this->get('organization')->patch($hexaaAdmin, $id, $modified);
            return $this->redirect($request->getUri());
        }

        $manager = $this->isManager($id);
        if ($hexaaAdmin == "true") {
            $manager = true;
        }

        return $this->render(
            'AppBundle:Organization:properties.html.twig',
            array(
                'entity_show_path' => $this->getEntityShowPath($organization, $manager),
                "entity" => $organization,
                "propertiesbox" => $propertiesbox,
                "propertiesform" => $formProperties->createView(),
                "action" => $action,
                "roles" => $this->rolesToAccordion(true, $roles, $id, false, false, false, false, $request),
                "organizations" => $this->get('organization')->cget($hexaaAdmin),
                "services" => $this->get('service')->cget($hexaaAdmin),
                "admin" => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
                'submenu' => 'true',
                'organizationsWhereManager' => $this->orgWhereManager(),
                'manager' => "false",
                'error' => $error,
                'ismanager' => $manager,
                'hexaaHat' => $this->get('session')->get('hexaaHat'),
            )
        );
    }

    /**
     * @Route("/{id}/users")
     * @Template()
     * @return Response
     * @param   int     $id      Organization ID
     * @param   Request $request request
     */
    public function usersAction($id, Request $request)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $organization = $this->getOrganization($id);

        $managers = $this->getManagers($organization);
        $members = $this->getMembers($organization);

        foreach ($members as $member) {
            foreach ($managers as $manager) {
                if ($member['id'] == $manager['id']) {
                    $key = array_search($member, $members);
                    unset($members[$key]);
                }
            }
        }

        $managersButtons = array(
            "changerole" => array(
                "class" => "btn-blue",
                "text" => "Change roles",
            ),
            "revoke" => array(
                "class" => "btn-blue",
                "text" => "Revoke",
            ),
            "proposal" => array(
                "class" => "btn-blue",
                "text" => "Proposal",
            ),
            "message" => array(
                "class" => "btn-blue",
                "text" => "Message",
            ),
            "remove" => array(
                "class" => "btn-blue",
                "text" => "Remove",
            ),
            "invite" => array(
                "class" => "btn-red",
                "last" => true,
                "text" => '<i class="material-icons">add</i> Invite',
            ),
        );
       /* $membersButtons = array(
            "changerole" => array(
                "class" => "btn-blue",
                "text" => "Change roles",
            ),
            "proposal" => array(
                "class" => "btn-blue",
                "text" => "Proposal",
            ),
            "message" => array(
                "class" => "btn-blue",
                "text" => "Message",
            ),
            "remove" => array(
                "class" => "btn-blue",
                "text" => "Remove",
            ),
            "invite" => array(
                "class" => "btn-red",
                "last" => true,
                "text" => '<i class="material-icons">add</i> Invite',
            ),
        );*/


        $form = $this->createCreateInvitationForm($organization);

        $sendInEmailForm = $this->createForm(
            OrganizationUserInvitationSendEmailType::class,
            array(),
            array(
                "action" => $this->generateUrl("app_organization_sendinvitation", array("id" => $id)),
                "method" => "POST",
            )
        );

        $sendEmailForm = $this->createForm(
            OrganizationUserMessageManagerType::class,
            array(),
            array(
                "action" => $this->generateUrl("app_organization_message", array("id" => $id)),
                "method" => "POST",
            )
        );

       /* $sendMemberEmailForm = $this->createForm(
            OrganizationUserMessageType::class,
            array(),
            array(
                "action" => $this->generateUrl("app_organization_message", array("id" => $id)),
                "method" => "POST",
            )
        );*/

        $changeRolesForm = $this->createForm(
            OrganizationUserChangeRolesType::class,
            array('organizationRoles' => $this->getRoles($organization)),
            array(
                "action" => $this->generateUrl("app_organization_changerole", array("id" => $id)),
                "method" => "POST",
            )
        );

        $form->handleRequest($request);
        $manager = $this->isManager($id);
        if ($hexaaAdmin == "true") {
            $manager = "true";
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $dataToBackend = $data;
            $dataToBackend['organization'] = $id;
            $invitationResource = $this->get('invitation');
            $invite = $invitationResource->sendInvitation($dataToBackend);

            $headers = $invite->getHeaders();

            try {
                $invitationId = basename(parse_url($headers['Location'][0], PHP_URL_PATH));
                $invitation = $invitationResource->get($invitationId);
            } catch (\Exception $e) {
                throw $this->createNotFoundException('Invitation not found at backend');
            }

            $inviteLink = $this->generateUrl('app_organization_resolveinvitationtoken', array("token" => $invitation['token'], "organizationid" => $id), UrlGeneratorInterface::ABSOLUTE_URL);

            return $this->render(
                'AppBundle:Organization:users.html.twig',
                array(
                    'entity_show_path' => $this->getEntityShowPath($organization, $manager),
                    'entity' => $organization,

                    "managers" => $managers,
                    'manager' => "false",
                    "members" => $members,
                    'organizationsWhereManager' => $this->orgWhereManager(),
                    "organizations" => $this->get('organization')->cget($hexaaAdmin),
                    "services" => $this->get('service')->cget($hexaaAdmin),
                    "managers_buttons" => $managersButtons,
                    /*"members_buttons" => $membersButtons,*/
                    "invite_link" => $inviteLink,
                    "inviteForm" => $form->createView(),
                    "sendInEmailForm" => $sendInEmailForm->createView(),
                    "sendEmailForm" => $sendEmailForm->createView(),
                    /*"sendMemberEmailForm" => $sendMemberEmailForm->createView(),*/
                    "changeRolesForm" => $changeRolesForm->createView(),
                    "admin" => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
                    'ismanager' => $manager,
                    'hexaaHat' => $this->get('session')->get('hexaaHat'),
                )
            );
        }

        return $this->render(
            'AppBundle:Organization:users.html.twig',
            array(
                'entity_show_path' => $this->getEntityShowPath($organization, $manager),
                'entity' => $organization,
                'manager' => "false",
                "managers" => $managers,
                "members" => $members,
                'organizationsWhereManager' => $this->orgWhereManager(),
                "organizations" => $this->get('organization')->cget($hexaaAdmin),
                "services" => $this->get('service')->cget($hexaaAdmin),
                "managers_buttons" => $managersButtons,
                /*"members_buttons" => $membersButtons,*/
                "inviteForm" => $form->createView(),
                "sendInEmailForm" => $sendInEmailForm->createView(),
                "sendEmailForm" => $sendEmailForm->createView(),
               /* "sendMemberEmailForm" => $sendMemberEmailForm->createView(),*/
                "changeRolesForm" => $changeRolesForm->createView(),
                "admin" => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
                'submenu' => 'true',
                'ismanager' => $manager,
                'hexaaHat' => $this->get('session')->get('hexaaHat'),
            )
        );
    }

    /**
     * @Route("/{id}/createInvitation")
     * @Method("POST")
     * @Template()
     * @return Response
     * @param   int     $id      Organization ID
     * @param   Request $request request
     */
    public function createInvitationAction($id, Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(array('message' => 'You can access this only using Ajax!'), 400);
        }
        $organization = $this->getOrganization($id);

        // Invite form készítés
        $roles = array();
        foreach ($this->getRoles($organization) as $role) {
            $roles[$role['name']] = $role['id'];
        }

        $form = $this->createForm(OrganizationUserInvitationType::class);
        $form->add(
            'role',
            ChoiceType::class,
            array(
                "label" => false,
                'choices' => $roles,
                'required' => false,
                'placeholder' => 'To what role?',
            )
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $dataToBackend = $data;

            // TODO invitation->createHexaaInvitation()
            // TODO this->sendInvitations()

            $invitationResource = $this->get('invitation');
            $dataToBackend['organization'] = $id;
            $invite = $invitationResource->sendInvitation($this->get('session')->get('hexaaAdmin'), $dataToBackend);

            $headers = $invite->getHeaders();

            try {
                $invitationId = basename(parse_url($headers['Location'][0], PHP_URL_PATH));
                $invitation = $invitationResource->get($this->get('session')->get('hexaaAdmin'), $invitationId);
            } catch (\Exception $e) {
                throw $this->createNotFoundException('Invitation not found at backend');
            }

            $landingUrl = null;
            if (! empty($data['landing_url'])) {
                $landingUrl = urlencode($data['landing_url']);
            }
            $inviteLink = $this->generateUrl('app_organization_resolveinvitationtoken', array("token" => $invitation['token'], "organizationid" => $id, "landing_url" => $landingUrl), UrlGeneratorInterface::ABSOLUTE_URL);

            return new JsonResponse(array('link' => $inviteLink), 200);
        }
    }

    /**
     * @Route("/{id}/sendInvitation")
     * @Method("POST")
     * @Template()
     * @return Response
     * @param   int     $id      Organization ID
     * @param   Request $request request
     */
    public function sendInvitationAction($id, Request $request)
    {
        $organization = $this->getOrganization($id);
        $form = $this->createForm(OrganizationUserInvitationSendEmailType::class);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            if (! $data['emails']) { // there is no email, we are done
                return $this->redirect($this->generateUrl('app_organization_users', array("id" => $id)));
            }
            $role = null;
            if ($data['role_id']) {
                $role = $this->getRole($data['role_id']);
            }
            $emails = explode(',', preg_replace('/\s+/', '', $data['emails']));
            $config = $this->getParameter('invitation_config');
            $hexaa_ui_url = $this->generateUrl('homepage', array(), UrlGeneratorInterface::ABSOLUTE_URL);
            $currentPrincipal = $this->get('principal')->getSelf($this->get('session')->get('hexaaAdmin'));
            $mailer = $this->get('mailer');
            $link = $data['link'];
            foreach ($emails as $email) {
                try {
                    $message = $mailer->createMessage()
                        ->setSubject($config['subject'] . ' to ' . $organization['name'])
                        ->setFrom($config['from'])
                        ->setTo($email)
                        ->setReplyTo($config['reply-to'])
                        ->setBody(
                            $this->renderView(
                              'AppBundle:Organization:invitationEmail.html.twig',
                              [
                                'link' => $link,
                                'organization' => $organization,
                                'footer' => $config['footer'],
                                'role' => $role,
                                'message' => $data['message'],
                                'inviter' => $currentPrincipal['display_name'],
                                'hexaa_ui_url' => $hexaa_ui_url,
                                'recipient' => $email,
                              ]
                            ),
                            'text/html'
                        );

                    $mailer->send($message);
                    $this->get('session')
                      ->getFlashBag()
                      ->add('success', 'Invitations sent succesfully.');
                } catch (\Exception $e) {
                    $this->get('session')
                        ->getFlashBag()
                        ->add('error', 'Invitation sending failure. <br> Please send the invitation link manually to your partners. <br> The link is: <br><strong>' . $link . '</strong><br> The error was: <br> ' . $e->getMessage());
                }
            }

            return $this->redirect($this->generateUrl('app_organization_users', array("id" => $id)));
        }
    }


    /**
     * @Route("/{organizationid}/resolveInvitationToken/{token}/{landing_url}", defaults={"landing_url" = null})
     * @Template()
     * @return Response
     * @param   string $token          Invitation token
     * @param   int    $organizationid Organization ID
     * @param   string $landingUrl     Url to redirect after accept invitation
     */
    public function resolveInvitationTokenAction($token, $organizationid, $landingUrl = null)
    {
        $invitationResource = $this->get('invitation');
        try {
            $invitationResource->accept($this->get('session')->get('hexaaAdmin', "false"), $token);
        } catch (\Exception $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            switch ($statusCode) {
                case '409':
                    return array("error" => "You are already member of this organization.");
                    break;
                default:
                    return array("error" => $e->getMessage());
                    break;
            }
        }
        if ($landingUrl) {
            $decodedurl = urldecode($landingUrl);

            return $this->redirect($decodedurl);
        }
        $this->get('session')->getFlashBag()->add('success', 'The invitation accepted successfully.');

        return $this->redirect($this->generateUrl('app_organization_properties', array("id" => $organizationid)));
    }

    /**
     * @Route("/{id}/removeusers")
     * @Template()
     * @return Response
     * @param   int     $id      Organization ID
     * @param   Request $request request
     */
    public function removeUsersAction($id, Request $request)
    {
        $pids = $request->get('userId');
        $organizationResource = $this->get('organization');

        $errors = array();
        $errormessages = array();
        foreach ($pids as $pid) {
            try {
                $organizationResource->deleteMember($this->get('session')->get('hexaaAdmin'), $id, $pid);
            } catch (\Exception $e) {
                $errors[] = $e;
                $errormessages[] = $e->getMessage();
            }
        }
        if (count($errors)) {
            $this->get('session')->getFlashBag()->add('error', implode(', ', $errormessages));
            $this->get('logger')->error('User remove failed');
        }

        return $this->redirect($this->generateUrl('app_organization_users', array('id' => $id)));
    }

    /**
     * @Route("/{id}/message")
     * @Template()
     * @return Response
     * @param   int     $id      Organization ID
     * @param   Request $request request
     */
    public function messageAction($id, Request $request)
    {
        $form1 = $this->createForm(OrganizationUserMessageType::class);
        $form2 = $this->createForm(OrganizationUserMessageManagerType::class);
        $pids = $request->get('userId');
        $emails = array();
        foreach ($pids as $pid) {
            $principal = $this->get('principals')->getById($this->get('session')->get('hexaaAdmin'), $pid);
            array_push($emails, $principal['email']);
        }
        $currentPrincipal = $this->get('principal')->getSelf($this->get('session')->get('hexaaAdmin'));

        $form1->handleRequest($request);
        $form2->handleRequest($request);
        if ($form1->isValid() || $form2->isValid()) {
            $organization = $this->getOrganization($id);

            if ($form1->isValid()) {
                $data = $form1->getData();
            }
            if ($form2->isValid()) {
                $data = $form2->getData();
            }
            $config = $this->getParameter('invitation_config');
            $mailer = $this->get('mailer');
            try {
                $message = $mailer->createMessage()
                    ->setSubject($data['subject'])
                    ->setFrom($currentPrincipal['email'])
                    ->setCc($emails)
                    ->setReplyTo($config['reply-to'])
                    ->setBody(
                        $this->render(
                            'AppBundle:Organization:sendEmail.txt.twig',
                            array(
                                'footer' => $config['footer'],
                                'message' => $data['message'],
                            )
                        ),
                        'text/plain'
                    );

                $mailer->send($message);
                $this->get('session')->getFlashBag()->add('success', 'Message sent succesfully.');
            } catch (\Exception $e) {
                $this->get('session')->getFlashBag()->add('error', 'Message sending failure. <br>'.$e->getMessage());
            }

            return $this->redirect($this->generateUrl('app_organization_users', array('id' => $id)));
        }
    }

    /**
     * @Route("/{id}/propose")
     * @Template()
     * @return Response
     * @param   int     $id      Organization ID
     * @param   Request $request request
     */
    public function proposeAction($id, Request $request)
    {
        $organization = $this->getOrganization($id);

        $pids = $request->get('userId');
        $errors = array();
        $errormessages = array();
        foreach ($pids as $pid) {
            try {
                $this->get('organization')->addManager($this->get('session')->get('hexaaAdmin'), $id, $pid);
            } catch (\Exception $e) {
                $errors[] = $e;
                $errormessages[] = $e->getMessage();
            }
        }
        if (count($errors)) {
            $this->get('session')->getFlashBag()->add('error', implode(', ', $errormessages));
            $this->get('logger')->error('Set member to manager failed!');
        }

        return $this->redirect($this->generateUrl('app_organization_users', array('id' => $id)));
    }

    /**
     * @Route("/{id}/revoke")
     * @Template()
     * @return Response
     * @param   int     $id      Organization ID
     * @param   Request $request request
     */
    public function revokeAction($id, Request $request)
    {

        $organization = $this->getOrganization($id);

        $pids = $request->get('userId');
        $errors = array();
        $errormessages = array();
        foreach ($pids as $pid) {
            try {
                $this->get('organization')->deleteManager($this->get('session')->get('hexaaAdmin'), $id, $pid);
            } catch (\Exception $e) {
                $errors[] = $e;
                $errormessages[] = $e->getMessage();
            }
        }
        if (count($errors)) {
            $this->get('session')->getFlashBag()->add('error', implode(', ', $errormessages));
            $this->get('logger')->error('Set manager to member failed!');
        }

        return $this->redirect($this->generateUrl('app_organization_users', array('id' => $id)));
    }

    /**
     * @Route("/{id}/changerole")
     * @Template()
     * @return Response
     * @param   int     $id      Organization ID
     * @param   Request $request request
     * @throws \AppBundle\Exception
     */
    public function changeroleAction($id, Request $request)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $organization = $this->getOrganization($id);

        try {
            $form = $this->createForm(
                OrganizationUserChangeRolesType::class,
                array('organizationRoles' => $this->getRoles($organization))
            );
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $action = $request->get('action');
                $principalIds = $request->get('userId');
                $data = $form->getData();
                $roleIds = $data['roles'];
                $roleResource = $this->get('role');
                foreach ($roleIds as $roleId) {
                    foreach ($principalIds as $principalId) {
                        if ('add' == $action) {
                            $roleResource->putPrincipal($hexaaAdmin, $roleId, $principalId);
                        } elseif ('remove' == $action) {
                            $roleResource->deletePrincipal($hexaaAdmin, $roleId, $principalId);
                        } else {
                            throw new \AppBundle\Exception("Invalid action: ".$action);
                        }
                    }
                }
            }
            $this->get('session')->getFlashBag()->add('success', 'Roles of users updated successful.');
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', $e->getMessage());
            $this->get('logger')->error($e);
        }

        return $this->redirect($this->generateUrl('app_organization_users', array('id' => $id)));
    }

    /**
     * @Route("/{orgId}/role/{id}/delete")
     * @Template()
     * @return Response
     * @param int $orgId Organization id
     * @param int $id    Role Id
     *
     */
    public function roleDeleteAction($orgId, $id)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $organization = $this->getOrganization($orgId);

        $organizationResource = $this->get('role');
        $organizationResource->delete($hexaaAdmin, $id);
        $this->get('session')->getFlashBag()->add('success', 'The role has been deleted.');

        return $this->redirectToRoute("app_organization_roles", array("id" => $orgId));
    }

    /**
     * @Route("/{id}/roles/{action}/{roleId}", defaults={"action": false, "roleId": false})
     * @Template()
     * @return Response
     * @param int     $id      Organization ID
     * @param string  $action  More action ex. create show create form
     * @param int     $roleId  actual role id
     * @param Request $request Request
     */
    public function rolesAction($id, $action, $roleId, Request $request)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        if (! in_array($action, array("false", "create"))) {
            $this->createNotFoundException("Invalid action in url: ".$action);
        }

        $organization = $this->getOrganization($id);
        $roles = $this->getRoles($organization);
        $entitlements = $this->getEntitlements($organization);
        $members = $this->getMembers($organization);
        $rolesAccordion = $this->rolesToAccordion(false, $roles, $id, $entitlements, $members, $action, $roleId, $request);

        if (false === $rolesAccordion) { // belső form rendesen le lett kezelve, vissza az alapokhoz
            return $this->redirectToRoute('app_organization_roles', array("id" => $id));
        }

        $form = $this->createForm(
            OrganizationRoleType::class,
            array()
        );

        $form->handleRequest($request);
        $error = "false";
        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                if (!empty($roles)) {
                    foreach ($roles as $role) {
                        if (strtolower($data['name']) == $role['name']) {
                            throw new \Exception('Role name is case insensitive! It must be unique!');
                        }
                    }
                }

                if (strlen($data['name']) < 3) {
                    throw new \Exception('Role name has to be at least three character long!');
                }

                //create role
                $role = $this->get('organization')->createRole(
                    $hexaaAdmin,
                    $id,
                    $data['name'],
                    $this->get('role')
                );

                // put creator to role
                if ($data["wantToBeAMember"]) {
                    $members = $this->get('organization')->getMembers($hexaaAdmin, $id);
                    $self = $this->get('principal')->getSelf($hexaaAdmin, "normal", $this->getUser()->getToken());
                    $putmember = false;
                    foreach ($members['items'] as $member) {
                        if($member['fedid'] == $self['fedid']) {
                            $this->get('role')->putPrincipal($hexaaAdmin, $role['id'], $self['id']);
                            $putmember = true;
                            break;
                        }
                    }
                    if ($putmember == false) {
                        $this->get('role')->delete($hexaaAdmin, $role['id']);
                        throw new \Exception("You can't be member of this role until you aren't a member of this organization!");
                    }
                }

                return $this->redirect($request->getUri());
            }
        } catch (\Exception $exception) {
            $this->get('session')->getFlashBag()->add('error', $exception->getMessage());
            $error = 'true';
        }
        $manager = $this->isManager($id);
        if ($hexaaAdmin == "true") {
            $manager = "true";
        }

        return $this->render(
            'AppBundle:Organization:roles.html.twig',
            array(
                'entity_show_path' => $this->getEntityShowPath($organization, $manager),
                'entity' => $organization,
                "roles" => $roles,
                "roles_accordion" => $rolesAccordion,
                "action" => $action,
                "form" => $form->createView(),
                "organizations" => $this->get('organization')->cget($hexaaAdmin),
                "services" => $this->get('service')->cget($hexaaAdmin),
                "admin" => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
                'submenu' => 'true',
                'error' => $error,
                'organizationsWhereManager' => $this->orgWhereManager(),
                'manager' => "false",
                'ismanager' => $manager,
                'hexaaHat' => $this->get('session')->get('hexaaHat'),
            )
        );
    }

    /**
     * @Route("/{id}/connectedservices/{action}", defaults={"action": false})
     * @Template()
     * @return Response
     * @param int     $id      Organization Id
     * @param string  $action
     * @param Request $request
     */
    public function connectedservicesAction($id, $action, Request $request)
    {
        $manager = $this->isManager($id);
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        if ($hexaaAdmin == "true") {
            $manager = true;
        }

        if (! in_array($action, array("false", "create"))) {
            $this->createNotFoundException("Invalid action in url: ".$action);
        }

        $form = $this->createForm(
            ConnectServiceType::class,
            array()
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $organization = $this->getOrganization($id);

            $data = $form->getData();
            $token = $data["token"];
            try {
                $this->get('organization')->connectService($hexaaAdmin, $id, $token);
            } catch (\Exception $e) {
                $this->get('session')->getFlashBag()->add('error', $e->getMessage());
            }

            return $this->redirect($request->getUri());
        }

        $services = array();
        $linkIDs = array();
        $links = null;
        if ($manager) {
            $links = $this->get('organization')->getLinks($hexaaAdmin, $id);
            foreach ($links['items'] as $link) {
                $service = $this->get('service')->get($hexaaAdmin, $link['service_id']);
                array_push($services, $service);
                array_push($linkIDs, $link['id']);
            }
        }

        $entitlementpacks = array();
        foreach ($linkIDs as $linkID) {
            $eps = $this->get('link')->getEntitlementPacks($hexaaAdmin, $linkID);
            if ($eps['item_number'] != 0) {
                array_push($entitlementpacks, $eps);
            }
        }
        $entitlementids = array();
        foreach ($entitlementpacks as $entitlementpackitems) {
            foreach ($entitlementpackitems['items'] as $entitlementpackitem) {
                foreach ($entitlementpackitem['entitlement_ids'] as $oneid) {
                    array_push($entitlementids, $oneid);
                }
            }
        }

        $entitlementsunique = array_unique($entitlementids, $sortflags = SORT_REGULAR);

        $entitlements = array();

        foreach ($linkIDs as $linkID) {
            $linksentitlements = $this->get('link')->getEntitlements($hexaaAdmin, $linkID);
            foreach ($linksentitlements['items'] as $linksentitlement) {
                if (count($linksentitlements) > 0) {
                    array_push($entitlements, $linksentitlement);
                }
            }
        }

        $principalentitlements = $this->get('principal')->getEntitlements($hexaaAdmin);
        $entitlementorg = $this->get('organization')->getEntitlements($hexaaAdmin, $id, 'normal', 0, 10000);
        foreach ($entitlementsunique as $entitlementunique) {
            $entitlement = null;
            foreach ($entitlementorg['items'] as $oneentitlement) {
                if ($oneentitlement['id'] == $entitlementunique) {
                    $entitlement = $oneentitlement;
                    break;
                }
            }
         // $entitlement = $this->get('entitlement')->get($entitlementunique);
            if (empty($entitlements)) {
            }
            if (!in_array($entitlement, $entitlements)) {
                array_push($entitlements, $entitlement);
            }
        }

        $servicesAccordion = null;
        if ($entitlementpacks != null && $entitlementpacks[0]['items'] != null) {
            $servicesAccordion = $this->servicesToAccordion($id, $services, $entitlementpacks);
        }

        $entitlementsAccordion = null;
        if ($entitlements != null) {
            $entitlementsAccordion = $this->entitlementsToAccordion($id, $services, $entitlements);
        }

        $organization = $this->get('organization')->get($hexaaAdmin, $id);

        return $this->render(
            'AppBundle:Organization:connectedservices.html.twig',
            array(
                'entity_show_path' => $this->getEntityShowPath($organization, $manager),
                'entity' => $organization,
                "organizations" => $this->get('organization')->cget($hexaaAdmin),
                "services" => $this->get('service')->cget($hexaaAdmin),
                "services_accordion" => $servicesAccordion,
                "entitlements_accordion" => $entitlementsAccordion,
                "action" => $action,
                "form" => $form->createView(),
                "admin" => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
                "manager" => $manager,
                'submenu' => 'true',
                'organizationsWhereManager' => $this->orgWhereManager(),
                'ismanager' => $manager,
                'hexaaHat' => $this->get('session')->get('hexaaHat'),
            )
        );
    }

    /**
     * @Route("/{id}/delete")
     * @Template()
     * @return Response
     * @param int $id Organization Id
     *
     */
    public function deleteAction($id)
    {
        $organization = $this->getOrganization($id);

        $organizationResource = $this->get('organization');
        $organizationResource->delete($this->get('session')->get('hexaaAdmin'), $id);

        return $this->redirectToRoute("homepage");
    }

    /**
     * Get the history of the requested organization.
     * @Route("/history/{id}")
     * @Template()
     * @return array
     * @param int $id Organization Id
     */
    public function historyAction($id)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $organizationResource = $this->get('organization');
        $organization = $organizationResource->get($hexaaAdmin, $id);
        $manager = $this->isManager($id);
        if ($hexaaAdmin == "true") {
            $manager = "true";
        }

        return array(
            'entity_show_path' => $this->getEntityShowPath($organization, $manager),
            'entity' => $organization,
            'manager' => "false",
            "organizations" => $this->get('organization')->cget($hexaaAdmin),
            "services" => $this->get('service')->cget($hexaaAdmin),
            "admin" => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
            'ismanager' => $manager,
            'submenu' => 'true',
            'organizationsWhereManager' => $this->orgWhereManager(),
            'hexaaHat' => $this->get('session')->get('hexaaHat'),
        );
    }

    /**
     * @Route("/history/json/{id}")
     * @param string       $id       Organization id
     * @param integer|null $offset   Offset
     * @param integer      $pageSize Pagesize
     * @return array
     */
    public function historyJSONAction($id, $offset = null, $pageSize = 25)
    {
        $organizationResource = $this->get('organization');
        $principalResource = $this->get('principals');
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        if  ($this->get('session')->get('hexaaAdmin') == null) {
            $hexaaAdmin = 'false';
        }
        $data = $organizationResource->getHistory($hexaaAdmin, $id);
        $displayNames = array();
        for ($i = 0; $i < $data['item_number']; $i++) {
            $principalId = $data['items'][$i]['principal_id'];
            if ($principalId) {
                if (! array_key_exists($principalId, $displayNames)) {
                    $principal = $principalResource->getById($this->get('session')->get('hexaaAdmin'), $principalId);
                    $displayNames[$principalId] = $principal['display_name']." «".$principal['email']."»";
                }
                $data['items'][$i]['principal_display_name'] = $displayNames[$principalId];
            } else {
                $data['items'][$i]['principal_display_name'] = '';
            }

            $dateTime = new \DateTime($data['items'][$i]['created_at']);
            $data['items'][$i]['created_at'] =  "<div style='white-space: nowrap'>".$dateTime->format('Y-m-d H:i')."</div>";
        }
        $response = new JsonResponse($data);

        return $response;
    }

    /**
    * @Route("/{id}/link/{servId}/delete")
    * @Template()
    * @return Response
    * @param int $servId Service id
    * @param int $id     Permission Id
    *
    */
    public function linkDeleteAction($servId, $id)
    {
        $organization = $this->getOrganization($id);
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $orglinks = $this->get('organization')->getLinks($hexaaAdmin, $id);
        foreach ($orglinks['items'] as $orglink) {
            if ($orglink['organization_id'] == $id && $orglink['service_id'] == $servId) {
                $this->get('link')->deletelink($hexaaAdmin, $orglink['id']);
            }
        }
        $this->get('session')->getFlashBag()->add('success', 'The link has been deleted.');

        return $this->redirectToRoute("app_organization_connectedservices", array("id" => $id));
    }

    /**
     * @Route("/{id}/{ismanager}/warnings")
     * @param string $id
     * @param bool    $ismanager
     *
     * @return JsonResponse
     */
    public function getWarnings($id, $ismanager)
    {
        $organization = $this->get('organization');
        $serializer = $this->get('serializer');
        $data = $organization->getWarnings($this->get('session')->get('hexaaAdmin'), $id, array("roleResource" => $this->get('role')), $ismanager);
        $serializedData = $serializer->serialize($data, 'json');

        return new JsonResponse($serializedData);
    }

    /**
     * @param $organization
     * @return \Symfony\Component\Form\Form
     */
    private function createCreateInvitationForm($organization)
    {
        // Invite form készítés
        $roles = array();
        foreach ($this->getRoles($organization) as $role) {
            $roles[$role['name']] = $role['id'];
        }

        $form = $this->createForm(
            OrganizationUserInvitationType::class,
            array(
                "start_date" => date("Y-m-d"),
                "end_date" => date("Y-m-d", strtotime("+1 week")),
            ),
            array(
                "action" => $this->generateUrl("app_organization_createinvitation", array("id" => $organization['id'])),
                "method" => "POST",
            )
        );

        $form->add(
            'role',
            ChoiceType::class,
            array(
                "label" => false,
                'choices' => $roles,
                'required' => false,
                'placeholder' => 'To what role?',
            )
        );

        return $form;
    }

    /**
     * @param $id
     * @return mixed
     */
    private function getOrganization($id)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        if($this->get('session')->get('hexaaAdmin') == null) {
            $hexaaAdmin = 'false';
        }
        return $this->get('organization')->get($hexaaAdmin, $id);
    }

    /**
    * @param $id
    * @return bool
    */
    private function isManager($id)
    {
        $manager = false;
        $organizations = $this->get('principal')->orgsWhereUserIsManager($this->get('session')->get('hexaaAdmin'));
        foreach ($organizations as $oneorg) {
            if ($oneorg['id'] == $id) {
                $manager = true;
                break;
            }
        }

        return $manager;
    }

    /**
     * Check user is manager, throw AccessDeniedException if not.
     * @param $organization
     * @return bool
     * @throws AccessDeniedHttpException
     */
    private function checkManagerGrant($organization)
    {
        $managers = $this->getManagers($organization);
        foreach ($managers as $manager) {
            if ($this->getUser()->getUsername() == $manager["fedid"]) {
                return true;
            }
        }

        throw new AccessDeniedHttpException("You are not a manager of this Organization.");
    }

    /**
     * @param $organization
     * @return mixed
     */
    private function getRoles($organization)
    {
        return $this->get('organization')->getRoles('true', $organization['id'], 'expanded')['items'];
    }

    /**
     * @param $organization
     * @return mixed
     */
    private function getManagers($organization)
    {
        return $this->get('organization')->getManagers($this->get('session')->get('hexaaAdmin'), $organization['id'])['items'];
    }

    /**
     * @param $organization
     * @return mixed
     */
    private function getMembers($organization)
    {
        return $this->get('organization')->getMembers($this->get('session')->get('hexaaAdmin'), $organization['id'])['items'];
    }

    /**
     * @param $organization
     * @return mixed
     */
    private function getEntitlements($organization)
    {
        return $this->get('organization')->getEntitlements($this->get('session')->get('hexaaAdmin'), $organization['id'])['items'];
    }

    /**
     * @param $service
     * @return mixed
     */
    private function getEntitlementPack($service)
    {
        return $this->get('service')->getEntitlementPacks($this->get('session')->get('hexaaAdmin'), $service['id'])['items'];
    }

    /**
     * @param $id
     * @return mixed
     */
    private function getRole($id)
    {
        return $this->get('role')->get($this->get('session')->get('hexaaAdmin'), $id);
    }

    /**
     * @param bool    $properties
     * @param array   $roles
     * @param int     $orgId
     * @param string  $action
     * @param int     $roleId
     * @param Request $request
     *
     * @return array
     */
    private function rolesToAccordion($properties, $roles, $orgId, $entitlements, $principals, $action, $roleId, Request $request)
    {
        $rolesAccordion = array();
        foreach ($roles as $role) {
            $rolesAccordion[$role['id']]['title'] = $role['name'];
            $rolesAccordion[$role['id']]['deleteUrl'] = $this->generateUrl("app_organization_roledelete", [
                'orgId' => $orgId,
                'id' => $role['id'],
            ]);

            $role['organizationMembers'] = $principals;
            $role['organizationEntitlements'] = $entitlements;
            $rolesAccordion[$role['id']]['entitlementstoprotip'] = true;
            if(count($roles) == 1) {
                $rolesAccordion[$role['id']]['last'] = 'true';
            } else {
                $rolesAccordion[$role['id']]['last'] = 'false';
            }

            if (!array_key_exists('principals', $role)) {
                $role['principals'] = [];
            }

            if ($properties == false) {
                $form = $this->createForm(
                    OrganizationRoleUpdateType::class,
                    $role,
                    [
                        "action" => $this->generateUrl("app_organization_roles", [
                            "id" => $orgId,
                            "action" => "update",
                            "roleId" => $role['id'],
                        ]),
                    ]
                );
            }

            $members = [];
            foreach ($role['principals'] as $principal) {
                if(!(empty($principal['principal']['display_name']))){
                    $members[$principal['principal']['id']] = $principal['principal']['display_name'];
                } elseif (!(empty($principal['principal']['email']))){
                    $members[$principal['principal']['id']] = $principal['principal']['email'];
                } else {
                    $members[$principal['principal']['id']] = $principal['principal']['fedid'];
                }
            }

            $permissions = [];
            foreach ($role['entitlements'] as $entitlement) {
                $permissions[] = $entitlement['name'];
            }

            if (empty($permissions) && empty($members))
            {
                $rolesAccordion[$role['id']]['protiptext'] = "This role hasn't got any permission and member";
                $rolesAccordion[$role['id']]['claim'] = true;
            }
            if (empty($permissions) && !empty($members))
            {
                $rolesAccordion[$role['id']]['protiptext'] = "This role hasn't got any permission";
                $rolesAccordion[$role['id']]['claim'] = true;
            }
            if (empty($members) && !empty($permissions))
            {
                $rolesAccordion[$role['id']]['protiptext'] = "This role hasn't got any member";
                $rolesAccordion[$role['id']]['claim'] = true;
            }
            if (!empty($permissions) && !empty($members))
            {
                $rolesAccordion[$role['id']]['claim'] = false;
            }

            $rolesAccordion[$role['id']]['contents'] = [
                [
                    'key' => 'Permissions',
                    'values' => $permissions,
                ],
                [
                    'key' => 'Members',
                    'values' => $members,
                ],
            ];

            if ($properties == false) {
                if ($roleId == $role['id']) { // csak akkor dolgozzuk fel, ha erről a role-ról van szó.
                    $form->handleRequest($request);
                }

                if ($form->isValid() and $form->isSubmitted()) {
                    $data = $form->getData();
                    try {
                        $roleResource = $this->get('role');
                        $role = $roleResource->get($this->get('session')->get('hexaaAdmin'), $data['id']);
                        $this->amIManagerOfThis($role); //TODO

                        $roleToBackend = [
                            'name' => $data['name'],
                        ];
                        try {
                            $roleResource->patch($this->get('session')->get('hexaaAdmin'), $role['id'], $roleToBackend);
                        } catch (\Exception $exception) {
                            $form->get('name')->addError(new FormError($exception->getMessage()));
                        }

                        $entitlementsToBackend = [];
                        foreach ($data['entitlements'] as $id) {
                            $entitlementsToBackend["entitlements"][] = $id;
                        }
                        try {
                            $roleResource->setEntitlements($this->get('session')->get('hexaaAdmin'), $roleId, $entitlementsToBackend);
                        } catch (\Exception $exception) {
                            $form->get('entitlements')->addError(new FormError($exception->getMessage()));
                        }

                        $principalsToBackend = [];
                        foreach ($data['members'] as $id) {
                            $principalsToBackend["principals"][] = ["principal" => $id];
                        }
                        try {
                            $roleResource->setPrincipals($this->get('session')->get('hexaaAdmin'), $roleId, $principalsToBackend);
                        } catch (\Exception $exception) {
                            $form->get('members')->addError(new FormError($exception->getMessage()));
                        }
                    } catch (\AppBundle\Exception $exception) {
                        $form->addError(new FormError($exception->getMessage()));
                    }
                    if (!$form->getErrors(true)->count()) { // false-szal térünk vissza, ha nincs hiba. Mehessen a redirect az alaphoz.
                        return false;
                    }
                }
                $rolesAccordion[$role['id']]['form'] = $form->createView();
            }
        }

        return $rolesAccordion;
    }

     /**
      *@param $id
     * @param $services
     * @param $entitlementPacks
     * @return array
     */
    private function servicesToAccordion($id, $services, $entitlementPacks)
    {
        $servicesAccordion = array();
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        foreach ($services as $service) {
            foreach ($entitlementPacks as $entitlementPacksub) {
                foreach ($entitlementPacksub['items'] as $entitlementPack) {
                    if ($entitlementPack['service_id'] == $service['id']) {
                        $servicesAccordion[$service['id']]['title'] = $service['name'];
                        $servicesAccordion[$service['id']]['deleteUrl'] = $this->generateUrl("app_organization_linkdelete", [
                            'servId' => $service['id'],
                            'id' => $id,
                            'action' => "delete",
                        ]);
                        $servicesAccordion[$service['id']]['description'] = 'Permission sets';
                        $managers = $this->get('service')->getManagers($hexaaAdmin, $service['id'])['items'];
                        $managersstring = "";
                        foreach ($managers as $manager) {
                            $managersstring .= $manager['display_name']." (".$manager['email'].") ";
                        }
                        $servicesAccordion[$service['id']]['titlemiddle'] = 'Service manager '.$managersstring;
                    }
                }
            }

            foreach ($entitlementPacks as $entitlementPacksub) {
                foreach ($entitlementPacksub['items'] as $entitlementPack) {
                    if ($entitlementPack['service_id'] == $service['id']) {
                        $servicesAccordion[$service['id']]['subaccordions'][$entitlementPack['id']]['title'] = $entitlementPack['name'];
                        $servicesAccordion[$service['id']]['subaccordions'][$entitlementPack['id']]['variant'] = 'light';
                        $servicesAccordion[$service['id']]['subaccordions'][$entitlementPack['id']]['contents'][] = array("key" => "Details", "values" => array($entitlementPack['description']));
                       // $servicesAccordion[$service['id']]['subaccordions'][$entitlementPack['id']]['buttons']['deleteEntitlementPack'] = array("icon" => "delete");

                        $entitlementnames = array();
                        $entitlementsorg = $this->get('organization')->getEntitlements($hexaaAdmin, $id, 'normal', 0, 10000);
                        foreach ($entitlementPack['entitlement_ids'] as $entitlementId) {
                            foreach ($entitlementsorg['items'] as $entitlementorg) {
                                if ($entitlementorg['id'] == $entitlementId) {
                                    $entitlementnames[] = $entitlementorg['name'];
                                }
                              // $entitlement = $this->get('entitlement')->get($entitlementId);
                              //$entitlementnames[] = $entitlement['name'];
                            }
                        }

                        $servicesAccordion[$service['id']]['subaccordions'][$entitlementPack['id']]['contents'][] = array("key" => "Permissions", "values" => $entitlementnames);
                    }
                }
            }
        }

        return $servicesAccordion;
    }

    /**
     * Replace accents
     *
     * @param string  $string
     * @return string
     */
    private function removeAccents($string)
    {
        if (!preg_match('/[\x80-\xff]/', $string)) {
            return $string;
        }

        $chars = array(
            chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
            chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
            chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
            chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
            chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
            chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
            chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
            chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
            chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
            chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
            chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
            chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
            chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
            chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
            chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
            chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
            chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
            chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
            chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
            chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
            chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
            chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
            chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
            chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
            chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
            chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
            chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
            chr(195).chr(191) => 'y',
            chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
            chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
            chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
            chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
            chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
            chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
            chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
            chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
            chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
            chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
            chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
            chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
            chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
            chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
            chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
            chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
            chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
            chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
            chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
            chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
            chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
            chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
            chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
            chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
            chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
            chr(196).chr(178) => 'IJ', chr(196).chr(179) => 'ij',
            chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
            chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
            chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
            chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
            chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
            chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
            chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
            chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
            chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
            chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
            chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
            chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
            chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
            chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
            chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
            chr(197).chr(146) => 'OE', chr(197).chr(147) => 'oe',
            chr(197).chr(148) => 'R', chr(197).chr(149) => 'r',
            chr(197).chr(150) => 'R', chr(197).chr(151) => 'r',
            chr(197).chr(152) => 'R', chr(197).chr(153) => 'r',
            chr(197).chr(154) => 'S', chr(197).chr(155) => 's',
            chr(197).chr(156) => 'S', chr(197).chr(157) => 's',
            chr(197).chr(158) => 'S', chr(197).chr(159) => 's',
            chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
            chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
            chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
            chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
            chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
            chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
            chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
            chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
            chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
            chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
            chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
            chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
            chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
            chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
            chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
            chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
        );

        $string = strtr($string, $chars);

        return $string;
    }

    /**
     * @param $services
     * @param $entitlements
     * @return array
     */
    private function entitlementsToAccordion($id, $services, $entitlements)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $entitlementsAccordion = array();
        foreach ($services as $service) {
            $entitlementsAccordion[$service['id']]['title'] = $service['name'];
            $entitlementsAccordion[$service['id']]['description'] = 'Permissions';
            $entitlementsAccordion[$service['id']]['deleteUrl'] = $this->generateUrl("app_organization_linkdelete", [
                'servId' => $service['id'],
                'id' => $id,
                'action' => "delete",
            ]);
            $managers = $this->get('service')->getManagers($hexaaAdmin, $service['id'])['items'];
            $managersstring = "";
            foreach ($managers as $manager) {
                $managersstring .= $manager['display_name']." (".$manager['email'].") ";
            }
            $entitlementsAccordion[$service['id']]['titlemiddle'] = 'Service manager '.$managersstring;
            foreach ($entitlements as $entitlement) {
                if ($entitlement['service_id'] == $service['id']) {
                    $entitlementsAccordion[$service['id']]['subaccordions'][$entitlement['id']]['title'] = $entitlement['name'];
                    $entitlementsAccordion[$service['id']]['subaccordions'][$entitlement['id']]['variant'] = 'light';
                    $entitlementsAccordion[$service['id']]['subaccordions'][$entitlement['id']]['contents'][] = array("key" => "Details", "values" => array($entitlement['description']));
                   // $entitlementsAccordion[$service['id']]['subaccordions'][$entitlement['id']]['buttons']['deleteEntitlement'] = array("icon" => "delete");
                   //  $servicesAccordion[$service['id']]['subaccordions'][$entitlementPack['id']]['contents'][] = array("key" => "Permissions", "values" => $entitlementnames);
                }
            }
        }

        return $entitlementsAccordion;
    }



    /**
     * Create and send hexaa invitations
     *
     * @param $organization
     * @param $role
     * @param string $emails
     * @param string|null $messageInMail
     */
    private function sendInvitations($organization, $role, string $emails, string $messageInMail = null)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $emails = explode(',', preg_replace('/\s+/', '', $emails));
        $config = $this->getParameter('invitation_config');
        $mailer = $this->get('mailer');
        $hexaa_ui_url = $this->generateUrl('homepage', array(), UrlGeneratorInterface::ABSOLUTE_URL);
        $currentPrincipal = $this->get('principal')->getSelf($this->get('session')->get('hexaaAdmin'));

        // create invitation

        $tokenResolverLink = $this->get('invitation')->createHexaaInvitation($hexaaAdmin, $organization['id'], $this->get('router'), $role['id']);
        foreach ($emails as $email) {
            try {
                $message = $mailer->createMessage()
                    ->setSubject($config['subject'] . ' to ' . $organization['name'])
                    ->setFrom($config['from'])
                    ->setTo($email)
                    ->setReplyTo($config['reply-to'])
                    ->setBody(
                        $this->renderView(
                            'AppBundle:Organization:invitationEmail.html.twig',
                            [
                                'link' => $tokenResolverLink,
                                'organization' => $organization,
                                'footer' => $config['footer'],
                                'role' => $role,
                                'message' => $messageInMail,
                                'hexaa_ui_url' => $hexaa_ui_url,
                                'inviter' => $currentPrincipal['display_name'],
                                'recipient' => $email,
                            ]
                        ),
                        'text/html'
                    );

                $mailer->send($message);
            } catch (Exception $exception) {
                $this->get('session')
                  ->getFlashBag()
                  ->add('error', 'Invitation sending failure. The error was: <br> ' . $exception->getMessage());
            }
        }
    }
}
