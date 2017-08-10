<?php

namespace AppBundle\Controller;

use AppBundle\Form\OrganizationUserInvitationSendEmailType;
use AppBundle\Form\OrganizationUserInvitationType;
use AppBundle\Form\OrganizationType;
use AppBundle\Model\Organization;
use GuzzleHttp\Exception\ClientException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/organization")
 */
class OrganizationController extends Controller
{

    /**
     * @Route("/")
     * @Template()
     * @return Response
     */
    public function indexAction()
    {
        $organizations = $this->get('organization')->cget();
        $services = $this->get('service')->cget();

        return $this->render(
            'AppBundle:Organization:index.html.twig',
            array(
                'organizations' => $organizations,
                'services' => $services,
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
        $form = $this->createForm(OrganizationType::class, array('role' => 'default'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $dataToBackend = $data;

            // create organization
            $organization = $this->get('organization')->create(
                $dataToBackend["name"],
                $dataToBackend["description"]
            );

            // valami miatt erre szükség van, mert amúgy más értéket fog meghívni a createRole
            $orgid = $organization['id'];

            // create role
            $role = $this->get('organization')->createRole(
                $orgid,
                $dataToBackend['role'],
                $this->get('role')
            );
            // put creator to role
            $self = $this->get('principal')->getSelf("normal", $this->getUser()->getToken());
            $this->get('role')->putPrincipal($role['id'], $self['id']);

            // set role to default in organization
            $this->get('organization')->patch($orgid, array("default_role" => $role['id']));

            // create invitations
            if ($dataToBackend["invitation_emails"]) {
                $this->sendInvitations($organization, $role, $dataToBackend["invitation_emails"]);
            }

            // connect to service
            // $dataToBackend["service_token"],

            return $this->render('AppBundle:Organization:created.html.twig', array('neworg' => $this->get('organization')->get($orgid, "expanded")));
        }

        return $this->render('AppBundle:Organization:create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/show/{id}")
     * @return Response
     * @param   int $id Organization ID
     */
    public function showAction($id)
    {
        $organization = $this->getOrganization($id);

        return $this->render(
            'AppBundle:Organization:show.html.twig',
            array(
                'organization' => $organization,
                'organizations' => $this->get('organization')->cget(),
                'services' => $this->get('service')->cget(),
                "admin" => $this->get('principal')->isAdmin()["is_admin"],
            )
        );
    }

    /**
     * @Route("/properties/{id}")
     * @Template()
     * @return Response
     * @param   int $id Organization ID
     */
    public function propertiesAction($id)
    {
        $organization = $this->getOrganization($id);
        $roles = $this->rolesToAccordion($this->getRoles($organization));
        $organization['default_role_name'] = null;
        foreach ($this->getRoles($organization) as $role) {
            if ($role['id'] == $organization['default_role_id']) {
                $defaultrole = $role;
                $organization['default_role_name'] = $role["name"];
            }
        }

        $propertiesbox = array(
            "Name" => "name",
            "Description" => "description",
            "Home page" => "url",
            "Default role" => "default_role_name",
        );

        return $this->render(
            'AppBundle:Organization:properties.html.twig',
            array(
                "organizations" => $this->get('organization')->cget(),
                "services" => $this->get('service')->cget(),
                "organization" => $organization,
                "roles" => $roles,
                "propertiesbox" => $propertiesbox,
                "admin" => $this->get('principal')->isAdmin()["is_admin"],
            )
        );
    }

    /**
     * @Route("/users/{id}")
     * @Template()
     * @return Response
     * @param   int     $id      Organization ID
     * @param   Request $request request
     */
    public function usersAction($id, Request $request)
    {

        $organization = $this->getOrganization($id);

        $managers = $this->getManagers($organization);
        $members = $this->getMembers($organization);
        $managersButtons = array(
            "changerole" => array(
                "class" => "btn-blue",
                "text" => "Change roles",
            ),
            "revoke" => array(
                "class" => "btn-blue",
                "text" => "Revoke",
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
        $membersButtons = array(
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
        );


        $form = $this->createCreateInvitationForm($organization);
        $sendInEmailForm = $this->createForm(
            OrganizationUserInvitationSendEmailType::class,
            array(),
            array(
                "action" => $this->generateUrl("app_organization_sendinvitation", array("id" => $id)),
                "method" => "POST",
            )
        );

        $form->handleRequest($request);

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
                    "managers" => $managers,
                    "members" => $members,
                    "organization" => $organization,
                    "organizations" => $this->get('organization')->cget(),
                    "services" => $this->get('service')->cget(),
                    "managers_buttons" => $managersButtons,
                    "members_buttons" => $membersButtons,
                    "invite_link" => $inviteLink,
                    "inviteForm" => $form->createView(),
                    "sendInEmailForm" => $sendInEmailForm->createView(),
                    "admin" => $this->get('principal')->isAdmin()["is_admin"],
                )
            );
        }

        return $this->render(
            'AppBundle:Organization:users.html.twig',
            array(
                "managers" => $managers,
                "members" => $members,
                "organization" => $organization,
                "organizations" => $this->get('organization')->cget(),
                "services" => $this->get('service')->cget(),
                "managers_buttons" => $managersButtons,
                "members_buttons" => $membersButtons,
                "inviteForm" => $form->createView(),
                "sendInEmailForm" => $sendInEmailForm->createView(),
                "admin" => $this->get('principal')->isAdmin()["is_admin"],
            )
        );
    }

    /**
     * @Route("/createInvitation/{id}")
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
            $invite = $invitationResource->sendInvitation($dataToBackend);

            $headers = $invite->getHeaders();

            try {
                $invitationId = basename(parse_url($headers['Location'][0], PHP_URL_PATH));
                $invitation = $invitationResource->get($invitationId);
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
     * @Route("/sendInvitation/{id}")
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
            $mailer = $this->get('mailer');
            $link = $data['link'];
            // TODO this->sendInvitations()
            try {
                $message = $mailer->createMessage()
                    ->setSubject($config['subject'])
                    ->setFrom($config['from'])
                    ->setCc($emails)
                    ->setReplyTo($config['reply-to'])
                    ->setBody(
                        $this->render(
                            'AppBundle:Organization:invitationEmail.txt.twig',
                            array(
                                'link' => $link,
                                'organization' => $organization,
                                'footer' => $config['footer'],
                                'role' => $role,
                                'message' => $data['message'],
                            )
                        ),
                        'text/plain'
                    );

                $mailer->send($message);
                $this->get('session')->getFlashBag()->add('success', 'Invitations sent succesfully.');
            } catch (\Exception $e) {
                $this->get('session')->getFlashBag()->add('error', 'Invitation sending failure. <br> Please send the invitation link manually to your partners. <br> The link is: <br><strong>'.$link.'</strong><br> The error was: <br> '.$e->getMessage());
            }

            return $this->redirect($this->generateUrl('app_organization_users', array("id" => $id)));
        }
    }


    /**
     * @Route("/resolveInvitationToken/{token}/{organizationid}/{landing_url}", defaults={"landing_url" = null})
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
            $invitationResource->accept($token);
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

        return $this->redirect($this->generateUrl('app_organization_show', array("id" => $organizationid)));
    }

    /**
     * @Route("/removeusers/{id}")
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
                $organizationResource->deleteMember($id, $pid);
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
     * @Route("/message/{id}")
     * @Template()
     * @return Response
     * @param   int     $id      Organization ID
     * @param   Request $request request
     */
    public function messageAction($id, Request $request)
    {
        try {
            // do something
            $this->get('session')->getFlashBag()->add('success', 'Siker');
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', 'Hiba a feldolgozás során');
            $this->get('logger')->error($e);
        }

        return $this->redirect($this->generateUrl('app_organization_users', array('id' => $id)));
    }

    /**
     * @Route("/propose/{id}")
     * @Template()
     * @return Response
     * @param   int     $id      Organization ID
     * @param   Request $request request
     */
    public function proposeAction($id, Request $request)
    {
        try {
            // do something
            $this->get('session')->getFlashBag()->add('success', 'Siker');
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', 'Hiba a feldolgozás során');
            $this->get('logger')->error($e);
        }

        return $this->redirect($this->generateUrl('app_organization_users', array('id' => $id)));
    }

    /**
     * @Route("/revoke/{id}")
     * @Template()
     * @return Response
     * @param   int     $id      Organization ID
     * @param   Request $request request
     */
    public function revokeAction($id, Request $request)
    {
        try {
            // do something
            $this->get('session')->getFlashBag()->add('success', 'Siker');
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', 'Hiba a feldolgozás során');
            $this->get('logger')->error($e);
        }

        return $this->redirect($this->generateUrl('app_organization_users', array('id' => $id)));
    }

    /**
     * @Route("/changerole/{id}")
     * @Template()
     * @return Response
     * @param   int     $id      Organization ID
     * @param   Request $request request
     */
    public function changeroleAction($id, Request $request)
    {
        try {
            // do something
            $this->get('session')->getFlashBag()->add('success', 'Siker');
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', 'Hiba a feldolgozás során');
            $this->get('logger')->error($e);
        }

        return $this->redirect($this->generateUrl('app_organization_users', array('id' => $id)));
    }

    /**
     * @Route("/roles/{id}")
     * @Template()
     * @return Response
     * @param   int $id Organization ID
     */
    public function rolesAction($id)
    {
        $organization = $this->getOrganization($id);
        $roles = $this->getRoles($organization);
        $rolesAccordion = $this->rolesToAccordion($roles);

        return $this->render(
            'AppBundle:Organization:roles.html.twig',
            array(
                "organization" => $organization,
                "organizations" => $this->get('organization')->cget(),
                "roles" => $roles,
                "services" => $this->get('service')->cget(),
                "roles_accordion" => $rolesAccordion,
                "admin" => $this->get('principal')->isAdmin()["is_admin"],
            )
        );
    }

    /**
     * @Route("/connectedservices/{id}")
     * @Template()
     * @return Response
     * @param int $id Organization Id
     *
     */
    public function connectedservicesAction($id)
    {
        $services = $this->get('service')->cget();
        $servicesAccordion = $this->servicesToAccordion($services);

        return $this->render(
            'AppBundle:Organization:connectedservices.html.twig',
            array(
                "organization" => $this->getOrganization($id),
                "organizations" => $this->get('organization')->cget(),
                "services" => $services,
                "services_accordion" => $servicesAccordion,
                "admin" => $this->get('principal')->isAdmin()["is_admin"],
            )
        );
    }

    /**
     * @Route("/delete/{id}")
     * @Template()
     * @return Response
     * @param int $id Organization Id
     *
     */
    public function deleteAction($id)
    {
        $organizationResource = $this->get('organization');
        $organizationResource->delete($id);

        return $this->redirectToRoute("homepage");
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
        return $this->get('organization')->get($id);
    }

    /**
     * @param $organization
     * @return mixed
     */
    private function getRoles($organization)
    {
        return $this->get('organization')->getRoles($organization['id'], 'expanded')['items'];
    }

    /**
     * @param $organization
     * @return mixed
     */
    private function getManagers($organization)
    {
        return $this->get('organization')->getManagers($organization['id'])['items'];
    }

    /**
     * @param $organization
     * @return mixed
     */
    private function getMembers($organization)
    {
        return $this->get('organization')->getMembers($organization['id'])['items'];
    }

    /**
     * @param $service
     * @return mixed
     */
    private function getEntitlementPack($service)
    {
        return $this->get('service')->getEntitlementPacks($service['id'])['items'];
    }

    /**
     * @param $id
     * @return mixed
     */
    private function getRole($id)
    {
        return $this->get('role')->get($id);
    }

    /**
     * @param $roles
     * @return array
     */
    private function rolesToAccordion($roles)
    {
        $rolesAccordion = array();

        foreach ($roles as $role) {
            $rolesAccordion[$role['id']]['title'] = $role['name'];
            $members = array();
            $permissions = array();

            foreach ($role['principals'] as $principal) {
                $members[] = $principal['principal']['display_name'];
            }
            foreach ($role['entitlements'] as $entitlement) {
                $permissions[] = $entitlement['name'];
            }

            $rolesAccordion[$role['id']]['contents'] = array(
                array(
                    'key' => 'Permissions',
                    'values' => $permissions,
                ),
                array(
                    'key' => 'Members',
                    'values' => $members,
                ),
            );
        }

        return $rolesAccordion;
    }

    /**
     * @param $services
     * @return array
     */
    private function servicesToAccordion($services)
    {
        $servicesAccordion = array();

        foreach ($services['items'] as $service) {
            $servicesAccordion[$service['id']]['title'] = $service['name'];
            $servicesAccordion[$service['id']]['description'] = 'Permission sets';
            $managers = $this->get('service')->getManagers($service['id'])['items'];
            $managersstring = "";
            foreach ($managers as $manager) {
                $managersstring .= $manager['display_name']." (".$manager['email'].") ";
            }
            $servicesAccordion[$service['id']]['titlemiddle'] = 'Service manager '.$managersstring;

            foreach ($this->getEntitlementPack($service) as $entitlementPack) {
                $servicesAccordion[$service['id']]['subaccordions'][$entitlementPack['id']]['title'] = $entitlementPack['name'];
                $servicesAccordion[$service['id']]['subaccordions'][$entitlementPack['id']]['variant'] = 'light';
                $servicesAccordion[$service['id']]['subaccordions'][$entitlementPack['id']]['contents'][] = array("key" => "Details", "values" => array($entitlementPack['description']));
                $servicesAccordion[$service['id']]['subaccordions'][$entitlementPack['id']]['buttons']['deleteEntitlementPack'] = array("icon" => "delete");

                $entitlementnames = array();
                foreach ($entitlementPack['entitlement_ids'] as $entitlementId) {
                    $entitlement = $this->get('entitlement')->get($entitlementId);
                    $entitlementnames[] = $entitlement['name'];
                }

                $servicesAccordion[$service['id']]['subaccordions'][$entitlementPack['id']]['contents'][] = array("key" => "Permissions", "values" => $entitlementnames);
            }
        }

        return $servicesAccordion;
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
        $emails = explode(',', preg_replace('/\s+/', '', $emails));
        $config = $this->getParameter('invitation_config');
        $mailer = $this->get('mailer');

        // create invitation

        $tokenResolverLink = $this->get('invitation')->createHexaaInvitation($organization['id'], $this->get('router'), $role['id']);
        $message = $mailer->createMessage()
            ->setSubject($config['subject'])
            ->setFrom($config['from'])
            ->setCc($emails)
            ->setReplyTo($config['reply-to'])
            ->setBody(
                $this->render(
                    'AppBundle:Organization:invitationEmail.txt.twig',
                    array(
                        'link' => $tokenResolverLink,
                        'organization' => $organization,
                        'footer' => $config['footer'],
                        'role' => $role,
                        'message' => $messageInMail,
                    )
                ),
                'text/plain'
            );

        $mailer->send($message);
    }
}
