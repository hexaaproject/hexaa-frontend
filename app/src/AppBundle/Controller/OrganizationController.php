<?php

namespace AppBundle\Controller;

use AppBundle\Form\ConnectServiceRequest1Type;
use AppBundle\Form\ConnectServiceRequest2Type;
use AppBundle\Form\OrganizationPropertiesType;
use AppBundle\Form\OrganizationRoleType;
use AppBundle\Form\OrganizationRoleUpdateType;
use AppBundle\Form\OrganizationUserInvitationSendEmailType;
use AppBundle\Form\OrganizationUserInvitationType;
use AppBundle\Form\OrganizationType;
use AppBundle\Form\OrganizationUserMessageManagerType;
use AppBundle\Form\OrganizationUserMessageType;
use AppBundle\Form\ConnectServiceType;
use AppBundle\Model\Organization;
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
            // $dataToBackend["service_token"], //TODO issue #103

            return $this->render('AppBundle:Organization:created.html.twig', array('neworg' => $this->get('organization')->get($orgid, "expanded")));
        }

        return $this->render('AppBundle:Organization:create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{id}/show")
     * @return Response
     * @param   int $id Organization ID
     */
    public function showAction($id)
    {
        $organization = $this->getOrganization($id);

        return $this->render(
            'AppBundle:Organization:show.html.twig',
            array(
                'entity_show_path' => $this->getEntityShowPath($organization),
                'entity' => $organization,
                'organizations' => $this->get('organization')->cget(),
                'services' => $this->get('service')->cget(),
                "admin" => $this->get('principal')->isAdmin()["is_admin"],
            )
        );
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

        $formProperties->handleRequest($request);

//        $formProperties->addError(new FormError("ERROR"));

        if ($formProperties->isSubmitted() && $formProperties->isValid()) {
            $data = $request->request->all();
            $modified = array(
                'name' => $data['organization_properties']['name'],
                'default_role' => $data['organization_properties']['default_role_id'],
                'description' => $data['organization_properties']['description'],
                'url' => $data['organization_properties']['url'],
            );
            $this->get('organization')->patch($id, $modified);

            return $this->redirect($request->getUri());
        }

        return $this->render(
            'AppBundle:Organization:properties.html.twig',
            array(
                'entity_show_path' => $this->getEntityShowPath($organization),
                "entity" => $organization,
                "propertiesbox" => $propertiesbox,
                "propertiesform" => $formProperties->createView(),
                "action" => $action,

                "roles" => $this->rolesToAccordion($roles, $id, false, false, $request),

                "organizations" => $this->get('organization')->cget(),
                "services" => $this->get('service')->cget(),
                "admin" => $this->get('principal')->isAdmin()["is_admin"],
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
                    'entity_show_path' => $this->getEntityShowPath($organization),
                    'entity' => $organization,

                    "managers" => $managers,
                    "members" => $members,

                    "organizations" => $this->get('organization')->cget(),
                    "services" => $this->get('service')->cget(),
                    "managers_buttons" => $managersButtons,
                    /*"members_buttons" => $membersButtons,*/
                    "invite_link" => $inviteLink,
                    "inviteForm" => $form->createView(),
                    "sendInEmailForm" => $sendInEmailForm->createView(),
                    "sendEmailForm" => $sendEmailForm->createView(),
                    /*"sendMemberEmailForm" => $sendMemberEmailForm->createView(),*/
                    "admin" => $this->get('principal')->isAdmin()["is_admin"],
                )
            );
        }

        return $this->render(
            'AppBundle:Organization:users.html.twig',
            array(
                'entity_show_path' => $this->getEntityShowPath($organization),
                'entity' => $organization,

                "managers" => $managers,
                "members" => $members,

                "organizations" => $this->get('organization')->cget(),
                "services" => $this->get('service')->cget(),
                "managers_buttons" => $managersButtons,
                /*"members_buttons" => $membersButtons,*/
                "inviteForm" => $form->createView(),
                "sendInEmailForm" => $sendInEmailForm->createView(),
                "sendEmailForm" => $sendEmailForm->createView(),
               /* "sendMemberEmailForm" => $sendMemberEmailForm->createView(),*/
                "admin" => $this->get('principal')->isAdmin()["is_admin"],
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
            $mailer = $this->get('mailer');
            $link = $data['link'];
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
       // dump($pids);exit;
        $emails = array();
        foreach ($pids as $pid) {
            $principal = $this->get('principals')->getById($pid);
            array_push($emails, $principal['email']);
        }
        $currentPrincipal = $this->get('principal')->getSelf();

        $form1->handleRequest($request);
        $form2->handleRequest($request);
        if ($form1->isValid() || $form2->isValid()) {
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
        $pids = $request->get('userId');
        $errors = array();
        $errormessages = array();
        foreach ($pids as $pid) {
            try {
                $this->get('organization')->addManager($id, $pid);
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
        $pids = $request->get('userId');
        $errors = array();
        $errormessages = array();
        foreach ($pids as $pid) {
            try {
                $this->get('organization')->deleteManager($id, $pid);
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
        if (! in_array($action, array("false", "create"))) {
            $this->createNotFoundException("Invalid action in url: ".$action);
        }

        $organization = $this->getOrganization($id);
        $roles = $this->getRoles($organization);
        $rolesAccordion = $this->rolesToAccordion($roles, $id, $action, $roleId, $request);

        $form = $this->createForm(
            OrganizationRoleType::class,
            array()
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            //create role
            $role = $this->get('organization')->createRole(
                $id,
                $data['name'],
                $this->get('role')
            );

            // put creator to role
            if ($data["wantToBeAMember"]) {
                $self = $this->get('principal')->getSelf("normal", $this->getUser()->getToken());
                $this->get('role')->putPrincipal($role['id'], $self['id']);
            }

            return $this->redirect($request->getUri());
        }

        return $this->render(
            'AppBundle:Organization:roles.html.twig',
            array(
                'entity_show_path' => $this->getEntityShowPath($organization),
                'entity' => $organization,

                "roles" => $roles,
                "roles_accordion" => $rolesAccordion,
                "action" => $action,

                "form" => $form->createView(),

                "organizations" => $this->get('organization')->cget(),
                "services" => $this->get('service')->cget(),
                "admin" => $this->get('principal')->isAdmin()["is_admin"],
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

        if (! in_array($action, array("false", "create"))) {
            $this->createNotFoundException("Invalid action in url: ".$action);
        }

        $form = $this->createForm(
            ConnectServiceType::class,
            array()
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $token = $data["token"];
            try {
                $this->get('organization')->connectService($id, $token);
            } catch (\Exception $e) {
                $this->get('session')->getFlashBag()->add('error', $e->getMessage());
            }

            return $this->redirect($request->getUri());
        }

        $services = array();
        $linkIDs = array();
        $links = null;
        if ($manager) {
            $links = $this->get('organization')->getLinks($id);
            foreach ($links['items'] as $link) {
                $service = $this->get('service')->get($link['service_id']);
                array_push($services, $service);
                array_push($linkIDs, $link['id']);
            }
        }

        $entitlementpacks = array();
        foreach ($linkIDs as $linkID) {
            $eps = $this->get('link')->getEntitlementPacks($linkID);
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
            $linksentitlements = $this->get('link')->getEntitlements($linkID);
            foreach ($linksentitlements['items'] as $linksentitlement) {
                if (count($linksentitlements) > 0) {
                    array_push($entitlements, $linksentitlement);
                }
            }
        }

        $principalentitlements = $this->get('principal')->getEntitlements();
        foreach ($entitlementsunique as $entitlementunique) {
            $entitlement = $this->get('entitlement')->get($entitlementunique);
            if (empty($entitlements)) {
            }
            //dump($entitlements);exit;
            if (!in_array($entitlement, $entitlements)) {
                array_push($entitlements, $entitlement);
            }
        }

        $servicesAccordion = null;
        if ($entitlementpacks != null && $entitlementpacks[0]['items'] != null) {
            $servicesAccordion = $this->servicesToAccordion($services, $entitlementpacks);
        }

        $entitlementsAccordion = null;
        if ($entitlements != null) {
            $entitlementsAccordion = $this->entitlementsToAccordion($services, $entitlements);
        }

        $organization = $this->get('organization')->get($id);

        return $this->render(
            'AppBundle:Organization:connectedservices.html.twig',
            array(
                'entity_show_path' => $this->getEntityShowPath($organization),
                'entity' => $organization,

                "organizations" => $this->get('organization')->cget(),
                "services" => $this->get('service')->cget(),
                "services_accordion" => $servicesAccordion,
                "entitlements_accordion" => $entitlementsAccordion,
                "action" => $action,
                "form" => $form->createView(),
                "admin" => $this->get('principal')->isAdmin()["is_admin"],
                "manager" => $manager,
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
        $organizationResource = $this->get('organization');
        $organizationResource->delete($id);

        return $this->redirectToRoute("homepage");
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
        $organizationResource = $this->get('role');
        $organizationResource->delete($id);
        $this->get('session')->getFlashBag()->add('success', 'The role has been deleted.');

        return $this->redirectToRoute("app_organization_roles", array("id" => $orgId));
    }



    /**
     * Get the history of the requested organization.
     * @Route("/{id}/history")
     * @Template()
     * @return array
     * @param int $id Organization Id
     */
    public function historyAction($id)
    {
        $organizationResource = $this->get('organization');
        $organization = $organizationResource->get($id);

        return array(
            'entity_show_path' => $this->getEntityShowPath($organization),
            'entity' => $organization,

            "organizations" => $this->get('organization')->cget(),
            "services" => $this->get('service')->cget(),
            "admin" => $this->get('principal')->isAdmin()["is_admin"],
        );
    }

    /**
     * @Route("/{id}/history/json")
     * @param string       $id       Organization id
     * @param integer|null $offset   Offset
     * @param integer      $pageSize Pagesize
     * @return array
     */
    public function historyJSONAction($id, $offset = null, $pageSize = 25)
    {
        $organizationResource = $this->get('organization');
        $principalResource = $this->get('principals');
        $data = $organizationResource->getHistory($id);
        $displayNames = array();
        for ($i = 0; $i < $data['item_number']; $i++) {
            $principalId = $data['items'][$i]['principal_id'];
            if ($principalId) {
                if (! array_key_exists($principalId, $displayNames)) {
                    $principal = $principalResource->getById($principalId);
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
    * @param $id
    * @return bool
    */
    private function isManager($id)
    {
        $manager = false;
        $organizations = $this->get('principal')->orgsWhereUserIsManager();
        foreach ($organizations as $oneorg) {
            if ($oneorg['id'] == $id) {
                $manager = true;
                break;
            }
        }

        return $manager;
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
     * @param array   $roles
     * @param int     $orgId
     * @param string  $action
     * @param int     $roleId
     * @param Request $request
     *
     * @return array
     */
    private function rolesToAccordion($roles, $orgId, $action, $roleId, Request $request)
    {
        $rolesAccordion = array();
        foreach ($roles as $role) {
            if (! array_key_exists('principals', $role)) {
                $role['principals'] = array();
            }
            $form =  $this->createForm(
                OrganizationRoleUpdateType::class,
                $role,
                array(
                    "action" => $this->generateUrl("app_organization_roles", array("id" => $orgId, "action" => "update", "roleId" => $role['id'])),
                )
            );


            $rolesAccordion[$role['id']]['title'] = $role['name'];
            $rolesAccordion[$role['id']]['deleteUrl'] = $this->generateUrl("app_organization_roledelete", array('orgId' => $orgId, 'id' => $role['id']));

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
                    'key'    => 'Permissions',
                    'values' => $permissions,
                ),
                array(
                    'key'    => 'Members',
                    'values' => $members,
                ),
            );

            if ($roleId == $role['id']) { // csak akkor dolgozzuk fel, ha erről a role-ról van szó.
                $form->handleRequest($request);
            }

            if ($form->isValid() and $form->isSubmitted()) {
                $data = $form->getData();
                $roleResource = $this->get('role');
                try {
                    $role = $roleResource->get($data['id']);
                    $this->amIManagerOfThis($role); //TODO
                    $role["name"] = $data["name"];

                    // persist role
                    $roleResource->patch($role['id'], $role);
                } catch (\AppBundle\Exception $exception) {
                    $form->addError(new FormError($exception->getMessage()));
                }
            }
            $rolesAccordion[$role['id']]['form'] = $form->createView();
        }

        return $rolesAccordion;
    }

    /**
     * @param $services
     * @param $entitlementPacks
     * @return array
     */
    private function servicesToAccordion($services, $entitlementPacks)
    {
        $servicesAccordion = array();
        foreach ($services as $service) {
            foreach ($entitlementPacks as $entitlementPacksub) {
                foreach ($entitlementPacksub['items'] as $entitlementPack) {
                    if ($entitlementPack['service_id'] == $service['id']) {
                        $servicesAccordion[$service['id']]['title'] = $service['name'];
                        $servicesAccordion[$service['id']]['description'] = 'Permission sets';
                        $managers = $this->get('service')->getManagers($service['id'])['items'];
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
                        foreach ($entitlementPack['entitlement_ids'] as $entitlementId) {
                            $entitlement = $this->get('entitlement')->get($entitlementId);
                            $entitlementnames[] = $entitlement['name'];
                        }

                        $servicesAccordion[$service['id']]['subaccordions'][$entitlementPack['id']]['contents'][] = array("key" => "Permissions", "values" => $entitlementnames);
                    }
                }
            }
        }

        return $servicesAccordion;
    }

    /**
     * @param $services
     * @param $entitlements
     * @return array
     */
    private function entitlementsToAccordion($services, $entitlements)
    {
        $entitlementsAccordion = array();
        foreach ($services as $service) {
            $entitlementsAccordion[$service['id']]['title'] = $service['name'];
            $entitlementsAccordion[$service['id']]['description'] = 'Entitlements';
            $managers = $this->get('service')->getManagers($service['id'])['items'];
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
