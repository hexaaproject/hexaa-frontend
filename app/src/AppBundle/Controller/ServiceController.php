<?php

namespace AppBundle\Controller;

use GuzzleHttp\Exception\ServerException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ServicePropertiesType;
use AppBundle\Form\ServiceOwnerType;
use AppBundle\Form\ServicePrivacyType;
use AppBundle\Form\ServiceAddAttributeSpecificationType;

/**
 * @Route("/service")
 */
class ServiceController extends Controller
{

    /**
     * @Route("/index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {

        // copy + paste from service.php
        try {
            $serviceId = $request->query->get('id');
            if ($request->query->has('menu')) {
                $menu = $request->query->get('menu');
            } else {
                $menu = 'main';
            }
            if ($serviceId) {
                $service = $this->get('service')->get($serviceId);
            } else {
                $service = null;
            }
            $organizations = $this->get('organization')->cget();

            $services = $this->get('service')->cget();

        } catch (ServerException $e) {
            return $this->render(
                'error.html.twig',
                array('serverexception' => $e)
            );
        }

        return $this->render(
            'AppBundle:Service:index.html.twig',
            array(
                'service' => $service,
                'organizations' => $organizations,
                'services' => $services,
                'menu' => $menu,
            )
        );
    }

    /**
     * @Route("/addStepOne")
     * @Template()
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addStepOneAction(Request $request)
    {
        return $this->render('AppBundle:Service:addStepOne.html.twig', array());
    }

    /**
     * @Route("/addStepTwo")
     * @Template()
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addStepTwoAction(Request $request)
    {
        $verbose = "expanded";
        $attributespecs = $this->get('attribute_spec')->cget($verbose);

        return $this->render(
            'AppBundle:Service:addStepTwo.html.twig',
            array(
                'attributes' => $attributespecs,
            )
        );
    }

    /**
     * @Route("/addStepThree")
     * @Template()
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addStepThreeAction(Request $request)
    {
        $verbose = "expanded";
        $permissionsset = $this->get('entitlement_pack')->getPublic($verbose)['items'];

        return $this->render('AppBundle:Service:addStepThree.html.twig');
    }

    /**
     * @Route("/addStepFour")
     * @Template()
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addStepFourAction(Request $request)
    {
        return $this->render('AppBundle:Service:addStepFour.html.twig', array());
    }

    /**
     * @Route("/addStepFive")
     * @Template()
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addStepFiveAction(Request $request)
    {
        return $this->render('AppBundle:Service:addStepFive.html.twig', array());
    }

    /**
     * @Route("/show/{id}")
     * @param integer $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        return $this->render(
            'AppBundle:Service:show.html.twig',
            array(
                'organizations' => $this->getOrganizations(),
                'services' => $this->getServices(),
                'service' => $this->getService($id),
                'servsubmenubox' => $this->getServSubmenuPoints(),
            )
        );
    }

    /**
     * @Route("/properties/{id}")
     * @Template()
     * @param integer $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function propertiesAction($id, Request $request)
    {

        $entityids = $this->get('service')->getEntityIds();
        $entityidskeys = array_keys($entityids);
        $choicearray = array();
        foreach ($entityidskeys as $entityID) {
            $choicearray[$entityID] = $entityID;
        }

        $propertiesDatas = array();
        $service = $this->getService($id);
        $propertiesDatas['serviceName'] = $service['name'];
        $propertiesDatas['serviceDescription'] = $service['description'];
        $propertiesDatas['serviceURL'] = $service['url'];
        $propertiesDatas['serviceSAML'] = $service['entityid'];
        $propertiesDatas['serviceOwnerName'] = $service['org_name'];
        $propertiesDatas['serviceOwnerDescription'] = $service['org_description'];
        $propertiesDatas['serviceOwnerURL'] = $service['org_url'];
        $propertiesDatas['serviceOwnerShortName'] = $service['org_short_name'];
        $propertiesDatas['servicePrivacyURL'] = $service['priv_url'];
        $propertiesDatas['servicePrivacyDescription'] = $service['priv_description'];
        $propertiesDatas['serviceEntityIDs'] = $choicearray;

        $formproperties = $this->createForm(
            ServicePropertiesType::class,
            array(
                'properties' => $propertiesDatas,
            )
        );

        $formproperties->handleRequest($request);

        if ($formproperties->isSubmitted() && $formproperties->isValid()) {
            $data = $request->request->all();
            $modified = array('name' => $data['service_properties']['serviceName'],
                'entityid' => $data['service_properties']['serviceSAML'], 'description' => $data['service_properties']['serviceDescription'],
                'url' => $data['service_properties']['serviceURL'], );
            $this->get('service')->patch($id, $modified);

            return $this->redirect($request->getUri());
        }

        $formowner = $this->createForm(
            ServiceOwnerType::class,
            array(
                'properties' => $propertiesDatas,
            )
        );

        $formowner->handleRequest($request);

        if ($formowner->isSubmitted() && $formowner->isValid()) {
            $data = $request->request->all();
            $modified = array('org_name' => $data['service_owner']['serviceOwnerName'],
                'org_short_name' => $data['service_owner']['serviceOwnerShortName'],
                'org_description' => $data['service_owner']['serviceOwnerDescription'],
                'org_url' => $data['service_owner']['serviceOwnerURL'], );
            $this->get('service')->patch($id, $modified);

            return $this->redirect($request->getUri());
        }

        $formprivacy = $this->createForm(
            ServicePrivacyType::class,
            array(
                'properties' => $propertiesDatas,
            )
        );

        $formprivacy->handleRequest($request);

        if ($formprivacy->isSubmitted() && $formprivacy->isValid()) {
            $data = $request->request->all();
            $modified = array('priv_url' => $data['service_privacy']['servicePrivacyURL'],
                'priv_description' => $data['service_privacy']['servicePrivacyDescription'], );
            $this->get('service')->patch($id, $modified);

            return $this->redirect($request->getUri());
        }

        return $this->render(
            'AppBundle:Service:properties.html.twig',
            array(
                'organizations' => $this->getOrganizations(),
                'services' => $this->getServices(),
                'service' => $this->getService($id),
                'main' => $this->getService($id),
                'propertiesbox' => $this->getPropertiesBox(),
                'privacybox' => $this->getPrivacyBox(),
                'ownerbox' => $this->getOwnerBox(),
                'servsubmenubox' => $this->getservsubmenupoints(),
                'propertiesform' => $formproperties->createView(),
                'ownerform' => $formowner->createView(),
                'privacyform' => $formprivacy->createView(),
            )
        );
    }

    /**
     * @Route("/managers/{id}")
     * @Template()
     * @param integer $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function managersAction($id)
    {
        $service = $this->getService($id);
        $managers = $this->getManagers($service);
        $managersButtons = array(
            "remove" => array(
                "class" => "btn-blue pull-left",
                "text" => "Remove",
            ),
            "invite" => array(
                "class" => "btn-red pull-right",
                "last" => true,
                "text" => '<i class="material-icons">add</i> Invite',
            ),
        );

        return $this->render(
            'AppBundle:Service:managers.html.twig',
            array(
                'organizations' => $this->getOrganizations(),
                'services' => $this->getServices(),
                'service' => $this->getService($id),
                'servsubmenubox' => $this->getServSubmenuPoints(),
                'managers' => $managers,
                'managers_buttons' => $managersButtons,
            )
        );
    }

    /**
     * @Route("/attributes/{id}")
     * @Template()
     * @param integer $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function attributesAction($id, Request $request)
    {
        $service = $this->getService($id);
        $attributes = $this->getServiceAttributes($service);
        $attributesButtons = array(
            "remove" => array(
                "class" => "btn-blue pull-left",
                "text" => "Remove",
            ),
            "add" => array(
                "class" => "btn-red pull-right",
                "last" => true,
                "text" => '<i class="material-icons">add</i> Add',
            ),
        );

        $form = $this->createAddAttributeSpecificationForm($service);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->all();
            dump($data);
            $added = $data['service_add_attribute_specification']['specname'];

            $this->get('service')->addAttributeSpec($id, $added);

            return $this->redirect($request->getUri());
        }

        return $this->render(
            'AppBundle:Service:attributes.html.twig',
            array(
                'organizations' => $this->getOrganizations(),
                'services' => $this->getServices(),
                'service' => $this->getService($id),
                'servsubmenubox' => $this->getServSubmenuPoints(),
                'attributes' => $attributes,
                'attributes_buttons' => $attributesButtons,
                'addAttributeSpecForm' => $form->createView(),
            )
        );
    }

    /**
     * @Route("/permissions/{id}")
     * @Template()
     * @param integer $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function permissionsAction($id)
    {
        $verbose = "expanded";
        $permissions = $this->get('service')->getEntitlements($id, $verbose)['items'];

        return $this->render(
            'AppBundle:Service:permissions.html.twig',
            array(
                'organizations' => $this->getOrganizations(),
                'servsubmenubox' => $this->getServSubmenuPoints(),
                'services' => $this->getServices(),
                'service' => $this->getService($id),
                'permissions_accordion' => $this->permissionsToAccordion($permissions),
            )
        );
    }

    /**
     * @Route("/permissionssets/{id}")
     * @Template()
     * @param integer $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function permissionssetsAction($id)
    {
        $permissionsset = $this->get('service')->getEntitlementPacks($id)['items'];

        return $this->render(
            'AppBundle:Service:permissionssets.html.twig',
            array(
                'organizations' => $this->getOrganizations(),
                'services' => $this->getServices(),
                'service' => $this->getService($id),
                'servsubmenubox' => $this->getServSubmenuPoints(),
                'permissions_accordion_set' => $this->permissionSetToAccordion($permissionsset),
            )
        );
    }

    /**
     * @Route("/connectedorganizations/{id}")
     * @Template()
     * @param integer $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function connectedOrganizationsAction($id)
    {
        return $this->render(
            'AppBundle:Service:connectedorganizations.html.twig',
            array(
                'organizations' => $this->getOrganizations(),
                'services' => $this->getServices(),
                'service' => $this->getService($id),
            )
        );
    }

    /**
     * @Route("/removemanagers/{id}")
     * @Template()
     * @param integer $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function removemanagersAction($id, Request $request)
    {
        $pids = $request->get('userId');
        dump($request);
        $serviceResource = $this->get('service');
        $errors = array();
        $errormessages = array();
        foreach ($pids as $pid) {
            try {
                $serviceResource->deleteMember($id, $pid);
                dump($serviceResource);
            } catch (\Exception $e) {
                $errors[] = $e;
                $errormessages[] = $e->getMessage();
            }
        }
        if (count($errors)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                implode(', ', $errormessages)
            );
            $this->get('logger')->error(
                'User remove failed'
            );
        }

        return $this->redirect($this->generateUrl('app_service_managers', array('id' => $id, )));
    }

    /**
     * @param $service
     * @return \Symfony\Component\Form\Form
     */
    private function createAddAttributeSpecificationForm($service)
    {
        $attributespecifications = array();
        $serviceattributespecifications = array();
        $verbose = "expanded";

        //service attribute specifications without names
        $serviceattributespecs = $this->get('service')->getAttributeSpecs($service['id'])['items'];

        foreach ($this->get('attribute_spec')->cget($verbose)['items'] as $attributespecification) {
            foreach ($serviceattributespecs as $serviceattributespec) {
                if ($attributespecification['id'] == $serviceattributespec['attribute_spec_id']) {
                    $serviceattributespecifications[$attributespecification['name']] = $serviceattributespec['attribute_spec_id'];
                }
            }
        }

        //all attribute specifications
        foreach ($this->get('attribute_spec')->cget($verbose)['items'] as $attributespecification) {
            $attributespecifications[$attributespecification['name']] = $attributespecification['id'];
        }
        dump($attributespecifications);

        //attribute specifications which don't belong to the service
        $result = array_diff(
            $attributespecifications,
            $serviceattributespecifications
        );

        $form = $this->createForm(
            ServiceAddAttributeSpecificationType::class,
            array('attributespecifications' => $result)
        );

        return $form;
    }

