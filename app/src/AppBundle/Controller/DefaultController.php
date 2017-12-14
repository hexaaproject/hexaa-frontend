<?php

namespace AppBundle\Controller;

use GuzzleHttp\Exception\ServerException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $organizations = [];
        $services = [];

        if ($this->getUser()) { // authenticated
            try {
                $organizations = $this->get('organization')->cget();
                $services = $this->get('service')->cget();
                $adminorno = $this->get('principal')->isAdmin();
                $admin = $adminorno["is_admin"];
            } catch (ServerException $e) {
                return $this->render('error.html.twig', array('serverexception' => $e));
            }
        }

        return $this->render(
            'AppBundle:Default:index.html.twig',
            array(
                'organizations' => $organizations,
                'services' => $services,
                'orgsubmenubox' => $this->getOrgSubmenuPoints(),
                'servsubmenubox' => $this->getServSubmenuPoints(),
                'admin' => $admin,
                'adminsubmenubox' => $this->getAdminSubmenuPoints(),
            )
        );
    }

    /**
     * @Route("/login")
     * @Template()
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("has_role('ROLE_USER')")
     */
    public function loginAction()
    {
        return $this->redirect($this->generateUrl('homepage'));
    }

    /**
     * @Route("/logout")
     * @Template()
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function logoutAction()
    {
        return $this->redirect($this->generateUrl('homepage'));
    }

    private function getOrgSubmenuPoints()
    {
        $submenuBox = array(
            "app_organization_properties" => "Properties",
            "app_organization_users" => "Users",
            "app_organization_roles" => "Roles",
            "app_organization_connectedservices" => "Conencted services",
        );

        return $submenuBox;
    }

    private function getServSubmenuPoints()
    {
        $submenuBox = array(
            "app_service_properties" => "Properties",
            "app_service_managers" => "Managers",
            "app_service_attributes" => "Attributes",
            "app_service_permissions" => "Permissions",
            "app_service_permissionssets" => "Permissions sets",
            "app_service_connectedorganizations" => "Connected organizations",
        );

        return $submenuBox;
    }


    /**
     * @return array
     */
    private function getAdminSubmenuPoints()
    {
        $submenubox = array(
            "app_admin_attributes" => "Attributes",
            "app_admin_principals" => "Principals",
            "app_admin_entity" => "Entity IDs",
            "app_admin_security" => "Security domains",
            "app_admin_contact" => "Contact",
        );

        return $submenubox;
    }
}
