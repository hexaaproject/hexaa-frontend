<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("admin")
 */
class AdminController extends Controller
{
    /*
    * @Route("/")
    * @Template()
    * @return Response
    */
    public function indexAction()
    {
        $adminorno = $this->get('principal')->isAdmin();
        $admin = $adminorno["is_admin"];

        return $this->render(
            'AppBundle:Admin:index.html.twig',
            array(
                'admin' => $admin,
                'adminsubmenubox' => $this->getAdminSubmenupoints(),
            )
        );
    }

    /**
     * @Route("/attributes/{admin}")
     * @Template()
     * @param bool $admin
     * @return Response
     */
    public function attributesAction($admin)
    {
        $attributespecifications = $this->get('attribute_spec')->cget();
        dump($attributespecifications);
        return $this->render('AppBundle:Admin:attributes.html.twig',
            array(
                "organizations" => $this->get('organization')->cget(),
                "services" => $this->get('service')->cget(),
                "admin" => $this->get('principal')->isAdmin()["is_admin"],
                "submenu" => "true",
                'adminsubmenubox' => $this->getAdminSubmenupoints(),
                'attributes_accordion' => $this->attributesToAccordion($attributespecifications),
            )
        );
    }

    /**
     * @Route("/principals/{admin}")
     * @Template()
     * @param bool $admin
     * @return Response
     */
    public function principalsAction($admin)
    {
        return $this->render('AppBundle:Admin:principals.html.twig',
            array(
                "organizations" => $this->get('organization')->cget(),
                "services" => $this->get('service')->cget(),
                "admin" => $this->get('principal')->isAdmin()["is_admin"],
                "submenu" => "true",
                'adminsubmenubox' => $this->getAdminSubmenupoints(),
            )
        );
    }

    /**
     * @Route("/entity/{admin}")
     * @Template()
     * @param bool $admin
     * @return Response
     */
    public function entityAction($admin)
    {
        return $this->render('AppBundle:Admin:entity.html.twig',
            array(
                "organizations" => $this->get('organization')->cget(),
                "services" => $this->get('service')->cget(),
                "admin" => $this->get('principal')->isAdmin()["is_admin"],
                "submenu" => "true",
                'adminsubmenubox' => $this->getAdminSubmenupoints(),
            )
        );
    }

    /**
     * @Route("/security/{admin}")
     * @Template()
     * @param bool $admin
     * @return Response
     */
    public function securityAction($admin)
    {
        return $this->render('AppBundle:Admin:security.html.twig',
            array(
                "organizations" => $this->get('organization')->cget(),
                "services" => $this->get('service')->cget(),
                "admin" => $this->get('principal')->isAdmin()["is_admin"],
                "submenu" => "true",
                'adminsubmenubox' => $this->getAdminSubmenupoints(),
            )
        );
    }

    /**
     * @Route("/contact/{admin}")
     * @Template()
     * @param bool $admin
     * @return Response
     */
    public function contactAction($admin)
    {
        return $this->render('AppBundle:Admin:contact.html.twig',
            array(
                "organizations" => $this->get('organization')->cget(),
                "services" => $this->get('service')->cget(),
                "admin" => $this->get('principal')->isAdmin()["is_admin"],
                "submenu" => "true",
                'adminsubmenubox' => $this->getAdminSubmenupoints(),
            )
        );
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

    /**
     * @param $attributespecifications
     * @return array
     */
    private function attributesToAccordion($attributespecifications)
    {
        $attributesAccordion = array();
        foreach ($attributespecifications['items'] as $attributespecification) {
            $attributesAccordion[$attributespecification['id']]['title'] = $attributespecification['name'];
            $description = array();
            $uri = array();
            $syntax = array();
            $maintainer = array();
            $multivalue = array();
            array_push($description, $attributespecification['description']);
            array_push($uri, $attributespecification['uri']);
            array_push($syntax, $attributespecification['syntax']);
            array_push($maintainer, $attributespecification['maintainer']);
            array_push($multivalue, $attributespecification['is_multivalue']);
            $attributesAccordion[$attributespecification['id']]['contents'] = array(
                array(
                    'key' => 'Description',
                    'values' => $description,
                ),
                array(
                    'key' => 'URI',
                    'values' => $uri,
                ),
                array(
                    'key' => 'Syntax',
                    'values' => $syntax,
                ),
                array(
                    'key' => 'Maintainer',
                    'values' => $maintainer,
                ),
                array(
                    'key' => 'Multivalue',
                    'values' => $multivalue,
                ),
            );
        }
        return $attributesAccordion;
    }
}
