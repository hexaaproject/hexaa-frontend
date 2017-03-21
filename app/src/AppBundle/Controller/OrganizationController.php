<?php

namespace AppBundle\Controller;

use AppBundle\Form\OrganizationUserInvitationSendEmailType;
use AppBundle\Form\OrganizationUserInvitationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
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
     */
    public function indexAction()
    {
        $organizations = $this->get('organization')->cget();
        $services = $this->get('service')->cget();
        return $this->render(
            'AppBundle:Organization:index.html.twig',
            array(
                'organizations' => $organizations,
                'services' => $services
            )
        );
    }

    /**
     * @Route("/addStepOne")
     * @Template()
     */
    public function addStepOneAction(Request $request)
    {
        return $this->render('AppBundle:Organization:addStepOne.html.twig', array());
    }

    /**
     * @Route("/addStepTwo")
     * @Template()
     */
    public function addStepTwoAction(Request $request)
    {
        return $this->render('AppBundle:Organization:addStepTwo.html.twig', array());
    }

    /**
     * @Route("/addStepThree")
     * @Template()
     */
    public function addStepThreeAction(Request $request)
    {
        return $this->render('AppBundle:Organization:addStepThree.html.twig', array());
    }

    /**
     * @Route("/addStepFour")
     * @Template()
     */
    public function addStepFourAction(Request $request)
    {
        return $this->render('AppBundle:Organization:addStepFour.html.twig', array());
    }

    /**
     * @Route("/addStepFive")
     * @Template()
     */
    public function addStepFiveAction(Request $request)
    {
        return $this->render('AppBundle:Organization:addStepFive.html.twig', array());
    }


    /**
     * @Route("/addStepSix")
     * @Template()
     */
    public function addStepSixAction(Request $request)
    {
        $id = 1; // TODO, ez az org id lesz, miután rendesen sikeresen perzisztálódott az org, és ezt kaptuk vissza
        return $this->render('AppBundle:Organization:addStepSix.html.twig', array('id' => $id));
    }

    /**
     * @Route("/show/{id}")
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
            )
        );
    }

    /**
     * @Route("/properties/{id}")
     * @Template()
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
            "Default role" => "default_role_name"
        );

        return $this->render(
            'AppBundle:Organization:properties.html.twig',
            array(
                "organizations" => $this->get('organization')->cget(),
                "services" => $this->get('service')->cget(),
                "organization" => $organization,
                "roles" => $roles,
                "propertiesbox" => $propertiesbox
            )
        );
    }

    /**
     * @Route("/users/{id}")
     * @Template()
     */
    public function usersAction(Request $request, $id)
    {

        $organization = $this->getOrganization($id);

        $managers = $this->getManagers($organization);
        $members = $this->getMembers($organization);
        $managers_buttons = array(
            "changerole" => array(
                "class" => "btn-blue",
                "text" => "Change roles"
            ),
            "revoke" => array(
                "class" => "btn-blue",
                "text" => "Revoke"
            ),
            "message" => array(
                "class" => "btn-blue",
                "text" => "Message"
            ),
            "remove" => array(
                "class" => "btn-blue",
                "text" => "Remove"
            ),
            "invite" => array(
                "class" => "btn-red",
                "last" => true,
                "text" => '<i class="material-icons">add</i> Invite'
            ),
        );
        $members_buttons = array(
            "changerole" => array(
                "class" => "btn-blue",
                "text" => "Change roles"
            ),
            "proposal" => array(
                "class" => "btn-blue",
                "text" => "Proposal"
            ),
            "message" => array(
                "class" => "btn-blue",
                "text" => "Message"
            ),
            "remove" => array(
                "class" => "btn-blue",
                "text" => "Remove"
            ),
            "invite" => array(
                "class" => "btn-red",
                "last" => true,
                "text" => '<i class="material-icons">add</i> Invite'
            ),
        );


        $form = $this->createCreateInvitationForm($organization);
        $sendInEmailForm = $this->createForm(
            OrganizationUserInvitationSendEmailType::class,
            array(),
            array(
                "action" => $this->generateUrl("app_organization_sendinvitation", array("id" => $id)),
                "method" => "POST"
            )
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $data_to_backend = $data;
            $data_to_backend['organization'] = $id;
            $invitationResource = $this->get('invitation');
            $invite = $invitationResource->sendInvitation($data_to_backend);

            $headers = $invite->getHeaders();

            try {
                $invitationId = basename(parse_url($headers['Location'][0], PHP_URL_PATH));
                $invitation = $invitationResource->get($invitationId);
            } catch (\Exception $e) {
                throw $this->createNotFoundException('Invitation not found at backend');
            }

            $invite_link = $this->generateUrl('app_organization_resolveinvitationtoken', array("token" => $invitation['token'], "organizationid" => $id), UrlGeneratorInterface::ABSOLUTE_URL);
            // emails
            //$data_to_backend['emails'] = explode(',', preg_replace('/\s+/', '', $data['emails']));


            return $this->render(
                'AppBundle:Organization:users.html.twig',
                array(
                    "managers" => $managers,
                    "members" => $members,
                    "organization" => $organization,
                    "organizations" => $this->get('organization')->cget(),
                    "services" => $this->get('service')->cget(),
                    "managers_buttons" => $managers_buttons,
                    "members_buttons" => $members_buttons,
                    "invite_link" => $invite_link,
                    "inviteForm" => $form->createView(),
                    "sendInEmailForm" => $sendInEmailForm->createView()
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
                "managers_buttons" => $managers_buttons,
                "members_buttons" => $members_buttons,
                "inviteForm" => $form->createView(),
                "sendInEmailForm" => $sendInEmailForm->createView()
            )
        );
    }

    /**
     * @Route("/createInvitation/{id}")
     * @Method("POST")
     * @Template()
     */
    public function createInvitationAction(Request $request, $id)
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
                'placeholder' => 'To what role?'
            )
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $data_to_backend = $data;
            $invitationResource = $this->get('invitation');
            $data_to_backend['organization'] = $id;
            $invite = $invitationResource->sendInvitation($data_to_backend);

            $headers = $invite->getHeaders();

            try {
                $invitationId = basename(parse_url($headers['Location'][0], PHP_URL_PATH));
                $invitation = $invitationResource->get($invitationId);
            } catch (\Exception $e) {
                throw $this->createNotFoundException('Invitation not found at backend');
            }

            $invite_link = $this->generateUrl('app_organization_resolveinvitationtoken', array("token" => $invitation['token'], "organizationid" => $id), UrlGeneratorInterface::ABSOLUTE_URL);

            return new JsonResponse(array('link' => $invite_link), 200);
        }

        return new JsonResponse(
            array(
                'message' => 'Error',
                'form' => $this->renderView('AcsdfdfdfTODOmeBundle:Demo:form.html.twig',
                    array(
                        'form' => $form->createView(),
                    )
                )),
            400);
    }


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
                "landing_url" => $this->generateUrl('app_organization_show', array("id" => $organization['id']), UrlGeneratorInterface::ABSOLUTE_URL)
            ),
            array(
                "action" => $this->generateUrl("app_organization_createinvitation", array("id" => $organization['id'])),
                "method" => "POST"
            )
        );

        $form->add(
            'role',
            ChoiceType::class,
            array(
                "label" => false,
                'choices' => $roles,
                'required' => false,
                'placeholder' => 'To what role?'
            )
        );
        return $form;
    }

    /**
     * @Route("/sendInvitation/{id}")
     * @Method("POST")
     * @Template()
     */
    public function sendInvitationAction(Request $request, $id)
    {
        $organization = $this->getOrganization($id);
        $form = $this->createForm(OrganizationUserInvitationSendEmailType::class);

        $form->handleRequest($request);
        $data = $form->getData(); // TODO majd nem kell
        dump($request, $data);
        if ($form->isValid()) {
            $data = $form->getData();
            if (! $data['emails']) { // there is no email, we are done
                return $this->redirect($this->generateUrl('app_organization_users', array("id" => $id)));
            }
            $role = $this->getRole($data['role_id']);
            $emails = explode(',', preg_replace('/\s+/', '', $data['emails']));
            $config = $this->getParameter('invitation_config');
            $mailer = $this->get('mailer');
            $link = $data['link'];
            if ($data['landing_url']) {
                $link = $data['link'] . '?landing_url=' . $data['landing_url'];
            }
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
                            'message' => $data['message']
                        )
                    ),
                    'text/plain'
                );

            $mailer->send($message);

            $this->get('session')->getFlashBag()->add('success', 'Invitations sent succesful.');
            return $this->redirect($this->generateUrl('app_organization_show', array("id" => $id)));
        }
        dump($form);exit;

    }


    /**
     * @Route("/resolveInvitationToken/{token}/{organizationid}/{landing_url}")
     * @Template()
     */
    public function resolveInvitationTokenAction($token, $organizationid, $landing_url = null)
    {
        $invitationResource = $this->get('invitation');
        $invitationResource->accept($token);
        $this->get('session')->getFlashBag()->add('error', 'NEM TODO Hiba a feldolgozás során');
        if ($landing_url) {
            return $this->redirect($this->generateUrl($landing_url));
        }
        return $this->redirect($this->generateUrl('app_organization_show', array("id" => $organizationid)));
    }

    /**
     * @Route("/removeusers/{id}")
     * @Template()
     */
    public function removeusersAction($id, Request $request)
    {
        $pids = $request->get('userId');
        $organizationResource = $this->get('organization');
        $errors = array();
        foreach ($pids as $pid) {
            try {
                $organizationResource->deleteMember($id, $pid);
            } catch (\Exception $e) {
                $errors[] = $e;
            }
        }
        if (count($errors)) {
            $this->get('session')->getFlashBag()->add('error', 'Hiba a feldolgozás során');
            $this->get('logger')->error($errors);
        } else {
            $this->get('session')->getFlashBag()->add('success', 'Siker');
        }
        return $this->redirect($this->generateUrl('app_organization_users', array('id' => $id)));
    }

    /**
     * @Route("/message/{id}")
     * @Template()
     */
    public function messageAction($id, Request $request)
    {
        try {
            # do something
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
     */
    public function proposeAction($id, Request $request)
    {
        try {
            # do something
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
     */
    public function revokeAction($id, Request $request)
    {
        try {
            # do something
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
     */
    public function changeroleAction($id, Request $request)
    {
        try {
            # do something
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
     */
    public function rolesAction($id)
    {
        $organization = $this->getOrganization($id);
        $roles = $this->getRoles($organization);
        $roles_accordion = $this->rolesToAccordion($roles);

        return $this->render(
            'AppBundle:Organization:roles.html.twig',
            array(
                "organization" => $organization,
                "organizations" => $this->get('organization')->cget(),
                "roles" => $roles,
                "services" => $this->get('service')->cget(),
                "roles_accordion" => $roles_accordion
            )
        );
    }

    /**
     * @Route("/connectedservices/{id}")
     * @Template()
     */
    public function connectedservicesAction($id)
    {
        $services = $this->get('service')->cget();
        $services_accordion = $this->servicesToAccordion($services);

        return $this->render(
            'AppBundle:Organization:connectedservices.html.twig',
            array(
                "organization" => $this->getOrganization($id),
                "organizations" => $this->get('organization')->cget(),
                "services" => $services,
                "services_accordion" => $services_accordion
            )
        );
    }

    private function getOrganization($id)
    {
        return $this->get('organization')->get($id);
    }

    private function getRoles($organization)
    {
        return $this->get('organization')->getRoles($organization['id'], 'expanded')['items'];
    }

    private function getManagers($organization)
    {
        return $this->get('organization')->getManagers($organization['id'])['items'];
    }

    private function getMembers($organization)
    {
        return $this->get('organization')->getMembers($organization['id'])['items'];
    }

    private function getEntitlementPack($service)
    {
        return $this->get('service')->getEntitlementPacks($service['id'])['items'];
    }

    private function getRole($id)
    {
        return $this->get('role')->get($id);
    }

    private function rolesToAccordion($roles)
    {
        $roles_accordion = array();

        foreach ($roles as $role) {
            $roles_accordion[$role['id']]['title'] = $role['name'];
            $members = array();
            $permissions = array();

            foreach ($role['principals'] as $principal) {
                $members[] = $principal['principal']['display_name'];
            }
            foreach ($role['entitlements'] as $entitlement) {
                $permissions[] = $entitlement['name'];
            }

            $roles_accordion[$role['id']]['contents'] = array(
                array(
                    'key' => 'Permissions',
                    'values' => $permissions
                ),
                array(
                    'key' => 'Members',
                    'values' => $members
                )
            );
        }
        return $roles_accordion;
    }

    private function servicesToAccordion($services)
    {
        $services_accordion = array();

        foreach ($services['items'] as $service) {
            $services_accordion[$service['id']]['title'] = $service['name'];
            $services_accordion[$service['id']]['description'] = 'Permission sets';
            $managers = $this->get('service')->getManagers($service['id'])['items'];
            $managersstring = "";
            foreach ($managers as $manager) {
                $managersstring .= $manager['display_name'] . " (" . $manager['email'] . ") ";
            }
            $services_accordion[$service['id']]['titlemiddle'] = 'Service manager ' . $managersstring;

            foreach ($this->getEntitlementPack($service) as $entitlementPack) {
                $services_accordion[$service['id']]['subaccordions'][$entitlementPack['id']]['title'] = $entitlementPack['name'];
                $services_accordion[$service['id']]['subaccordions'][$entitlementPack['id']]['variant'] = 'light';
                $services_accordion[$service['id']]['subaccordions'][$entitlementPack['id']]['contents'][] = array("key" => "Details", "values" => array($entitlementPack['description']));
                $services_accordion[$service['id']]['subaccordions'][$entitlementPack['id']]['buttons']['deleteEntitlementPack'] = array("icon" => "delete");

                $entitlementnames = array();
                foreach ($entitlementPack['entitlement_ids'] as $entitlement_id) {
                    $entitlement = $this->get('entitlement')->get($entitlement_id);
                    $entitlementnames[] = $entitlement['name'];
                }

                $services_accordion[$service['id']]['subaccordions'][$entitlementPack['id']]['contents'][] = array("key" => "Permissions", "values" => $entitlementnames);

            }

        }
        return $services_accordion;
    }

}
