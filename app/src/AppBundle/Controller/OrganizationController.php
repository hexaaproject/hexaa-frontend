<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\OrganizationUserInvitationType;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @Route("/organization")
 */
class OrganizationController extends Controller {

    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction() {
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
    public function showAction($id) {
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
    public function usersAction($id)
    {
        // $inviteForm = new OrganizationUserInvitationType();
        $inviteForm = $this->createForm(OrganizationUserInvitationType::class);

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
                "inviteForm" => $inviteForm->createView()
            )
        );
    }

    /**
     * @Route("/removeusers/{id}")
     * @Template()
     */
    public function removeusersAction($id, Request $request)
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