    /**
     * @Route("/removeattributes/{id}")
     * @Template()
     * @param integer $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function removeattributesAction($id, Request $request)
    {
        $asids = $request->get('attributespecId');
        dump($request);
        $serviceResource = $this->get('service');
        $errors = array();
        $errormessages = array();
        foreach ($asids as $asid) {
            try {
                $serviceResource->deleteAttributeSpec($id, $asid);
                dump($serviceResource);
            } catch (\Exception $e) {
                $errors[] = $e;
                $errormessages[] = $e->getMessage();
            }
        }
        if (count($errors)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                implode(', ', $errormessages)
            );
            $this->get('logger')->error('User remove failed');
        }

        return $this->redirect($this->generateUrl('app_service_attributes', array('id' => $id, )));
    }

    /**
     * @param $permissions
     * @return array
     */
    private function permissionsToAccordion($permissions)
    {
        $permissionsAccordion = array();
        foreach ($permissions as $permission) {
            $permissionsAccordion[$permission['id']]['title'] = $permission['name'];
            $description = array();
            $uri = array();
            array_push($description, $permission['description']);
            array_push($uri, $permission['uri']);
            $permissionsAccordion[$permission['id']]['contents'] = array(
                array(
                    'key' => 'Description',
                    'values' => $description,
                ),
                array(
                    'key' => 'URI',
                    'values' => $uri,
                ),
            );
        }

        return $permissionsAccordion;
    }

