<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/service")
 */
class ServiceController extends Controller {

    /**
     * @Route("/index")
     */
    public function indexAction() {

        // copy + paste from service.php
        try {
            $serviceid = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            $menu = filter_input(INPUT_GET, 'menu');
            if (!$menu) {
                $menu = "main";
            }
            if ($serviceid) {
                $service = $this->get('service')->get($serviceid);
            }
            $organizations = $this->get('organization')->cget();

            $services = $this->get('service')->cget();
        } catch (ClientException $e) {
            return $this->render('error.html.twig', array('clientexception' => $e));
        } catch (ServerException $e) {
            return $this->render('error.html.twig', array('serverexception' => $e));
        } finally {
            //?
        }

        return $this->render('AppBundle:Service:index.html.twig', array(
                    'service' => $service,
                    'organizations' => $organizations,
                    'services' => $services,
                    'menu' => $menu
                        )
        );
    }

    /**
     * @Route("/show/{id}")
     */
    public function showAction($id) {
        return $this->render(
                        'AppBundle:Service:show.html.twig', array(
                    'organizations' => $this->getOrganizations(),
                    'services' => $this->getServices(),
                    'service' => $this->getService($id),
                    'servsubmenubox' => $this->getservsubmenupoints()
                        )
        );
    }

    /**
     * @Route("/properties/{id}")
     * @Template()
     */
    public function propertiesAction($id) {
        return $this->render(
                        'AppBundle:Service:properties.html.twig', array(
                    'organizations' => $this->getOrganizations(),
                    'services' => $this->getServices(),
                    'service' => $this->getService($id),
                    'main' => $this->getService($id),
                    'propertiesbox' => $this->getPropertiesBox(),
                    'servsubmenubox' => $this->getservsubmenupoints()
                        )
        );
    }

    private function getservsubmenupoints() {
        $submenubox = array(
            "app_service_properties" => "Properties",
            "app_service_managers" => "Managers",
            "app_service_attributes" => "Attributes",
            "app_service_permissions" => "Permissions",
            "app_service_permissionssets" => "Permissions sets",
            "app_service_connectedorganizations" => "Connected organizations"
        );

        return $submenubox;
    }

    private function getPropertiesBox() {
        $propertiesbox = array(
            "Name" => "name",
            "Description" => "description",
            "Home page" => "url",
            "SAML SP Entity ID" => "entityid",
            "Created" => "created_at",
            "Last updated" => "updated_at"
        );

        return $propertiesbox;
    }

    /**
     * @Route("/managers/{id}")
     * @Template()
     */
    public function managersAction($id) {
        $service = $this->getService($id);
        $managers = $this->getManagers($service);
        $managers_buttons = array(
            "remove" => array(
                "class" => "btn-blue pull-left",
                "text" => "Remove"
            ),
            "invite" => array(
                "class" => "btn-red pull-right",
                "last" => true,
                "text" => '<i class="material-icons">add</i> Invite'
            ),
        );
        return $this->render(
                        'AppBundle:Service:managers.html.twig', array(
                    'organizations' => $this->getOrganizations(),
                    'services' => $this->getServices(),
                    'service' => $this->getService($id),
                    'servsubmenubox' => $this->getservsubmenupoints(),
                    'managers' => $managers,
                    'managers_buttons' => $managers_buttons
                        )
        );
    }
    
    /**
     * @Route("/removemanagers/{id}")
     * @Template()
     */
    public function removemanagersAction($id, Request $request)
    {
        try {
            # do something
            $this->get('session')->getFlashBag()->add('success', 'Siker');
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', 'Hiba a feldolgozás során');
            $this->get('logger')->error($e);
        }
        return $this->redirect($this->generateUrl('app_service_managers', array('id' => $id)));
    }

    /**
     * @Route("/attributes/{id}")
     * @Template()
     */
    public function attributesAction($id) {
        $service = $this->getService($id);
        $attributes = $this->getServiceAttributes($service);
        $attributes_buttons = array(
            "change_attributes" => array(
                "class" => "btn-blue pull-left",
                "text" => "Remove"
            ),
            "add" => array(
                "class" => "btn-red pull-right",
                "last" => true,
                "text" => '<i class="material-icons">add</i> Add'
            ),
        );
        return $this->render(
                        'AppBundle:Service:attributes.html.twig', array(
                    'organizations' => $this->getOrganizations(),
                    'services' => $this->getServices(),
                    'service' => $this->getService($id),
                    'servsubmenubox' => $this->getservsubmenupoints(),
                    'attributes' => $attributes,
                    'attributes_buttons' => $attributes_buttons
                        )
        );
    }

