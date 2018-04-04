<?php

namespace AppBundle\Controller;

use AppBundle\Form\ConnectOrgType;
use AppBundle\Form\ModifyConnectOrgType;
use AppBundle\Model\Entitlement;
use GuzzleHttp\Exception\ServerException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use AppBundle\Form\ServicePropertiesType;
use AppBundle\Form\ServiceOwnerType;
use AppBundle\Form\ServicePrivacyType;
use AppBundle\Form\ServiceAddAttributeSpecificationType;
use AppBundle\Form\ServiceUserInvitationSendEmailType;
use AppBundle\Form\ServiceUserInvitationType;
use AppBundle\Form\ServiceType;
use AppBundle\Form\ServiceCreatePermissionType;
use AppBundle\Form\ServiceCreatePermissionSetType;
use AppBundle\Form\ServiceCreateEmailType;
use AppBundle\Form\ServicePermissionUpdateType;
use AppBundle\Form\ServicePermissionSetUpdateType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Devmachine\Bundle\FormBundle\Form\Type\TypeaheadType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use WebDriver\Exception;

/**
 * @Route("/service")
 */
class ServiceController extends BaseController
{

    /**
     * @Route("/index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');

        // copy + paste from service.php
        try {
            $serviceId = $request->query->get('id');
            if ($request->query->has('menu')) {
                $menu = $request->query->get('menu');
            } else {
                $menu = 'main';
            }
            if ($serviceId) {
                $service = $this->get('service')->get($hexaaAdmin, $serviceId);
            } else {
                $service = null;
            }
            $organizations = $this->get('organization')->cget($hexaaAdmin);

            $services = $this->get('service')->cget($hexaaAdmin);
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
                'manager' => "false",
                'organizationsWhereManager' => $this->orgWhereManager(),
                'hexaaHat' => $this->get('session')->get('hexaaHat'),
            )
        );
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
                'manager' => "false",
                'servsubmenubox' => $this->getServSubmenuPoints(),
                "admin" => $this->get('principal')->isAdmin($this->get('session')->get('hexaaAdmin'))["is_admin"],
                'organizationsWhereManager' => $this->orgWhereManager(),
                'hexaaHat' => $this->get('session')->get('hexaaHat'),
            )
        );
    }

    /**
     * @Route("/enable/{token}")
     * @param string  $token
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function enableAction($token, Request $request)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $tokenString = $request->getQueryString();
        $prefix = 'token=';
        $token = null;

        if (substr($tokenString, 0, strlen($prefix)) == $prefix) {
            $token = substr($tokenString, strlen($prefix));
        }

        try {
            $this->get('service')->enableService($hexaaAdmin, $token);

            $this->get('session')->getFlashBag()->add('success', 'Managed to enable the service!');

           /* $allService = $this->get('service')->getAll();
            $serviceIDs = array();
            foreach ($allService['items'] as $oneService) {
                array_push($serviceIDs, $oneService['id']);
            }

            $managersEmails = array();
            foreach ($serviceIDs as $serviceID) {
                $managers = $this->get('service')->getManagers($serviceID);
                foreach ($managers['items'] as $manager) {
                    if ( !in_array($manager['email'], $managersEmails)) {
                        array_push($managersEmails, $manager['email']);
                    }
                }
            }

            $config = $this->getParameter('invitation_config');
            $mailer = $this->get('mailer');
            $message = $mailer->createMessage()
                ->setSubject('The service is allowed')
                ->setFrom($config['from'])
                ->setCc($managersEmails)
                ->setReplyTo($config['reply-to'])
                ->setBody(
                    $this->render(
                        'AppBundle:Service:enabledServiceEmail.txt.twig',
                        array(
                            'footer' => $config['footer']
                        )
                    ),
                    'text/plain'
                );

            $mailer->send($message);
            $this->get('session')->getFlashBag()->add('success', 'The notification sends to service managers!');*/
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', 'Failed to enable the service! <br>'.$e->getMessage());
        }

        return $this->render(
            'AppBundle:Service:enable.html.twig',
            array(
                'organizations' => $this->getOrganizations(),
                'services' => $this->getServices(),
                'manager' => "false",
                "admin" => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
                'organizationsWhereManager' => $this->orgWhereManager(),
                'hexaaHat' => $this->get('session')->get('hexaaHat'),
            )
        );
    }

    /**
     * @Route("/create")
     * @Template()
     * @return \Symfony\Component\HttpFoundation\Response
     * @param   Request $request request
     * @param   bool    $click
     */
    public function createAction(Request $request, $click = "false")
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $services = $this->getServices();
        $servicesNames = array();
        foreach ($services['items'] as $service) {
            array_push($servicesNames, $service['name']);
        }

        $entityidsarray = array();
        $entityids = $this->get('entity_id')->cget($hexaaAdmin);
        $keys = array_keys($entityids['items']);
        foreach ($keys as $key) {
            $entityidsarray[$key] = $key;
        }

        $form = $this->createForm(ServiceType::class, $entityidsarray);

        $form->handleRequest($request);

        $emailForm = null;
        $click = "false";
        $clickback = "false";
        $firstpageerror = "false";

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                $dataToBackend = $data;
                $firstpageerror = "false";

           /* } catch (\Exception $exception) {
            $form->get('name')->addError(new FormError($exception->getMessage()));
          }*/

                foreach ($services['items'] as $service) {
                    if (strtolower($service['name']) == strtolower($dataToBackend["name"])) {
                        $form["name"]->addError(new FormError('Service name is case insensitive! This name already exists!'));
                        $firstpageerror = "true";
                    }
                }

                if (strlen($dataToBackend['name']) < 3) {
                    $form["name"]->addError(new FormError('This name of service has to be at least three character long!'));
                    $firstpageerror = "true";
                }

                if ($dataToBackend['entityid'] == null) {
                    $form["entityid"]->addError(new FormError('Invalid choosen!'));
                    $firstpageerror = "true";
                }


                $withoutAccent = $this->removeAccents($dataToBackend['entitlement']);
                $withoutAccentPlus1 = $this->removeAccents($dataToBackend['entitlementplus1']);
                $withoutAccentPlus2 = $this->removeAccents($dataToBackend['entitlementplus2']);
                $modifiedName = preg_replace("/[^a-zA-Z0-9-_:]+/", "", $withoutAccent);
                $modifiedNamePlus1 = preg_replace("/[^a-zA-Z0-9-_:]+/", "", $withoutAccentPlus1);
                $modifiedNamePlus2 = preg_replace("/[^a-zA-Z0-9-_:]+/", "", $withoutAccentPlus2);

                if ($modifiedName == null) {
                    $form["entitlement"]->addError(new FormError('First field must be fill out!'));
                    if ($firstpageerror == "false" && $click != "true") {
                        $click = "true";
                    }
                }

                if ($modifiedName != null) {
                    if (strlen($modifiedName) < 3) {
                        $form["entitlement"]->addError(new FormError('This name of entitlement has to be at least three character long!'));
                        if ($firstpageerror == "false" && $click != "true") {
                            $click = "true";
                        }
                    }
                }

                if ($modifiedNamePlus1 != null) {
                    if (strtolower($modifiedName) == strtolower($modifiedNamePlus1)) {
                         $form["entitlementplus1"]->addError(new FormError('Entitlement names are case-insensitive and letters with accent transformed into their proper letters without accent! Add different names to entitlements!'));
                        if ($firstpageerror == "false" && $click != "true") {
                            $click = "true";
                        }
                    }
                    if (strlen($modifiedNamePlus1) < 3) {
                        $form["entitlementplus1"]->addError(new FormError('This name of entitlement has to be at least three character long!'));
                        if ($firstpageerror == "false" && $click != "true") {
                            $click = "true";
                        }
                    }
                }

                if ($modifiedNamePlus2 != null) {
                    if (strtolower($modifiedName) == strtolower($modifiedNamePlus2)) {
                        $form["entitlementplus2"]->addError(new FormError('Entitlement names are case-insensitive and letters with accent transformed into their proper letters without accent! Add different names to entitlements!'));
                        if ($firstpageerror == "false" && $click != "true") {
                            $click = "true";
                        }
                    }
                    if (strlen($modifiedNamePlus2) < 3) {
                        $form["entitlementplus2"]->addError(new FormError('This name of entitlement has to be at least three character long!'));
                        if ($firstpageerror == "false" && $click != "true") {
                            $click = "true";
                        }
                    }
                }

                if ($modifiedNamePlus1 != null && $modifiedNamePlus2 != null) {
                    if (strtolower($modifiedNamePlus1) == strtolower($modifiedNamePlus2)) {
                        $form["entitlementplus2"]->addError(new FormError('Entitlement names are case-insensitive and letters with accent transformed into their proper letters without accent! Add different names to entitlements!'));
                        if ($firstpageerror == "false" && $click != "true") {
                            $click = "true";
                        }
                       // throw new \Exception();
                    }
                }

             /*   foreach ($form['name']->getErrors() as $key => $error) {
                     array_push($errors, $error->getMessage());
                }

                foreach ($form['url']->getErrors() as $key => $error) {
                    array_push($errors, $error->getMessage());
                }

                foreach ($form['description']->getErrors() as $key => $error) {
                    array_push($errors, $error->getMessage());
                }

                foreach ($form['entityid']->getErrors() as $key => $error) {
                    array_push($errors, $error->getMessage());
                }

                foreach ($form['entitlement']->getErrors() as $key => $error) {
                    array_push($errors, $error->getMessage());
                }

                foreach ($form['entitlementplus1']->getErrors() as $key => $error) {
                    array_push($errors, $error->getMessage());
                }

                foreach ($form['entitlementplus2']->getErrors() as $key => $error) {
                    array_push($errors, $error->getMessage());
                }*/

                foreach ($form->getErrors(true) as $error) {
                    //$form->addError(new FormError($error));
                    throw new \Exception();
                }


                /*  try {*/
                   /* if ($dataToBackend['entityid'] == null) {
                      $form["entityid"]->addError(new FormError('Invalid choosen!'));
                      $firstpageerror = "true";

                    }*/
              //dump($dataToBackend);exit;
                if ($dataToBackend['entityid'] != null) {
                    // dump($dataToBackend); exit;
                    $service = $this->get('service')->create(
                        $hexaaAdmin,
                        $dataToBackend["name"],
                        $dataToBackend["description"],
                        $dataToBackend["url"],
                        $dataToBackend["entityid"]
                    );
                }
                 /* } catch (\Exception $exception) {*/
                    //$firstpageerror = "true";
                  /*  $message = $exception->getMessage();
                    $partafterchildren = explode("\"children\":{", $message);
                    $errormessage = $this->get_string_between($partafterchildren[1], "[\"", "\"]");
                    if ( substr($partafterchildren[1], 0, 6) == "\"name\"") {
                        $form["name"]->addError(new FormError($errormessage));
                        $firstpageerror = "true";
                    }
                    dump($exception->getMessage());exit;*/
                   /* dump($partafterchildren);*/
                   /* $clickback = true;
                     dump($exception);exit;*/
                  /*  $this->get('session')
                      ->getFlashBag()
                      ->add('error', $exception->getMessage());*/
                  /*}*/
             /* if(count($form->getErrors(true)) == 0) {*/
                $servid = $service['id'];

                  //add manager to the service
                $self = $this->get('principal')->getSelf($hexaaAdmin, "normal", $this->getUser()->getToken());
                $this->get('service')->putManager($hexaaAdmin, $servid, $self['id']);
                $apiProperties = $this->get('service')->apget($hexaaAdmin);
                $uriPrefix = $apiProperties['entitlement_base'];
                  // create permission
                $permission = $this->get('service')->createPermission(
                    //$this->getParameter("hexaa_permissionprefix"),
                    $hexaaAdmin,
                    $uriPrefix,
                    $servid,
                    $modifiedName,
                    $dataToBackend['entitlement'],
                    null,
                    $this->get('entitlement')
                );

                  // create permissionset to permission
                $permissionset = $this->get('service')->createPermissionSet(
                    $hexaaAdmin,
                    $servid,
                    'default',
                    $this->get('entitlement_pack')
                );

                  //add permission to permissionset
                $this->get('entitlement_pack')->addPermissionToPermissionSet(
                    $hexaaAdmin,
                    $permissionset['id'],
                    $permission['id']
                );

                if ($dataToBackend['entitlementplus1'] != null) {
                    $permissionplus1 = $this->get('service')->createPermission(
                        //$this->getParameter("hexaa_permissionprefix"),
                        $hexaaAdmin,
                        $uriPrefix,
                        $servid,
                        $modifiedNamePlus1,
                        $dataToBackend['entitlementplus1'],
                        null,
                        $this->get('entitlement')
                    );

                    $this->get('entitlement_pack')->addPermissionToPermissionSet(
                        $hexaaAdmin,
                        $permissionset['id'],
                        $permissionplus1['id']
                    );
                }

                if ($dataToBackend['entitlementplus2'] != null) {
                    $permissionplus2 = $this->get('service')->createPermission(
                        //$this->getParameter("hexaa_permissionprefix"),
                        $hexaaAdmin,
                        $uriPrefix,
                        $servid,
                        $modifiedNamePlus2,
                        $dataToBackend['entitlementplus2'],
                        null,
                        $this->get('entitlement')
                    );

                    $this->get('entitlement_pack')->addPermissionToPermissionSet(
                        $hexaaAdmin,
                        $permissionset['id'],
                        $permissionplus2['id']
                    );
                }

                  //generate token to permissionset
                $permissionssets = $this->get('service')->getEntitlementPacks($hexaaAdmin, $servid, 'normal', 0, 10000)['items'];
                $permissionsetid = null;
                foreach ($permissionssets as $permissionset) {
                    if ($permissionset['name'] == 'default') {
                        $permissionsetid = $permissionset['id'];
                    }
                }

                $postarray = [
                    "service" => $servid,
                    "entitlement_packs" => [$permissionsetid],
                ];
                $response = $this->get('link')->post($hexaaAdmin, $postarray);
                $headers = $response->getHeader('Location');
                $headerspartsary = explode("/", $headers[0]);
                $getlink = $this->get('link')->getNewLinkToken($hexaaAdmin, array_pop($headerspartsary));

                return $this->redirect($this->generateUrl(
                    'app_service_createemail',
                    [
                      'servid' => $servid,
                      'token' => $getlink['token'],
                      'entity' => $dataToBackend["entityid"],
                      'click' => $click,
                      'clickback' => $clickback,
                      'firstpageerror' => $firstpageerror,
                    ]
                ));
            }
        } catch (\Appbundle\Exception $exception) {
            $form->addError(new FormError($exception->getMessage()));
            $this->get('session')->getFlashBag()->add('error', $exception->getMessage());
           // dump($form->getErrors(TRUE));
        } catch (\Exception $exception) {
          //dump($form->getErrors(TRUE));
           //$form->addError(new FormError($exception->getMessage()));
        }

        return $this->render(
            'AppBundle:Service:create.html.twig',
            array(
                'form' => $form->createView(),
                'organizations' => $this->getOrganizations(),
                'services' => $this->getServices(),
                'manager' => "false",
                "admin" => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
                'click' => $click,
                'clickback' => $clickback,
                'firstpageerror' => $firstpageerror,
                'organizationsWhereManager' => $this->orgWhereManager(),
                'hexaaHat' => $this->get('session')->get('hexaaHat'),
                )
        );
    }

    /**
     * @Route("/createEmail/{servid}/{token}")
     * @Template()
     * @return Response
     * @param   string  $servid  Service ID
     * @param   string  $token   generatedtoken
     * @param   Request $request request
     * @param   bool    $click
     */
    public function createEmailAction($servid, $token, Request $request, $click = "false")
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $service = $this->getService($servid);
        $entityids = $this->get('entity_id')->cget($hexaaAdmin);
        //type, email, surName
        $contacts = array();
        foreach ($entityids['items'] as $key => $value) {
            if ($key == $service['entityid']) {
                if (sizeof($value) >= 1) {
                    foreach ($value as $val) {
                        $contacts[$val['type'].' ('.$val['email'].')'] = $val['type'];
                    }
                }
            }
        }

        $form = $this->createForm(ServiceCreateEmailType::class, array('contacts' => $contacts));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $types = $data['contactType'];
            $contactsSelected = array();
            $contactOne = array();
            foreach ($entityids['items'] as $key => $value) {
                if ($key == $service['entityid']) {
                    if (sizeof($value) >= 1) {
                        foreach ($value as $val) {
                            foreach ($types as $type) {
                                if ($val['type'] == $type) {
                                    $contactOne['surName'] = $val['surName'];
                                    $contactOne['email'] = $val['email'];
                                    $contactOne['type'] = $val['type'];
                                    array_push($contactsSelected, $contactOne);
                                }
                            }
                        }
                    }
                }
            }
            $this->get('service')->notifySP($hexaaAdmin, $servid, $contactsSelected);

            return $this->render(
                'AppBundle:Service:created.html.twig',
                array(
                    'newserv' => $this->get('service')->get($hexaaAdmin, $servid, "expanded"),
                    'token' => $token,
                    'organizations' => $this->getOrganizations(),
                    'services' => $this->getServices(),
                    "admin" => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
                    'manager' => "false",
                    'click' => $click,
                    'organizationsWhereManager' => $this->orgWhereManager(),
                    'hexaaHat' => $this->get('session')->get('hexaaHat'),
                )
            );
        }


          return $this->render(
              'AppBundle:Service:createEmail.html.twig',
              array(
                  'emailForm' => $form->createView(),
                  'organizations' => $this->getOrganizations(),
                  'services' => $this->getServices(),
                  "admin" => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
                  'click' => $click,
                  'manager' => "false",
                  'organizationsWhereManager' => $this->orgWhereManager(),
                  'hexaaHat' => $this->get('session')->get('hexaaHat'),
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
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $entityids = $this->get('service')->getEntityIds($hexaaAdmin);
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

        $services = $this->get('service')->getAll();

        $formproperties = $this->createForm(
            ServicePropertiesType::class,
            array(
                'properties' => $propertiesDatas,
            )
        );

        $formproperties->handleRequest($request);

        try {
            if ($formproperties->isSubmitted() && $formproperties->isValid()) {
                $data = $request->request->all();

                foreach ($services['items'] as $service) {
                    if (strtolower($propertiesDatas['serviceName']) == strtolower($data['service_properties']['serviceName'])) {
                        break;
                    }

                    if (strtolower($service['name']) == strtolower($data['service_properties']['serviceName'])) {
                        throw new \Exception("Name is case insensitive! This modified service name already exists! Please, choose different name!");
                    }
                }

                if (strlen($data['service_properties']['serviceName']) < 3) {
                    throw new \Exception("Service name must be at least three character long!");
                }

                $modified = array('name' => $data['service_properties']['serviceName'],
                    'entityid' => $data['service_properties']['serviceSAML'], 'description' => $data['service_properties']['serviceDescription'],
                    'url' => $data['service_properties']['serviceURL'], );
                $this->get('service')->patch($hexaaAdmin, $id, $modified);

                return $this->redirect($request->getUri());
            }
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', $e->getMessage());
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
            $this->get('service')->patch($hexaaAdmin, $id, $modified);

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
            $this->get('service')->patch($hexaaAdmin, $id, $modified);

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
                "admin" => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
                'organizationsWhereManager' => $this->orgWhereManager(),
                'manager' => "false",
                'hexaaHat' => $this->get('session')->get('hexaaHat'),
            )
        );
    }

    /**
     * @Route("/managers/{id}")
     * @Template()
     * @param integer $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function managersAction($id, Request $request)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
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

        $form = $this->createCreateInvitationForm($service);
        $sendInEmailForm = $this->createForm(
            ServiceUserInvitationSendEmailType::class,
            array(),
            array(
                "action" => $this->generateUrl("app_service_sendinvitation", array("id" => $id)),
                "method" => "POST",
            )
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $dataToBackend = $data;
            $dataToBackend['service'] = $id;
            $invitationResource = $this->get('invitation');
            $invite = $invitationResource->sendInvitation($hexaaAdmin, $dataToBackend);

            $headers = $invite->getHeaders();

            try {
                $invitationId = basename(parse_url($headers['Location'][0], PHP_URL_PATH));
                $invitation = $invitationResource->get($hexaaAdmin, $invitationId);
            } catch (\Exception $e) {
                throw $this->createNotFoundException('Invitation not found at backend');
            }

            $inviteLink = $this->generateUrl('app_service_resolveinvitationtoken', array("token" => $invitation['token'], "serviceid" => $id), UrlGeneratorInterface::ABSOLUTE_URL);

            return $this->render(
                'AppBundle:Service:managers.html.twig',
                array(
                    'organizations' => $this->getOrganizations(),
                    'services' => $this->getServices(),
                    'service' => $this->getService($id),
                    'servsubmenubox' => $this->getServSubmenuPoints(),
                    'managers' => $managers,
                    'managers_buttons' => $managersButtons,
                    "invite_link" => $inviteLink,
                    "inviteForm" => $form->createView(),
                    "sendInEmailForm" => $sendInEmailForm->createView(),
                    "admin" => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
                    'organizationsWhereManager' => $this->orgWhereManager(),
                    'manager' => "false",
                    'ismanager' => "true",
                    'hexaaHat' => $this->get('session')->get('hexaaHat'),
                )
            );
        }

        return $this->render(
            'AppBundle:Service:managers.html.twig',
            array(
                'organizations' => $this->getOrganizations(),
                'services' => $this->getServices(),
                'service' => $this->getService($id),
                'servsubmenubox' => $this->getServSubmenuPoints(),
                'managers' => $managers,
                'managers_buttons' => $managersButtons,
                "inviteForm" => $form->createView(),
                "sendInEmailForm" => $sendInEmailForm->createView(),
                "admin" => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
                'organizationsWhereManager' => $this->orgWhereManager(),
                'manager' => "false",
                'ismanager' => "true",
                'hexaaHat' => $this->get('session')->get('hexaaHat'),
            )
        );
    }

    /**
     * @Route("/sendInvitation/{id}")
     * @Method("POST")
     * @Template()
     * @return Response
     * @param   int     $id      Service ID
     * @param   Request $request request
     */
    public function sendInvitationAction($id, Request $request)
    {
        $service = $this->getService($id);
        $form = $this->createForm(ServiceUserInvitationSendEmailType::class);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            if (!$data['emails']) { // there is no email, we are done
                return $this->redirect($this->generateUrl('app_service_managers', array("id" => $id)));
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
                            'AppBundle:Service:invitationEmail.txt.twig',
                            array(
                                'link' => $link,
                                'service' => $service,
                                'footer' => $config['footer'],
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

            return $this->redirect($this->generateUrl('app_service_managers', array("id" => $id)));
        }
    }

    /**
     * @Route("/resolveInvitationToken/{token}/{serviceid}/{landing_url}", defaults={"landing_url" = null})
     * @Template()
     * @return Response
     * @param string $token      Invitation token
     * @param int    $serviceid  Service ID
     * @param string $landingUrl Url to redirect after accept invitation
     */
    public function resolveInvitationTokenAction($token, $serviceid, $landingUrl = null)
    {
        $invitationResource = $this->get('invitation');
        try {
            $invitationResource->accept($this->get('session')->get('hexaaAdmin'), $token);
        } catch (\Exception $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            switch ($statusCode) {
                case '409':
                    return array("error" => "You are already manager of this service.");
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

        return $this->redirect($this->generateUrl('app_service_show', array("id" => $serviceid)));
    }


    /**
     * @Route("/removemanagers/{id}")
     * @Template()
     * @param integer $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removemanagersAction($id, Request $request)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $pids = $request->get('userId');
        $serviceResource = $this->get('service');
        $errors = array();
        $errormessages = array();
        foreach ($pids as $pid) {
            try {
                $serviceResource->deleteMember($hexaaAdmin, $id, $pid);
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
     * @Route("/createInvitation/{id}")
     * @Method("POST")
     * @Template()
     * @return Response
     * @param   int     $id      Service ID
     * @param   Request $request request
     */
    public function createInvitationAction($id, Request $request)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(array('message' => 'You can access this only using Ajax!'), 400);
        }
        $service = $this->getService($id);
        $form = $this->createForm(ServiceUserInvitationType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $dataToBackend = $data;

            // TODO invitation->createHexaaInvitation()
            // TODO this->sendInvitations()

            $invitationResource = $this->get('invitation');
            $dataToBackend['service'] = $id;
            $invite = $invitationResource->sendInvitation($hexaaAdmin, $dataToBackend);

            $headers = $invite->getHeaders();

            try {
                $invitationId = basename(parse_url($headers['Location'][0], PHP_URL_PATH));
                $invitation = $invitationResource->get($hexaaAdmin, $invitationId);
            } catch (\Exception $e) {
                throw $this->createNotFoundException('Invitation not found at backend');
            }

            $landingUrl = null;
            if (!empty($data['landing_url'])) {
                $landingUrl = urlencode($data['landing_url']);
            }
            $inviteLink = $this->generateUrl('app_service_resolveinvitationtoken', array("token" => $invitation['token'], "serviceid" => $id, "landing_url" => $landingUrl), UrlGeneratorInterface::ABSOLUTE_URL);

            return new JsonResponse(array('link' => $inviteLink), 200);
        }
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
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
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
            $added = $data['service_add_attribute_specification']['specname'];

            $this->get('service')->addAttributeSpec($hexaaAdmin, $id, $added);

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
                "admin" => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
                'organizationsWhereManager' => $this->orgWhereManager(),
                'manager' => "false",
                'ismanager' => "true",
                'hexaaHat' => $this->get('session')->get('hexaaHat'),
            )
        );
    }

    /**
     * @Route("/removeattributes/{id}")
     * @Template()
     * @param integer $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeattributesAction($id, Request $request)
    {
        $asids = $request->get('attributespecId');
        $serviceResource = $this->get('service');
        $errors = array();
        $errormessages = array();
        foreach ($asids as $asid) {
            try {
                $serviceResource->deleteAttributeSpec($this->get('session')->get('hexaaAdmin'), $id, $asid);
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
     * @Route("/permissions/{id}/{permissionId}/{action}", defaults={"permissionId" : false, "action" : null})
     * @Template()
     * @param integer     $id
     * @param Request     $request
     * @param integer     $permissionId
     * @param string|null $action
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function permissionsAction($id, Request $request, $permissionId, $action = null)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $apiProperties = $this->get('service')->apget($hexaaAdmin);
        $uriPrefix = $apiProperties['entitlement_base'];

        $verbose = "expanded";
        $allpermission = $this->get('service')->getEntitlements($hexaaAdmin, $id, $verbose);
        $allallpermission = $this->get('service')->getEntitlements($hexaaAdmin, $id, $verbose, 0, 100000);
        $totalnumber = $allpermission['item_number'];
        $permissions = $allpermission['items'];
        $totalpages = ceil($totalnumber / 25);
        $offset = 25;
        $pagesize = 25;
        $verbose = "normal";
        $permissionsperpage = array();
        array_push($permissionsperpage, $permissions);
        for ($i = 1; $i < $totalpages; $i++) {
            $permissionperpage = $this->get('service')->getEntitlements($hexaaAdmin, $id, $verbose, $offset, $pagesize);
            array_push($permissionsperpage, $permissionperpage['items']);
            $offset = $offset +25;
        }

        $permissionsaccordion = $this->permissionsToAccordion($permissionsperpage, $id, $permissionId, $action, $request);
        if (false === $permissionsaccordion) { // belső form rendesen le lett kezelve, vissza az alapokhoz
            return $this->redirectToRoute('app_service_permissions', array("id" => $id));
        }

        $formCreatePermissions = $this->createForm(
            ServiceCreatePermissionType::class
        );

        $formCreatePermissions->handleRequest($request);
        $error = 'false';

        try {
            if ($formCreatePermissions->isSubmitted() && $formCreatePermissions->isValid()) {
                $data = $request->request->all();
                $withoutAccent = $this->removeAccents($data['service_create_permission']['permissionURL']);
                $modifiedName = preg_replace("/[^a-zA-Z0-9-_:]+/", "", $withoutAccent);

                if (strlen($modifiedName) < 3) {
                    throw new \Exception('URI postfix must be at least 3 character long!');
                }
                foreach ($permissions as $permission) {
                    if ($permission['uri'] == $uriPrefix.":".$id.":".$modifiedName) {
                        throw new \Exception('URI must be unique!');
                    }
                    if (strtolower($permission['name']) == strtolower($data['service_create_permission']['permissionName'])) {
                        throw new \Exception('Permission name is case insensitive! It must be unique!');
                    }
                }

                $permisson = array(
                    'name' => $data['service_create_permission']['permissionName'],
                    'uri' => $data['service_create_permission']['permissionURL'],
                    'description' => $data['service_create_permission']['permissionDescription'],
                );
                $this->get('service')->createPermission($hexaaAdmin, $uriPrefix, $id, $modifiedName, $permisson['name'], $permisson['description'], $this->get('entitlement'));
                $this->get('session')->getFlashBag()->add('success', 'Permission created succesfully.');

                return $this->redirect($request->getUri());
            }
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', $e->getMessage());
            $error = 'true';
        }

        return $this->render(
            'AppBundle:Service:permissions.html.twig',
            array(
                'organizations' => $this->getOrganizations(),
                'servsubmenubox' => $this->getServSubmenuPoints(),
                'services' => $this->getServices(),
                'service' => $this->getService($id),
                'permissions_accordion' => $permissionsaccordion,
                'allpermissions_accordion' => $this->allpermissionsToAccordion($allallpermission, $id),
                'total_number' => $totalnumber,
                'total_pages' => $totalpages,
                'admin' => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
                'formCreatePermission' => $formCreatePermissions->createView(),
                'action' => $action,
                'uriprefix' => $uriPrefix,
                'serviceID' => $id,
                'permissions' => $allallpermission,
                'error' => $error,
                'organizationsWhereManager' => $this->orgWhereManager(),
                'manager' => "false",
                'ismanager' => $this->isManager($id),
                'hexaaHat' => $this->get('session')->get('hexaaHat'),
            )
        );
    }

    /**
     * @Route("/permissionssets/{id}/{permissionsetId}/{action}/{token}/{permissionsetname}",  defaults={"permissionsetId" : false, "action" : false})
     * @Template()
     * @param Request $request
     * @param integer $id
     * @param integer $permissionsetId
     * @param string  $action
     * @param string  $token
     * @param string  $permissionsetname
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function permissionssetsAction(Request $request, $id, $permissionsetId, $action, $token = null, $permissionsetname = null)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $manager = $this->isManager($id);
        if ($hexaaAdmin == "true") {
            $manager = "true";
        }
        $service = $this->get('service');
        $allpermissionset = $service->getEntitlementPacks($hexaaAdmin, $id);
        $permissionssets = $allpermissionset['items'];
        $totalnumber = $allpermissionset['item_number'];
        $totalpages = ceil($totalnumber / 25);
        $offset = 25;
        $pagesize = 25;
        $verbose = "normal";
        $permissionsetsperpage = array();
        $allallpermissionset = $service->getEntitlementPacks($hexaaAdmin, $id, $verbose, 0, 10000);
        array_push($permissionsetsperpage, $permissionssets);
        for ($i = 1; $i < $totalpages; $i++) {
            $permissionsetperpage = $service->getEntitlementPacks($hexaaAdmin, $id, $verbose, $offset, $pagesize);
            array_push($permissionsetsperpage, $permissionsetperpage['items']);
            $offset = $offset +25;
        }

        $permissions = array();
        $servicepermissions = $service->getEntitlements($hexaaAdmin, $id, $verbose, 0, 10000);
        foreach ($servicepermissions['items'] as $servicepermission) {
            $permissions[$servicepermission['id']] = $servicepermission['name'];
        }

        $permissionsetaccordion = $this->permissionSetToAccordion($permissionsetsperpage, $id, $permissionsetId, $action, $request);

        if (false === $permissionsetaccordion) { // belső form rendesen le lett kezelve, vissza az alapokhoz
            return $this->redirectToRoute('app_service_permissionssets', array("id" => $id));
        }

        $formCreatePermissionsSet = $this->createForm(
            ServiceCreatePermissionSetType::class,
            array(
                'permissions' => $permissions,
            )
        );

        $formCreatePermissionsSet->handleRequest($request);
        $error = "false";

        try {
            if ($formCreatePermissionsSet->isSubmitted() && $formCreatePermissionsSet->isValid()) {
                $data = $request->request->all();

                if (strlen($data['service_create_permission_set']['permissionSetName']) < 3) {
                    throw new \Exception('Name must be at least 3 character long!');
                }
                foreach ($permissionssets as $permissionset) {
                    if (strtolower($permissionset['name']) == strtolower($data['service_create_permission_set']['permissionSetName'])) {
                        throw new \Exception('Permission set name is case insensitive! It must be unique!');
                    }
                }

                $permissionids = [];
                if (count($data['service_create_permission_set']['permissions']) != 0) {
                    $iter = 0;
                    $apiProperties = $this->get('service')->apget($hexaaAdmin);
                    foreach (array_unique($data['service_create_permission_set']['permissions']) as $permission) {
                        $iter = 0;
                        foreach ($servicepermissions['items'] as $servicepermission) {
                            if ($servicepermission['name'] == $permission) {
                                array_push($permissionids, $servicepermission['id']);
                                break;
                            } else {
                                $iter++;
                            }
                        }
                        if ($iter == $servicepermissions['item_number']) {
                            $withoutAccent = $this->removeAccents($permission);
                            $modifiedName = preg_replace("/[^a-zA-Z0-9-_:]+/", "", $withoutAccent);
                            $newpermission = $service->createPermission($hexaaAdmin, $apiProperties['entitlement_base'], $id, $modifiedName, $permission, null, $this->get('entitlement'));
                            array_push($permissionids, $newpermission['id']);
                        }
                    }
                }

                $permissonSet = array(
                    'name' => $data['service_create_permission_set']['permissionSetName'],
                    'type' => $data['service_create_permission_set']['permissionSetType'],
                    'description' => $data['service_create_permission_set']['permissionSetDescription'],
                );

                if (count($data['service_create_permission_set']['permissions']) != 0) {
                    $entitlementpack = $service->postPermissionSet($hexaaAdmin, $id, $permissonSet, $this->get('entitlement_pack'));

                    foreach ($permissionids as $permissionid) {
                        $this->get('entitlement_pack')->addPermissionToPermissionSet($hexaaAdmin, $entitlementpack['id'], $permissionid);
                    }
                }
                $this->get('session')->getFlashBag()->add('success', 'Permission set created succesfully.');

                return $this->redirect($request->getUri());
            }
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', $e->getMessage());
            $error = "true";
        }

        return $this->render(
            'AppBundle:Service:permissionssets.html.twig',
            array(
                'organizations' => $this->getOrganizations(),
                'services' => $this->getServices(),
                'service' => $this->getService($id),
                'servsubmenubox' => $this->getServSubmenuPoints(),
                'permissions_accordion_set' => $permissionsetaccordion,
                'allpermissions_accordion_set' => $this->allpermissionSetToAccordion($allallpermissionset, $id),
                'total_pages' => $totalpages,
                'total_number' => $totalnumber,
                'token' => $token,
                'permissionsetname' => $permissionsetname,
                'admin' => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
                'formCreatePermissionsSet' => $formCreatePermissionsSet->createView(),
                'permissionsets' => $allallpermissionset,
                'permissions' => $permissions,
                'error' => $error,
                'organizationsWhereManager' => $this->orgWhereManager(),
                'manager' => "false",
                'ismanager' => $manager,
                'hexaaHat' => $this->get('session')->get('hexaaHat'),
            )
        );
    }

    /**
     * @Route("/connectedorganizations/{id}/{token}", defaults = {"token" = null})
     * @Template()
     * @param integer $id
     * @param string  $token
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function connectedOrganizationsAction($id, $token, Request $request)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $manager = $this->isManager($id);
        if ($hexaaAdmin == "true") {
            $manager = "true";
        }

        $requestslinks = $this->get('service')->getLinkRequests($hexaaAdmin, $id);

        $allData = array();
        foreach ($requestslinks['items'] as $requestlink) {
            $allData[$requestlink['organization_id']]['name'] = $this->get('organization')->get($hexaaAdmin, $requestlink['organization_id'])['name'];
            $allData[$requestlink['organization_id']]['id'] = $requestlink['organization_id'];
            $allData[$requestlink['organization_id']]['service_id'] = $requestlink['service_id'];
            $allData[$requestlink['organization_id']]['status'] = $requestlink['status'];
            $allData[$requestlink['organization_id']]['link_id'] = $requestlink['id'];
            $entitlementpacks = $this->get('link')->getEntitlementPacks($hexaaAdmin, $requestlink['id']);

            $entitlementpackNames = null;
            $i = 0;
            $len = count($entitlementpacks['items']);
            foreach ($entitlementpacks['items'] as $entitlementpack) {
                if ($i == $len - 1) {
                    $entitlementpackNames = $entitlementpackNames.$entitlementpack['name'];
                } else {
                    $entitlementpackNames = $entitlementpackNames.$entitlementpack['name'].', ';
                }
                $i++;
            }

            $entitlements = $this->get('link')->getEntitlements($hexaaAdmin, $requestlink['id']);
            $duplicate = array();

            $entitlementsserv = $this->get('service')->getEntitlements($hexaaAdmin, $id, 'normal', 0, 100000);
            foreach ($entitlementpacks['items'] as $entitlementpack) {
                foreach ($entitlementpack['entitlement_ids'] as $entitlementid) {
                   /* array_push($entitlements['items'], $this->get('entitlement')->get($entitlementid));*/
                    foreach ($entitlementsserv['items'] as $entitlementserv) {
                        if ($entitlementserv['id'] == $entitlementid) {
                            array_push($entitlements['items'], $entitlementserv);
                        }
                    }
                }
            }

            $entitlementNames = null;
            $j = 0;
            $withoutduplicate = array_unique($entitlements['items'], SORT_REGULAR);

            $len2 = count($withoutduplicate);
            foreach ($withoutduplicate as $entitlement) {
                if ($j == $len2 - 1) {
                    $entitlementNames = $entitlementNames.$entitlement['name'];
                } else {
                    $entitlementNames = $entitlementNames.$entitlement['name'].', ';
                }
                $j++;
            }

            $allData[$requestlink['organization_id']]['contents'] = array(
                array(
                    'key' => 'entitlementpacks',
                    'values' => $entitlementpackNames,
                ),
                array(
                    'key' => 'entitlements',
                    'values' => $entitlementNames,
                ),
            );
        }

        $pending = false;
        foreach ($allData as $oneData) {
            if ($oneData['status'] == 'pending') {
                $pending = true;
                break;
            }
        }

        $entitlementpacks = $this->get('service')->getEntitlementPacks($hexaaAdmin, $id, 'normal', 0, 100000);
        $entitlements = $this->get('service')->getEntitlements($hexaaAdmin, $id);
        $totalnumber = $entitlements['item_number'];
        $totalpages = ceil($totalnumber / 25);
        $offset = 25;
        $pagesize = 25;
        $verbose = "normal";
        for ($i = 1; $i < $totalpages; $i++) {
            $entitlementmore = $this->get('service')->getEntitlements($hexaaAdmin, $id, $verbose, $offset, $pagesize);
            foreach ($entitlementmore['items'] as $oneentitlementmore) {
                array_push($entitlements['items'], $oneentitlementmore);
            }
            $offset = $offset +25;
        }
        $organizations = $this->get('organization')->getAll();

        $datasToForm = array();
        $organizationsToForm = array();
        $organizationsNotToForm = array();
        $organizationsAllForm = array();
        foreach ($organizations['items'] as $organization) {
            foreach ($requestslinks['items'] as $requestlink) {
                if ($requestlink['organization_id'] == $organization['id']) {
                    $organizationsNotToForm[$organization['id']] = $organization['name'];
                    break;
                }
            }
            $organizationsAllForm[$organization['id']] = $organization['name'];
        }
        $organizationsToForm = array_diff($organizationsAllForm, $organizationsNotToForm);

        $entitlementpacksToForm = array();
        foreach ($entitlementpacks['items'] as $entitlementpack) {
            $entitlementpacksToForm[$entitlementpack['name']] = $entitlementpack['id'];
        }
        $datasToForm['entitlementpacksToForm'] = $entitlementpacksToForm;

        $entitlementsToForm = array();
        foreach ($entitlements['items'] as $entitlement) {
            $entitlementsToForm[$entitlement['name']] = $entitlement['id'];
        }
        $datasToForm['entitlementsToForm'] = $entitlementsToForm;

        $connectNewOrgForm = $this->createForm(
            ConnectOrgType::class,
            array('datas' => $datasToForm),
            array(
                "action" => $this->generateUrl("app_service_neworgconnect", array("id" => $id)),
                "method" => "POST",
            )
        );

        if ($organizationsToForm != null) {
            $connectNewOrgForm->add(
                'organizations',
                TypeaheadType::class,
                array(
                    'label' => "Organization (optional)",
                    'source_name' => 'organizations',
                    'min_length' => 1,
                    'placeholder' => 'Start typing',
                    'matcher' => 'contains', // ends_with, contains
                    'source' => $organizationsToForm,
                    'required' => 'false',
                    'limit' => 30,
                )
            );
        } else {
            $connectNewOrgForm->add(
                'organizations',
                HiddenType::class
            );
        }

        $links = $this->get('service')->getLinksOfService($hexaaAdmin, $id);
        $pendinglinkIDs = array();
        if ($links != null) {
            foreach ($links['items'] as $link) {
                if (($link['organization_id'] == null) && $link['status'] == "pending" && $link['service_id'] == $id) {
                    array_push($pendinglinkIDs, $link['id']);
                }
            }
        }

        $allpendingdata = array();
        foreach ($pendinglinkIDs as $pendinglinkID) {
            $allpendingdata[$pendinglinkID]['link_id'] = $pendinglinkID;
            $linkEntitlementpacks = $this->get('link')->getEntitlementPacks($hexaaAdmin, $pendinglinkID);
            $linkEntitlementpacksNames = null;
            $i = 0;
            $len = count($linkEntitlementpacks['items']);
            foreach ($linkEntitlementpacks['items'] as $linkEntitlementpack) {
                if ($i == $len - 1) {
                    $linkEntitlementpacksNames = $linkEntitlementpacksNames.$linkEntitlementpack['name'];
                } else {
                    $linkEntitlementpacksNames = $linkEntitlementpacksNames.$linkEntitlementpack['name'].', ';
                }
                $i++;
            }

            $linkentitlements = $this->get('link')->getEntitlements($hexaaAdmin, $pendinglinkID);

            foreach ($linkEntitlementpacks['items'] as $linkEntitlementpack) {
                foreach ($linkEntitlementpack['entitlement_ids'] as $entitlementid) {
                    array_push($linkentitlements['items'], $this->get('entitlement')->get($hexaaAdmin, $entitlementid));
                }
            }

            $linkentitlementNames = null;
            $j = 0;
            $withoutduplicatelinks = array_unique($linkentitlements['items'], SORT_REGULAR);
            $len2 = count($withoutduplicatelinks);
            foreach ($withoutduplicatelinks as $entitlement) {
                if ($j == $len2 - 1) {
                    $linkentitlementNames = $linkentitlementNames.$entitlement['name'];
                } else {
                    $linkentitlementNames = $linkentitlementNames.$entitlement['name'].', ';
                }
                $j++;
            }

            $tokens = $this->get('link')->getTokens($hexaaAdmin, $pendinglinkID);
            $linktokens = null;
            $i = 0;
            $len3 = count($tokens);
            foreach ($tokens as $token) {
                if ($i == $len3 - 1) {
                    $linktokens = $linktokens.$token['token'];
                } else {
                    $linktokens = $linktokens.$token['token'].', ';
                }
                $i++;
            }

            $allpendingdata[$pendinglinkID]['contents'] = array(
                array(
                    'key' => 'entitlementpacks',
                    'values' => $linkEntitlementpacksNames,
                ),
                array(
                    'key' => 'entitlements',
                    'values' => $linkentitlementNames,
                ),
                array(
                    'key' => 'tokens',
                    'values' => $linktokens,
                ),
            );
        }

        $acceptedNumber = 0;
        $allChoosenData = null;
        $datasToLinkId = array();
        $forms = array ();
        foreach ($allData as $oneData) {
            $allChoosenData = null;
            $form1 = null;
            if ($oneData['status'] == 'accepted') {
                $acceptedNumber++;
                $allChoosenData['entitlementpacksToForm'] =  $datasToForm['entitlementpacksToForm'];
                $allChoosenData['entitlementsToForm']  =  $datasToForm['entitlementsToForm'];
                $epackNames = array();
                $eNames = array();
                foreach ($oneData['contents'] as $onecontent) {
                    if ($onecontent['key'] == 'entitlementpacks') {
                        $names = explode(', ', $onecontent['values']);
                        foreach ($names as $name) {
                            foreach ($entitlementpacks['items'] as $entitlementpack) {
                                if ($entitlementpack['name'] == trim($name)) {
                                    $epackNames[trim($name)] = $entitlementpack['id'];
                                }
                            }
                        }
                    }
                    if ($onecontent['key'] == 'entitlements') {
                        $names = explode(',', $onecontent['values']);
                        foreach ($names as $name) {
                            foreach ($entitlements['items'] as $entitlement) {
                                if ($entitlement['name'] == trim($name)) {
                                    $eNames[trim($name)] = $entitlement['id'];
                                }
                            }
                        }
                    }
                }
                $allChoosenData['currentEntitlementpacksToForm'] = $epackNames;
                $allChoosenData['currentEntitlementsToForm'] = $eNames;
                $allChoosenData['link_id'] = $oneData['link_id'];
                $form1 = $this->createForm(
                    ModifyConnectOrgType::class,
                    $allChoosenData
                );
                array_push($forms, $form1);
                array_push($datasToLinkId, $oneData['link_id']);
                $allChoosenData = null;
                $form1 = null;
            }
        }

        foreach ($forms as $form) {
            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                $data = $form->getData();
                $entitlementpackIds = array();
                $entitlementsIds = array();
                foreach ($data['entitlementpacks'] as $epack) {
                    array_push($entitlementpackIds, $epack);
                }
                foreach ($data['entitlements'] as $e) {
                    array_push($entitlementsIds, $e);
                }
                $modified['entitlement_packs'] = $entitlementpackIds;
                $modified['entitlements'] = $entitlementsIds;
                $this->get('link')->editlink($hexaaAdmin, $data['link_id'], $modified);

                return $this->redirect($request->getUri());
            }
        }

        $formviews = array();
        foreach ($forms as $form) {
            array_push($formviews, $form->createView());
        }

        return $this->render(
            'AppBundle:Service:connectedorganizations.html.twig',
            array(
                'organizations' => $this->getOrganizations(),
                'services' => $this->getServices(),
                'service' => $this->getService($id),
                "admin" => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
                'servsubmenubox' => $this->getServSubmenuPoints(),
                'all_data' => $allData,
                'allpending_data' => $allpendingdata,
                'datasToLinkId' => $datasToLinkId,
                'pending'  => $pending,
                'connectNewOrgForm' => $connectNewOrgForm->createView(),
                'token' => $token,
                'pendinglinkIDs' => $pendinglinkIDs,
                'acceptedNumber' => $acceptedNumber,
                'forms' => $formviews,
                'manager' => $manager,
                'organizationsWhereManager' => $this->orgWhereManager(),
                'manager' => "false",
                'hexaaHat' => $this->get('session')->get('hexaaHat'),
            )
        );
    }

    /**
     * @Route("/newOrgConnect/{id}/")
     * @Template()
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @param   int     $id      Service ID
     * @param   Request $request request
     */
    public function newOrgConnect($id, Request $request)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $orglinks = $this->get('service')->getLinkRequests($hexaaAdmin, $id);

        $entitlementpacks = $this->get('service')->getEntitlementPacks($hexaaAdmin, $id);

        $entitlements = $this->get('service')->getEntitlements($hexaaAdmin, $id);
        $totalnumber = $entitlements['item_number'];
        $totalpages = ceil($totalnumber / 25);
        $offset = 25;
        $pagesize = 25;
        $verbose = "normal";
        for ($i = 1; $i < $totalpages; $i++) {
            $entitlementmore = $this->get('service')->getEntitlements($hexaaAdmin, $id, $verbose, $offset, $pagesize);
            foreach ($entitlementmore['items'] as $oneentitlementmore) {
                array_push($entitlements['items'], $oneentitlementmore);
            }
            $offset = $offset +25;
        }
        $organizations = $this->get('organization')->getAll();

        $datasToForm = array();
        $organizationsToForm = array();
        $organizationsNotToForm = array();
        $organizationsAllForm = array();
        foreach ($organizations['items'] as $organization) {
            foreach ($orglinks['items'] as $orglink) {
                if ($orglink['organization_id'] == $organization['id']) {
                    $organizationsNotToForm[$organization['id']] = $organization['name'];
                    break;
                }
            }
            $organizationsAllForm[$organization['id']] = $organization['name'];
        }
        $organizationsToForm = array_diff($organizationsAllForm, $organizationsNotToForm);

        $entitlementpacksToForm = array();
        foreach ($entitlementpacks['items'] as $entitlementpack) {
            $entitlementpacksToForm[$entitlementpack['name']] = $entitlementpack['id'];
        }
        $datasToForm['entitlementpacksToForm'] = $entitlementpacksToForm;

        $entitlementsToForm = array();
        foreach ($entitlements['items'] as $entitlement) {
            $entitlementsToForm[$entitlement['name']] = $entitlement['id'];
        }
        $datasToForm['entitlementsToForm'] = $entitlementsToForm;


        $form = $this->createForm(ConnectOrgType::class, array("datas" => $datasToForm));
        if ($organizationsToForm != null) {
            $form->add(
                'organizations',
                TypeaheadType::class,
                array(
                    'label' => "Organization (optional)",
                    'source_name' => 'organizations',
                    'min_length' => 1,
                    'placeholder' => 'Start typing',
                    'matcher' => 'contains', // ends_with, contains
                    'source' => $organizationsToForm,
                    'required' => 'false',
                    'limit' => 30,
                )
            );
        } else {
            $form->add(
                'organizations',
                HiddenType::class
            );
        }
        $currentPrincipal = $this->get('principal')->getSelf($hexaaAdmin);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $getlink = null;
            try {
                $data = $form->getData();
                if ((empty($data['entitlements']) == true) && (empty($data['entitlementpacks']) == true)) {
                    throw new \Exception('You must be choose at least one entitlement pack or entitlement!');
                }

                $entitlementsIDs = $data['entitlements'];
                $entitlementpacksIDs = $data['entitlementpacks'];
                $organizationName = $data['organizations'];

                $orgID = null;
                if ($organizationName != null) {
                    $orgs = $this->get('organization')->getAll();
                    foreach ($orgs['items'] as $org) {
                        if ($org['name'] == $organizationName) {
                            $orgID = $org['id'];
                        }
                    }
                }

                if ($orgID != null) {
                    $postarray = array("status" => "accepted", "organization" => $orgID, "service" => $id, "entitlement_packs" => $entitlementpacksIDs, "entitlements" => $entitlementsIDs);
                } else {
                    $postarray = array("status" => "pending", "service" => $id, "entitlement_packs" => $entitlementpacksIDs, "entitlements" => $entitlementsIDs);
                }
                $response = $this->get('link')->post($hexaaAdmin, $postarray);

                if ($orgID == null) {
                    $headers = $response->getHeader('Location');
                    $headerspartsary = explode("/", $headers[0]);
                    $getlink = $this->get('link')->getNewLinkToken($hexaaAdmin, array_pop($headerspartsary));
                }
                $this->get('session')->getFlashBag()->add('success', 'Link succesfully generated.');
            } catch (\Exception $e) {
                $this->get('session')->getFlashBag()->add('error', 'Message sending failure.<br>'.$e->getMessage());
            }

            return $this->redirect($this->generateUrl('app_service_connectedorganizations', array('id' => $id, 'token' => $getlink['token'])));
        }
    }

    /**
     * @Route("/removeConnectedOrganizations/{id}")
     * @Template()
     * @param integer $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeConnectedOrganizations($id, Request $request)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $linkIds = $request->get('linkId');
        $errors = array();
        $errormessages = array();
        foreach ($linkIds as $linkId) {
            try {
                $this->get('link')->deleteLink($hexaaAdmin, $linkId);
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
                'Link remove failed'
            );
        }

        return $this->redirect($this->generateUrl('app_service_connectedorganizations', array('id' => $id, )));
    }

    /**
     * @Route("/acceptConnectedOrganizations/{id}")
     * @Template()
     * @param integer $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function acceptConnectedOrganizations($id, Request $request)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $linkIds = $request->get('linkId');
        $errors = array();
        $errormessages = array();
        foreach ($linkIds as $linkId) {
            try {
                //get all organization link
               // $requests = $this->get('service')->getLinkRequests($id);
                $request =  $this->get('link')->get($hexaaAdmin, $linkId);
             //   foreach ($requests['items'] as $request) {
                $data = array();
                $data['service'] = $request['service_id'];
                $data['organization'] = $request['organization_id'];
                $entitlementpacksIds = array();
                $entitlementpacks = $this->get('link')->getEntitlementPacks($hexaaAdmin, $request['id']);
                foreach ($entitlementpacks['items'] as $entitlementpack) {
                    array_push($entitlementpacksIds, $entitlementpack['id']);
                }
                $data['entitlement_packs'] = $entitlementpacksIds;
                $data['status'] = "accepted";
                $this->get('link')->editLink($hexaaAdmin, $request['id'], $data);
              //  }
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
                'Organization accepted failed'
            );
        }

        return $this->redirect($this->generateUrl('app_service_connectedorganizations', array('id' => $id, )));
    }

    /**
     * @Route("/delete/{id}")
     * @Template()
     * @return Response
     * @param int $id Service Id
     *
     */
    public function deleteAction($id)
    {
        $serviceResource = $this->get('service');
        $serviceResource->delete($this->get('session')->get('hexaaAdmin'), $id);

        return $this->redirectToRoute("homepage");
    }

    /**
     * Get the history of the requested service.
     * @Route("/history/{id}")
     * @Template()
     * @return array
     * @param int $id Service Id
     */
    public function historyAction($id)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $serviceResource = $this->get('service');
        $service = $serviceResource->get($hexaaAdmin, $id);

        return array(
            "service" => $service,

            "organizations" => $this->get('organization')->cget($hexaaAdmin),
            "services" => $this->get('service')->cget($hexaaAdmin),
            'servsubmenubox' => $this->getServSubmenuPoints(),
            "admin" => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
            'organizationsWhereManager' => $this->orgWhereManager(),
            'manager' => "false",
            'hexaaHat' => $this->get('session')->get('hexaaHat'),
        );
    }

    /**
     * @Route("/history/json/{id}")
     * @param string       $id       Service id
     * @param integer|null $offset   Offset
     * @param integer      $pageSize Pagesize
     * @return array
     */
    public function historyJSONAction($id, $offset = null, $pageSize = 25)
    {
        $serviceResource = $this->get('service');
        $principalResource = $this->get('principals');
        $data = $serviceResource->getHistory($this->get('session')->get('hexaaAdmin'), $id);
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
     * @Route("/{servId}/permission/{id}/delete")
     * @Template()
     * @return Response
     * @param int $servId Service id
     * @param int $id     Permission Id
     *
     */
    public function permissionDeleteAction($servId, $id)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $this->get('entitlement')->deletePermission($hexaaAdmin, $id);
        $this->get('session')->getFlashBag()->add('success', 'The permission has been deleted.');

        return $this->redirectToRoute("app_service_permissions", array("id" => $servId));
    }

    /**
     * @Route("/{servId}/permissionset/{id}/delete")
     * @Template()
     * @return Response
     * @param int $servId Service id
     * @param int $id     PermissionSet Id
     *
     */
    public function permissionsetDeleteAction($servId, $id)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $this->get('entitlement_pack')->deletePermissionSet($hexaaAdmin, $id);
        $this->get('session')->getFlashBag()->add('success', 'The permission set has been deleted.');

        return $this->redirectToRoute("app_service_permissionssets", array("id" => $servId));
    }


    /**
     * @Route("/{id}/warnings")
     * @param string $id
     *
     * @return JsonResponse
     */
    public function getWarnings($id)
    {
        $service = $this->get('service');
        $serializer = $this->get('serializer');
        $data = $service->getWarnings($this->get('session')->get('hexaaAdmin'), $id, array("linkResource" => $this->get('link')));
        $serializedData = $serializer->serialize($data, 'json');

        return new JsonResponse($serializedData);
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
     * @param $service
     * @return \Symfony\Component\Form\Form
     */
    private function createCreateInvitationForm($service)
    {

        $form = $this->createForm(
            ServiceUserInvitationType::class,
            array(
                "start_date" => date("Y-m-d"),
                "end_date" => date("Y-m-d", strtotime("+1 week")),
            ),
            array(
                "action" => $this->generateUrl("app_service_createinvitation", array("id" => $service['id'])),
                "method" => "POST",
            )
        );

        return $form;
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
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        //service attribute specifications without names
        $serviceattributespecs = $this->get('service')->getAttributeSpecs($hexaaAdmin, $service['id'])['items'];

        foreach ($this->get('attribute_spec')->cget($hexaaAdmin, $verbose)['items'] as $attributespecification) {
            foreach ($serviceattributespecs as $serviceattributespec) {
                if ($attributespecification['id'] == $serviceattributespec['attribute_spec_id']) {
                    $serviceattributespecifications[$attributespecification['name']] = $serviceattributespec['attribute_spec_id'];
                }
            }
        }

        $attributespecifications["Which attribute specification?"] = "Which attribute specification?";
        //all attribute specifications
        foreach ($this->get('attribute_spec')->cget($hexaaAdmin, $verbose)['items'] as $attributespecification) {
            $attributespecifications[$attributespecification['name']] = $attributespecification['id'];
        }

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
     * @param $permissions
     * @param $servId
     * @param $permissionId,
     * @param $action
     * @param $request
     * @return array
     */
    private function permissionsToAccordion($permissions, $servId, $permissionId, $action, Request $request)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $permissionsAccordion = array();
        foreach ($permissions as $onepermissiongroup) {
            foreach ($onepermissiongroup as $permission) {
                $urichunked = explode(':', $permission['uri']);
                $permissionsAccordion[$permission['id']]['uripostfix'] = $urichunked[5];
                $permissionsAccordion[$permission['id']]['uriprefix'] = $urichunked[0].':'.$urichunked[1].':'.$urichunked[2].':'.$urichunked[3].':'.$urichunked[4].':';
                $form =  $this->createForm(
                    ServicePermissionUpdateType::class,
                    $permission,
                    array(
                        "action" => $this->generateUrl("app_service_permissions", array("id" => $servId, "action" => "update", "permissionId" => $permission['id'])),
                    )
                );


                $permissionsAccordion[$permission['id']]['title'] = $permission['name'];

                $permissionsAccordion[$permission['id']]['deleteUrl'] = $this->generateUrl("app_service_permissiondelete", [
                    'servId' => $servId,
                    'id' => $permission['id'],
                    'action' => "delete",
                ]);

                $description = [];
                $uri = [];
                array_push($description, $permission['description']);
                array_push($uri, $permission['uri']);
                $permissionsAccordion[$permission['id']]['contents'] = [
                [
                    'key' => 'Description',
                    'values' => $description,
                ],
                [
                    'key' => 'URI',
                    'values' => $uri,
                ],
                ];

                if ($permissionId == $permission['id']) { // csak akkor dolgozzuk fel, ha erről a role-ról van szó.
                    $form->handleRequest($request);
                }

                if ($form->isValid() and $form->isSubmitted()) {
                    $data = $form->getData();
                    $entitlementResource = $this->get('entitlement');
                    try {
                        $entitlement = $entitlementResource->get($hexaaAdmin, $data['id']);
                        $entitlement["name"] = $data["name"];
                        $entitlement["description"] = $data["description"];
                        $withoutAccent = $this->removeAccents($data['uripost']);
                        $modifiedName = preg_replace("/[^a-zA-Z0-9-_:]+/", "", $withoutAccent);
                        $entitlement["uri"] = $permissionsAccordion[$permission['id']]['uriprefix'].$modifiedName;
                        try {
                            $this->get('entitlement')->patch($hexaaAdmin, $entitlement['id'], [
                                'name' => $entitlement["name"],
                            ]);
                        } catch (\Exception $exception) {
                            $form->get('name')->addError(new FormError($exception->getMessage()));
                        }
                        try {
                            $this->get('entitlement')->patch($hexaaAdmin, $entitlement['id'], [
                                'uri' => $entitlement["uri"],
                            ]);
                        } catch (\Exception $exception) {
                            $form->get('uripost')->addError(new FormError($exception->getMessage()));
                        }
                        try {
                            $this->get('entitlement')->patch($hexaaAdmin, $entitlement['id'], [
                                'description' => $entitlement["description"],
                            ]);
                        } catch (\Exception $exception) {
                            $form->get('description')->addError(new FormError($exception->getMessage()));
                        }
                    } catch (\AppBundle\Exception $exception) {
                        $form->addError(new FormError($exception->getMessage()));
                    }
                    if ($form->getErrors(true)->count() == 0) {
                        $this->get('session')->getFlashBag()->add('success', 'Permission modified succesfully.');
                    }
                    if (! $form->getErrors(true)->count()) { // false-szal térünk vissza, ha nincs hiba. Mehessen a redirect az alaphoz.
                        return false;
                    }
                }
                $permissionsAccordion[$permission['id']]['form'] = $form->createView();
            }
        }

        $size = 25;
        $smallarray = array_chunk($permissionsAccordion, $size);

        return $smallarray;
    }

    /**
    * @param $permissions
    * @param $servId
    * @return array
    */
    private function allpermissionsToAccordion($permissions, $servId)
    {
        $permissionsAccordion = array();
        foreach ($permissions['items'] as $permission) {
            $permissionsAccordion[$permission['id']]['title'] = $permission['name'];

            // FIXME @annamari, nem találok permission delete url-t.
            $permissionsAccordion[$permission['id']]['deleteUrl'] = $this->generateUrl("app_service_permissiondelete", [
                'servId' => $servId,
                'id' => $permission['id'],
                'action' => "delete",
            ]);

            $description = [];
            $uri = [];
            array_push($description, $permission['description']);
            array_push($uri, $permission['uri']);
            $permissionsAccordion[$permission['id']]['contents'] = [
            [
                'key' => 'Description',
                'values' => $description,
            ],
            [
                'key' => 'URI',
                'values' => $uri,
            ],
            ];
        }

        return $permissionsAccordion;
    }

    /**
     * @param $permissionSets
     * @param $servId
     * @param $permissionsetId
     * @param $action
     * @param $request
     * @return array
     */
    private function permissionSetToAccordion($permissionSets, $servId, $permissionsetId, $action, $request)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $permissionsAccordionSet = array();
        foreach ($permissionSets as $onePermissionSetGroup) {
            foreach ($onePermissionSetGroup as $permissionSet) {
                $permissionsChoices = [];
                if (array_key_exists('entitlement_ids', $permissionSet) and !(empty($permissionSet['entitlement_ids']))) {
                    foreach ($permissionSet['entitlement_ids'] as $entitlementid) {
                        $entitlement = $this->get('entitlement')->get($hexaaAdmin, $entitlementid);
                        $permissionsChoices['permissions'][$entitlementid] = $entitlement['name'];
                        $permissionsChoices['name'] = $permissionSet['name'];
                        $permissionsChoices['description'] = $permissionSet['description'];
                        $permissionsChoices['type'] = $permissionSet['type'];
                        $permissionsChoices['id'] = $permissionSet['id'];
                    }
                } else {
                    $permissionsChoices['name'] = $permissionSet['name'];
                    $permissionsChoices['description'] = $permissionSet['description'];
                    $permissionsChoices['type'] = $permissionSet['type'];
                    $permissionsChoices['id'] = $permissionSet['id'];
                    $permissionsChoices['permissions'] = [];
                }
                if (!empty($permissionsChoices)) {
                    $form = $this->createForm(
                        ServicePermissionSetUpdateType::class,
                        $permissionsChoices,
                        [
                            "action" => $this->generateUrl("app_service_permissionssets", [
                                "id" => $servId,
                                "action" => "update",
                                "permissionsetId" => $permissionSet['id'],
                            ]),
                        ]
                    );
                }

                $permissionsAccordionSet[$permissionSet['id']]['title'] = $permissionSet['name'];
                $permissionsAccordionSet[$permissionSet['id']]['deleteUrl'] = $this->generateUrl("app_service_permissionsetdelete", [
                    'servId' => $servId,
                    'id' => $permissionSet['id'],
                    'action' => "delete",
                ]);

                $description = [];
                $type = [];
                $permissions = [];
                array_push($description, $permissionSet['description']);
                array_push($type, $permissionSet['type']);
                foreach ($permissionSet['entitlement_ids'] as $entitlementid) {
                    $entitlement = $this->get('entitlement')->getEntitlement($hexaaAdmin, $entitlementid);
                    array_push($permissions, $entitlement['name']);
                }
                $permissionsAccordionSet[$permissionSet['id']]['contents'] = [
                    [
                        'key' => 'Description',
                        'values' => $description,
                    ],
                    [
                        'key' => 'Type',
                        'values' => $type,
                    ],
                    [
                        'key' => 'Permissions',
                        'values' => $permissions,
                    ],
                ];


                if ($permissionsetId == $permissionSet['id']) { // csak akkor dolgozzuk fel, ha erről a role-ról van szó.
                    $form->handleRequest($request);
                }

                $verbose = "normal";

                $servicepermissions = $this->get('service')->getEntitlements($hexaaAdmin, $servId, $verbose, 0, 100000);
                if (!empty($permissionsChoices)) {
                    if ($form->isValid() and $form->isSubmitted()) {
                        $data = $form->getData();
                        $entitlementpackResource = $this->get('entitlement_pack');
                        try {
                            $entitlementpack = $entitlementpackResource->get($hexaaAdmin, $data['id']);
                            $entitlementpack["name"] = $data["name"];
                            $entitlementpack["description"] = $data["description"];
                            $entitlementpack["type"] = $data["type"];

                            $permissionids = [];
                            if (count(array_unique($data['permissions'])) != 0) {
                                $iter = 0;
                                $apiProperties = $this->get('service')->apget($hexaaAdmin);
                                foreach (array_unique($data['permissions']) as $permission) {
                                    $iter = 0;
                                    foreach ($servicepermissions['items'] as $servicepermission) {
                                        if ($servicepermission['name'] == $permission) {
                                            array_push($permissionids, $servicepermission['id']);
                                            break;
                                        } else {
                                            $iter++;
                                        }
                                    }
                                    if ($iter == $servicepermissions['item_number']) {
                                        $withoutAccent = $this->removeAccents($permission);
                                        $modifiedName = preg_replace("/[^a-zA-Z0-9-_:]+/", "", $withoutAccent);
                                        try {
                                            $newpermission = $this->get('service')->createPermission($hexaaAdmin, $apiProperties['entitlement_base'], $servId, $modifiedName, $permission, null, $this->get('entitlement'));
                                            array_push($permissionids, $newpermission['id']);
                                        } catch (\Exception $exception) {
                                            $form->get('permissions')->addError(new FormError($exception->getMessage()));
                                        }
                                    }
                                }
                            }
                            try {
                                $this->get('entitlement_pack')->put($hexaaAdmin, $data['id'], [
                                    'type' => $entitlementpack["type"],
                                    'name' => $entitlementpack["name"],
                                    'description' => $entitlementpack["description"],
                                ]);
                            } catch (\Exception $exception) {
                                $form->get('name')->addError(new FormError($exception->getMessage()));
                            }
                            try {
                                $this->get('entitlement_pack')->setPermissionsToPermissionSet($hexaaAdmin, $data['id'], $permissionids);
                            } catch (\Exception $exception) {
                                $form->get('entitlement_pack')->addError(new FormError($exception->getMessage()));
                            }
                        } catch (\AppBundle\Exception $exception) {
                            $form->addError(new FormError($exception->getMessage()));
                        }
                        if ($form->getErrors(true)->count() == 0) {
                            $this->get('session')->getFlashBag()->add('success', 'Permission set modified succesfully.');
                        }
                        if (!$form->getErrors(true)->count()) { // false-szal térünk vissza, ha nincs hiba. Mehessen a redirect az alaphoz.
                            return false;
                        }
                    }
                    $permissionsAccordionSet[$permissionSet['id']]['form'] = $form->createView();
                }
            }
        }
        $size = 25;
        $smallarray = array_chunk($permissionsAccordionSet, $size);

        return $smallarray;
    }

    /**
    * @param $permissionSets
    * @param $servId
    * @return array
    */
    private function allpermissionSetToAccordion($permissionSets, $servId)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $permissionsAccordionSet = array();
        foreach ($permissionSets['items'] as $permissionSet) {
            $permissionsAccordionSet[$permissionSet['id']]['title'] = $permissionSet['name'];
            $permissionsAccordionSet[$permissionSet['id']]['deleteUrl'] = $this->generateUrl("app_service_permissionsetdelete", [
                'servId' => $servId,
                'id' => $permissionSet['id'],
                'action' => "delete",
            ]);

            $description = [];
            $type = [];
            $permissions = [];
            array_push($description, $permissionSet['description']);
            array_push($type, $permissionSet['type']);
            foreach ($permissionSet['entitlement_ids'] as $entitlementid) {
                $entitlement = $this->get('entitlement')->getEntitlement($hexaaAdmin, $entitlementid);
                array_push($permissions, $entitlement['name']);
            }
            $permissionsAccordionSet[$permissionSet['id']]['contents'] = [
               [
                  'key' => 'Description',
                  'values' => $description,
               ],
               [
                  'key' => 'Type',
                  'values' => $type,
               ],
               [
                  'key' => 'Permissions',
                  'values' => $permissions,
               ],
            ];
        }

        return $permissionsAccordionSet;
    }

    /**
     * @param $service
     * @return mixed
     */
    private function getManagers($service)
    {
        return $this->get('service')->getManagers($this->get('session')->get('hexaaAdmin'), $service['id'])['items'];
    }

    /**
     * @param $service
     * @return array
     */
    private function getServiceAttributes($service)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $verbose = "expanded";
        $serviceattributespecs = $this->get('service')->getAttributeSpecs($hexaaAdmin, $service['id'])['items'];
        $attributestonames = array();
        foreach ($this->get('attribute_spec')->cget($hexaaAdmin, $verbose)['items'] as $attributespec) {
            foreach ($serviceattributespecs as $serviceattributespec) {
                if ($attributespec['id'] == $serviceattributespec['attribute_spec_id']) {
                    array_push($attributestonames, $attributespec);
                }
            }
        }

        return $attributestonames;
    }

    /**
    * @param $id
    * @return bool
    */
    private function isManager($id)
    {
        $manager = false;
        $services = $this->get('principal')->servsWhereUserIsManager($this->get('session')->get('hexaaAdmin'));
        foreach ($services as $oneserv) {
            if ($oneserv['id'] == $id) {
                $manager = true;
                break;
            }
        }

        return $manager;
    }

    /**
     * @return mixed
     */
    private function getOrganizations()
    {
        $organization = $this->get('organization')->cget($this->get('session')->get('hexaaAdmin'));

        return $organization;
    }

    /**
     * @return mixed
     */
    private function getServices()
    {
        $services = $this->get('service')->cget($this->get('session')->get('hexaaAdmin'));

        return $services;
    }

    /**
     * @param $id
     * @return mixed
     */
    private function getService($id)
    {
        $service = $this->get('service')->get($this->get('session')->get('hexaaAdmin'), $id);

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