    /**
     * @param $permissionSets
     * @return array
     */
    private function permissionSetToAccordion($permissionSets)
    {
        $permissionsAccordionSet = array();
        foreach ($permissionSets as $permissionSet) {
            dump($permissionSet);
            $permissionsAccordionSet[$permissionSet['id']]['title'] = $permissionSet['name'];
            dump($permissionsAccordionSet);
            $description = array();
            $type = array();
            $permissions = array();
            array_push($description, $permissionSet['description']);
            array_push($type, $permissionSet['type']);
            foreach ($permissionSet['entitlement_ids'] as $entitlementid) {
                dump($entitlementid);
                $entitlement = $this->get('entitlement')->getEntitlement($entitlementid);
                array_push($permissions, $entitlement['name']);
            }
            $permissionsAccordionSet[$permissionSet['id']]['contents'] = array(
                array(
                    'key' => 'Description',
                    'values' => $description,
                ),
                array(
                    'key' => 'Type',
                    'values' => $type,
                ),
                array(
                    'key' => 'Permissions',
                    'values' => $permissions,
                ),
            );
        }

        return $permissionsAccordionSet;
    }

    /**
     * @param $service
     * @return mixed
     */
    private function getManagers($service)
    {
        return $this->get('service')->getManagers($service['id'])['items'];
    }

