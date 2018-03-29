<?php

namespace AppBundle\Controller;

use AppBundle\Form\OrgManagersContactType;
use AppBundle\Form\ServManagersContactType;
use AppBundle\Form\AdminAttributeSpecType;
use AppBundle\Form\AdminAttributeSpecUpdateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 */
class AdminController extends BaseController
{
    /**
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
     * @Route("/attributes/{admin}/{attributeId}/{action}", defaults={"attributeId" : false, "action" : false})
     * @Template()
     * @param bool    $admin
     * @param integer $attributeId
     * @param string  $action
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function attributesAction($admin, $attributeId, $action, Request $request)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $attributespecifications = $this->get('attribute_spec')->cget($hexaaAdmin);
        $error = "false";

        $attributesaccordion = $this->attributesToAccordion($admin, $attributespecifications, $attributeId, $action, $request);

        if (false === $attributesaccordion) { // belső form rendesen le lett kezelve, vissza az alapokhoz
            return $this->redirectToRoute('app_admin_attributes', array("admin" => $admin));
        }

        $formCreateAttributeSpec = $this->createForm(
            AdminAttributeSpecType::class
        );

        $formCreateAttributeSpec->handleRequest($request);

        try {
            if ($formCreateAttributeSpec->isSubmitted() && $formCreateAttributeSpec->isValid()) {
                $data = $request->request->all();

                foreach ($attributespecifications['items'] as $attributespecification) {
                    if (strtolower($attributespecification['name']) == strtolower($data['admin_attribute_spec']['attributeSpecName'])) {
                        throw new \Exception('Name is case insensitive! Attribute specification name already exists! Please, choose different name!');
                    }
                }

                if (strlen($data['admin_attribute_spec']['attributeSpecName']) < 3) {
                    throw new \Exception('Name must be at least three character long!');
                }

                $attributeSpec = array(
                    'name' => $data['admin_attribute_spec']['attributeSpecName'],
                    'uri' => $data['admin_attribute_spec']['attributeSpecURI'],
                    'description' => $data['admin_attribute_spec']['attributeSpecDescription'],
                    'maintainer' => $data['admin_attribute_spec']['attributeSpecMaintainer'],
                    'syntax' => $data['admin_attribute_spec']['attributeSpecSyntax'],
                    'is_multivalue' => $data['admin_attribute_spec']['attributeSpecIsMultivalue'],
                );
                $this->get('attribute_spec')->createAttributeSpec($admin, $attributeSpec);
                //  $this->get('service')->createPermission($permisson['uri'], $id, $permisson['name'], $permisson['description'], $this->get('entitlement'));
                $this->get('session')->getFlashBag()->add('success', 'Attribute specification created succesfully.');

                return $this->redirect($request->getUri());
            }
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', $e->getMessage());
            $error = "true";
        }

        return $this->render(
            'AppBundle:Admin:attributes.html.twig',
            array(
                "organizations" => $this->get('organization')->cget($hexaaAdmin),
                "services" => $this->get('service')->cget($hexaaAdmin),
                "admin" => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
                'organizationsWhereManager' => $this->orgWhereManager(),
                "submenu" => "true",
                'adminsubmenubox' => $this->getAdminSubmenupoints(),
                'attributes_accordion' => $attributesaccordion,
                'formCreateAttributeSpec' => $formCreateAttributeSpec->createView(),
                'action' => $action,
                'attributespecifications' => $attributespecifications,
                'error' => $error,
                'manager' => "false",
                'ismanager' => "true",
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
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $principals = $this->get('principal')->getAllPrincipals()["items"];
        $principalsButtons = array(
            "remove" => array(
                "class" => "btn-blue pull-left",
                "text" => "Remove",
            ),
        );

        return $this->render(
            'AppBundle:Admin:principals.html.twig',
            array(
                "organizations" => $this->get('organization')->cget($hexaaAdmin),
                "services" => $this->get('service')->cget($hexaaAdmin),
                "admin" => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
                "submenu" => "true",
                "adminsubmenubox" => $this->getAdminSubmenupoints(),
                "principals_buttons" => $principalsButtons,
                "principals" => $principals,
                'organizationsWhereManager' => $this->orgWhereManager(),
                'manager' => "false",
                'ismanager' => "true",

            )
        );
    }

    /**
     * @Route("/removeprincipals")
     * @Template()
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeprincipalsAction(Request $request)
    {
        $pids = $request->get('userId');
        $errors = array();
        $errormessages = array();
        foreach ($pids as $pid) {
            try {
                $this->get('principal')->deletePrincipal($this->get('principal')->isAdmin($this->get('session')->get('hexaaAdmin'))["is_admin"], $pid);
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

        return $this->redirect($this->generateUrl('app_admin_principals', array("admin" => $this->get('principal')->isAdmin()["is_admin"])));
    }

    /**
     * @Route("/entity/{admin}")
     * @Template()
     * @param bool $admin
     * @return Response
     */
    public function entityAction($admin)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $entityids = $this->get('entity_id')->cget($hexaaAdmin);
        $totalnumber = $entityids['item_number'];
        $totalpages = ceil($totalnumber / 25);
        $offset = 25;
        $pagesize = 25;
        $verbose = "normal";
        $entitysperpage = array();
        $allentity = array();
        array_push($entitysperpage, $entityids['items']);
        for ($i = 1; $i < $totalpages; $i++) {
            $entityperpage = $this->get('entity_id')->cget($hexaaAdmin, $verbose, $offset, $pagesize);
            array_push($entitysperpage, $entityperpage['items']);
            $offset = $offset +25;
        }
        $allentitypart = $this->get('entity_id')->cget($hexaaAdmin, $verbose, 0, 100000);