    /**
     * @Route("/permissions/{id}")
     * @Template()
     */
    public function permissionsAction($id) {
        $verbose = "expanded";
        $permissions = $this->get('service')->entitlementsget($id, $verbose);
        return $this->render(
                        'AppBundle:Service:permissions.html.twig', array(
                    'organizations' => $this->getOrganizations(),
                    'servsubmenubox' => $this->getservsubmenupoints(),
                    'services' => $this->getServices(),
                    'service' => $this->getService($id),
                    'permissions_accordion' => $this->permissionsToAccordion($permissions)
                        )
        );
    }

    /**
     * @Route("/permissionssets/{id}")
     * @Template()
     */
    public function permissionssetsAction($id) {
        $verbose = "expanded";
        $permissionsset = $this->get('service')->entitlementpacksget($id, $verbose);
        return $this->render(
                        'AppBundle:Service:permissionssets.html.twig', array(
                    'organizations' => $this->getOrganizations(),
                    'services' => $this->getServices(),
                    'service' => $this->getService($id),
                    'servsubmenubox' => $this->getservsubmenupoints(),
                    'permissions_accordion_set' => $this->permissionsetToAccordion($permissionsset)
                        )
        );
    }

    /**
     * @Route("/connectedorganizations/{id}")
     * @Template()
     */
    public function connectedorganizationsAction($id) {
        return $this->render(
                        'AppBundle:Service:connectedorganizations.html.twig', array(
                    'organizations' => $this->getOrganizations(),
                    'services' => $this->getServices(),
                    'service' => $this->getService($id)
                        )
        );
    }

    private function permissionsToAccordion($permissions) {
        $permissions_accoordion = array();
        foreach ($permissions as $permission) {
            $permissions_accoordion[$permission['id']]['title'] = $permission['name'];
            $description = array();
            $uri = array();
            array_push($description, $permission['description']);
            array_push($uri, $permission['uri']);
            $permissions_accoordion[$permission['id']]['contents'] = array(
                array(
                    'key' => 'Description',
                    'values' => $description
                ),
                array(
                    'key' => 'URI',
                    'values' => $uri
                )
            );
        }
        return $permissions_accoordion;
    }

    private function permissionsetToAccordion($permissionsets) {
        $permissions_accoordion_set = array();
        foreach ($permissionsets as $permissionset) {
            $permissions_accoordion_set[$permissionset['id']]['title'] = $permissionset['name'];
            $description = array();
            $type = array();
            $permissions = array();
            array_push($description, $permissionset['description']);
            array_push($type, $permissionset['type']);
            foreach ($permissionset['entitlements'] as $entitlement) {
                $permissions[] = $entitlement['name'];
            }
            $permissions_accoordion_set[$permissionset['id']]['contents'] = array(
                array(
                    'key' => 'Description',
                    'values' => $description
                ),
                array(
                    'key' => 'Type',
                    'values' => $type
                ),
                array(
                    'key' => 'Permissions',
                    'values' => $permissions
                )
            );
        }
        return $permissions_accoordion_set;
    }

    private function getManagers($service) {
        return $this->get('service')->managersget($service['id']);
    }

    private function getServiceAttributes($service) {
        $verbose = "expanded";
        $serviceattributespecs = $this->get('service')->serviceattributesget($service['id']);
        $attributestonames = array();
        foreach ($serviceattributespecs as $serviceattributespec) {
            foreach ($this->get('service')->attributespecsget($verbose) as $attributespec) {
                if ($attributespec['id'] == $serviceattributespec['attribute_spec_id']) {
                    array_push($attributestonames, $attributespec);
                }
            }
        }
        return $attributestonames;
    }

    private function getOrganizations() {
        $organization = $this->get('organization')->cget();
        return $organization;
    }

    private function getServices() {
        $services = $this->get('service')->cget();
        return $services;
    }

    private function getService($id) {
        $service = $this->get('service')->get($id);
        return $service;
    }

}