    /**
     * @param $service
     * @return array
     */
    private function getServiceAttributes($service)
    {
        $verbose = "expanded";
        $serviceattributespecs = $this->get('service')->getAttributeSpecs($service['id'])['items'];
        $attributestonames = array();
        foreach ($this->get('attribute_spec')->cget($verbose)['items'] as $attributespec) {
            foreach ($serviceattributespecs as $serviceattributespec) {
                if ($attributespec['id'] == $serviceattributespec['attribute_spec_id']) {
                    array_push($attributestonames, $attributespec);
                }
            }
        }

        return $attributestonames;
    }

    /**
     * @return mixed
     */
    private function getOrganizations()
    {
        $organization = $this->get('organization')->cget();

        return $organization;
    }

    /**
     * @return mixed
     */
    private function getServices()
    {
        $services = $this->get('service')->cget();

        return $services;
    }

    /**
     * @param $id
     * @return mixed
     */
    private function getService($id)
    {
        $service = $this->get('service')->get($id);

        return $service;
    }

    /**
     * @return array
     */
    private function getServSubmenuPoints()
    {
        $submenubox = array(
            "app_service_properties" => "Properties",
            "app_service_managers" => "Managers",
            "app_service_attributes" => "Attributes",
            "app_service_permissions" => "Permissions",
            "app_service_permissionssets" => "Permissions sets",
            "app_service_connectedorganizations" => "Connected organizations",
        );

        return $submenubox;
    }

    /**
     * @return array
     */
    private function getPropertiesBox()
    {
        $propertiesbox = array(
            "Name" => "name",
            "Description" => "description",
            "Home page" => "url",
            "SAML SP Entity ID" => "entityid",
        );

        return $propertiesbox;
    }

    /**
     * @return array
     */
    private function getPrivacyBox()
    {
        $propertiesbox = array(
            "URL" => "priv_url",
            "Description" => "priv_description",
        );

        return $propertiesbox;
    }

    /**
     * @return array
     */
    private function getOwnerBox()
    {
        $propertiesbox = array(
            "Name" => "org_name",
            "Short name" => "org_short_name",
            "Description" => "org_description",
            "Home page" => "org_url",
        );

        return $propertiesbox;
    }
}