        return $this->render(
            'AppBundle:Admin:entity.html.twig',
            array(
                "organizations" => $this->get('organization')->cget($hexaaAdmin),
                "services" => $this->get('service')->cget($hexaaAdmin),
                "admin" => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
                "submenu" => "true",
                'adminsubmenubox' => $this->getAdminSubmenupoints(),
                'entityids_accordion' => $this->entityIDsToAccordion($entitysperpage),
                'all_entityid' => $this->allEntityIDsToAccordion($allentitypart),
                'total_number' => $totalnumber,
                'total_pages' => $totalpages,
                'organizationsWhereManager' => $this->orgWhereManager(),
                'manager' => "false",
                'ismanager' => "true",
            )
        );
    }

    /**
     * @Route("/contact/{admin}/{orgEmailSended}", defaults={"orgEmailSended" = "false"})
     * @Template()
     * @param bool    $admin
     * @param Request $request
     * @param string  $orgEmailSended
     * @return Response
     */
    public function contactAction($admin, Request $request, $orgEmailSended = "false")
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $services = $this->get('service')->getAll();
        $servicesNames = array();
        foreach ($services['items'] as $service) {
            array_push($servicesNames, $service['name']);
        }

        $organizations = $this->get('organization')->getAll();
        $organizationsNames = array();
        foreach ($organizations['items'] as $organization) {
            array_push($organizationsNames, $organization['name']);
        }

        $orgManagersForm = $this->createForm(OrgManagersContactType::class, array(
            'organizations' => $organizationsNames,
        ));
        $managersForm = $this->createForm(ServManagersContactType::class, array(
            'services' => $servicesNames,
        ));

        $principal = $this->get('principal')->getSelf($hexaaAdmin);

        $orgManagersForm->handleRequest($request);
        $managersForm->handleRequest($request);

        // $orgemailsended = "false";

        if ($orgManagersForm->isSubmitted() && $orgManagersForm->isValid()) {
            $data = $orgManagersForm->getData();

            $organizationName = $data['organization'];
            $organizationID = null;
            foreach ($organizations['items'] as $organization) {
                if ($organization['name'] == $organizationName) {
                    $organizationID = $organization['id'];
                    break;
                }
            }
            if ($organizationID == null) {
                $orgEmailSended = "true";
                $this->get('session')->getFlashBag()->add('error', 'Organization is not exist.');
            } else {
                $managers = $this->get('organization')->getManagers($hexaaAdmin, $organizationID);

                $orgManagersEmail = array();
                foreach ($managers['items'] as $manager) {
                    array_push($orgManagersEmail, $manager['email']);
                }

                $config = $this->getParameter('invitation_config');
                $mailer = $this->get('mailer');

                try {
                    $message = $mailer->createMessage()
                        ->setSubject($data['orgManagersTitle'])
                        ->setFrom($principal['email'])
                        ->setCc($orgManagersEmail)
                        ->setReplyTo($config['reply-to'])
                        ->setBody(
                            $this->render(
                                'AppBundle:Admin:AdminEmail.txt.twig',
                                array(
                                    'footer' => $config['footer'],
                                    'message' => $data['orgManagersMessage'],
                                )
                            ),
                            'text/plain'
                        );

                    $mailer->send($message);
                    $this->get('session')->getFlashBag()->add('success', 'Message sent succesfully.');
                    $orgemailsended = "true";

                    return $this->redirect(
                        $this->generateUrl(
                            'app_admin_contact',
                            array(
                                "admin" => $this->get('principal')->isAdmin()["is_admin"],
                                "orgEmailSended" => $orgemailsended,
                            )
                        )
                    );
                } catch (\Exception $e) {
                    $orgEmailSended = "true";
                    $this->get('session')->getFlashBag()->add('error', 'Message sending failure.<br> The error was: <br>'.$e->getMessage());
                }
            }
        }

        if ($managersForm->isSubmitted() && $managersForm->isValid()) {
            $data = $managersForm->getData();
            $serviceName = $data['service'];
            $serviceID = null;
            foreach ($services['items'] as $service) {
                if ($service['name'] == $serviceName) {
                    $serviceID = $service['id'];
                    break;
                }
            }
            if ($serviceID == null) {
                $orgEmailSended = "false";
                $this->get('session')->getFlashBag()->add('error', 'Service is not exist.');
            } else {
                $managers = $this->get('service')->getManagers($hexaaAdmin, $serviceID);
                $managersEmail = array();
                foreach ($managers['items'] as $manager) {
                    array_push($managersEmail, $manager['email']);
                }

                $config = $this->getParameter('invitation_config');
                $mailer = $this->get('mailer');
                try {
                    $message = $mailer->createMessage()
                        ->setSubject($data['managersTitle'])
                        ->setFrom($principal['email'])
                        ->setCc($managersEmail)
                        ->setReplyTo($config['reply-to'])
                        ->setBody(
                            $this->render(
                                'AppBundle:Admin:AdminEmail.txt.twig',
                                array(
                                    'footer' => $config['footer'],
                                    'message' => $data['managersMessage'],
                                )
                            ),
                            'text/plain'
                        );

                    $mailer->send($message);
                    $this->get('session')->getFlashBag()->add('success', 'Message sent succesfully.');
                    $orgemailsended = "false";

                    return $this->redirect(
                        $this->generateUrl(
                            'app_admin_contact',
                            array(
                                "admin" => $this->get('principal')->isAdmin()["is_admin"],
                                "submenu" => "true",
                                "orgEmailSended" => $orgemailsended,
                            )
                        )
                    );
                } catch (\Exception $e) {
                    $orgEmailSended = "false";
                    $this->get('session')->getFlashBag()->add('error', 'Message sending failure.<br> The error was: <br>'.$e->getMessage());
                }
            }
        }

        return $this->render(
            'AppBundle:Admin:contact.html.twig',
            array(
                "organizations" => $this->get('organization')->cget($hexaaAdmin),
                "services" => $this->get('service')->cget($hexaaAdmin),
                "admin" => $this->get('principal')->isAdmin($hexaaAdmin)["is_admin"],
                "submenu" => "true",
                "orgEmailSended" => $orgEmailSended,
                "adminsubmenubox" => $this->getAdminSubmenupoints(),
                "formOrgManagers" => $orgManagersForm->createView(),
                "formManagers" => $managersForm->createView(),
                "servicesName" => $servicesNames,
                'organizationsWhereManager' => $this->orgWhereManager(),
                'manager' => "false",
            )
        );
    }

    /**
     * @Route("/attributespecification/{id}/delete")
     * @Template()
     * @return Response
     * @param int $id Attributespecification Id
     *
     */
    public function attributespecificationDeleteAction($id)
    {
        $this->get('attribute_spec')->deleteAdmin($this->get('session')->get('hexaaAdmin'), $id);
        $this->get('session')->getFlashBag()->add('success', 'The attribute specification has been deleted.');

        return $this->redirectToRoute("app_admin_attributes", array("admin" => $this->get('principal')->isAdmin($this->get('session')->get('hexaaAdmin'))["is_admin"]));
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
            "app_admin_contact" => "Contact",
        );

        return $submenubox;
    }

    /**
     * @param $admin
     * @param $attributespecifications
     * @param $attributeId
     * @param $action
     * @param $request
     * @return array
     */
    private function attributesToAccordion($admin, $attributespecifications, $attributeId, $action, Request $request)
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        $attributesAccordion = array();
        foreach ($attributespecifications['items'] as $attributespecification) {
            $form =  $this->createForm(
                AdminAttributeSpecUpdateType::class,
                $attributespecification,
                array(
                    "action" => $this->generateUrl("app_admin_attributes", array("admin" => $admin, "action" => "update", "attributeId" => $attributespecification['id'])),
                )
            );
            $attributesAccordion[$attributespecification['id']]['title'] = $attributespecification['name'];
            $attributesAccordion[$attributespecification['id']]['deleteUrl'] = $this->generateUrl("app_admin_attributespecificationdelete", array('id' => $attributespecification['id']));
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

            if ($attributeId == $attributespecification['id']) { // csak akkor dolgozzuk fel, ha erről a role-ról van szó.
                $form->handleRequest($request);
            }

            if ($form->isValid() and $form->isSubmitted()) {
                $data = $form->getData();
                $attributeResource = $this->get('attribute_spec');
                try {
                    $attributespec = $attributeResource->get($hexaaAdmin, $data['id']);
                    $attributespec["name"] = $data["name"];
                    $attributespec["description"] = $data["description"];
                    $attributespec["uri"] = $data["uri"];
                    $attributespec["maintainer"] = $data["maintainer"];
                    $attributespec["is_multivalue"] = $data["Multivalue"];
                    $attributespec["syntax"] = $data["syntax"];
                    try {
                        $attributeResource->patchAdmin($attributespec['id'], ["name" => $attributespec['name']]);
                       // $attributeResource->patchAdmin($attributespec['id'], ["name" => $attributespec['name'], "description" => $attributespec['description'], "uri" => $attributespec['uri'], "syntax" => $attributespec['syntax'], "maintainer" => $attributespec['maintainer'], "is_multivalue" => $attributespec["is_multivalue"]]);
                    } catch (\Exception $exception) {
                        $form->get('name')->addError(new FormError($exception->getMessage()));
                    }
                    try {
                        $attributeResource->patchAdmin($attributespec['id'], ["description" => $attributespec['description']]);
                    } catch (\Exception $exception) {
                        $form->get('description')->addError(new FormError($exception->getMessage()));
                    }
                    try {
                        $attributeResource->patchAdmin($attributespec['id'], ["uri" => $attributespec['uri']]);
                    } catch (\Exception $exception) {
                        $form->get('uri')->addError(new FormError($exception->getMessage()));
                    }
                    try {
                        $attributeResource->patchAdmin($attributespec['id'], ["syntax" => $attributespec['syntax']]);
                    } catch (\Exception $exception) {
                        $form->get('syntax')->addError(new FormError($exception->getMessage()));
                    }
                    try {
                        $attributeResource->patchAdmin($attributespec['id'], ["maintainer" => $attributespec['maintainer']]);
                    } catch (\Exception $exception) {
                        if (strpos($exception->getMessage(), "this AttributeSpec can not be linked to a principal") !== false) {
                            $form->get('maintainer')
                            ->addError(new FormError("First delete attribute value connected to attribute spec! ".$exception->getMessage()));
                        } elseif (strpos($exception->getMessage(), "Can't assign maintainer to user to AttributeSpec") !== false) {
                            $form->get('maintainer')
                            ->addError(new FormError("First delete attribute value connected to attribute spec! ".$exception->getMessage()));
                        } else {
                            $form->get('maintainer')->addError(new FormError($exception->getMessage()));
                        }
                    }
                    try {
                        $attributeResource->patchAdmin($attributespec['id'], ["is_multivalue" => $attributespec['is_multivalue']]);
                    } catch (\Exception $exception) {
                        $form->get('uri')->addError(new FormError($exception->getMessage()));
                    }
                } catch (\AppBundle\Exception $exception) {
                    $form->addError(new FormError($exception->getMessage()));
                }
                if ($form->getErrors(true)->count() == 0) {
                    $this->get('session')->getFlashBag()->add('success', 'Attribute specification modified succesfully.');
                }
                if (!$form->getErrors(true)->count()) { // false-szal térünk vissza, ha nincs hiba. Mehessen a redirect az alaphoz.
                    return false;
                }
            }
            $attributesAccordion[$attributespecification['id']]['form'] = $form->createView();
        }

        return $attributesAccordion;
    }


    /**
     * @param $entityIDs
     * @return array
     */
    private function entityIDsToAccordion($entityIDs)
    {
        $entityIDsAccordion = array();
        $keys = array_keys($entityIDs);
        foreach ($keys as $key) {
            $entityarray = $entityIDs[$key];
            $keysinter = array_keys($entityarray);
            foreach ($keysinter as $keyinter) {
                $entityIDsAccordion[$keyinter]['title'] = $keyinter;
                $type = array();
                foreach ($entityarray as $entityfeature) {
                    foreach ($entityfeature as $oneentityfeature) {
                        array_push($type, ($oneentityfeature['type'].' ('.$oneentityfeature['email'].')'));
                    }
                    $entityIDsAccordion[$keyinter]['contents'] = array(
                       array(
                         'key' => 'Type',
                         'values' => $type,
                       ),
                    );
                    break;
                }
            }
        }

        $size = 25;
        $smallarray = array_chunk($entityIDsAccordion, $size);

        return $smallarray;
    }

    /**
     * @param $entityIDs
     * @return array
    */
    private function allEntityIDsToAccordion($entityIDs)
    {
        $entityIDsAccordion = array();
        $keys = array_keys($entityIDs['items']);
        foreach ($keys as $key) {
            $entityIDsAccordion[$key]['title'] = $key;

            $entityarray = $entityIDs['items'][$key];
            $type = array();
            foreach ($entityarray as $entityfeature) {
                array_push($type, ($entityfeature['type'].' ('.$entityfeature['email'].')'));
            }
            $entityIDsAccordion[$key]['contents'] = array(
                array(
                    'key' => 'Type',
                    'values' => $type,
                ),
            );
        }

        return $entityIDsAccordion;
    }
}
