<?php

namespace AppBundle\Controller;

use AppBundle\Form\OrgManagersContactType;
use AppBundle\Form\ServManagersContactType;
use AppBundle\Form\AdminAttributeSpecType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
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
     * @Route("/attributes/{admin}/{action}", defaults={"action" : false})
     * @Template()
     * @param bool    $admin
     * @param string  $action
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function attributesAction($admin, $action, Request $request)
    {
        $attributespecifications = $this->get('attribute_spec')->cget();

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

                return $this->redirect($request->getUri());
            }
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', $e->getMessage());
        }

        return $this->render(
            'AppBundle:Admin:attributes.html.twig',
            array(
                "organizations" => $this->get('organization')->cget(),
                "services" => $this->get('service')->cget(),
                "admin" => $this->get('principal')->isAdmin()["is_admin"],
                "submenu" => "true",
                'adminsubmenubox' => $this->getAdminSubmenupoints(),
                'attributes_accordion' => $this->attributesToAccordion($attributespecifications),
                'formCreateAttributeSpec' => $formCreateAttributeSpec->createView(),
                'action' => $action,
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
                "organizations" => $this->get('organization')->cget(),
                "services" => $this->get('service')->cget(),
                "admin" => $this->get('principal')->isAdmin()["is_admin"],
                "submenu" => "true",
                "adminsubmenubox" => $this->getAdminSubmenupoints(),
                "principals_buttons" => $principalsButtons,
                "principals" => $principals,
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
                $this->get('principal')->deletePrincipal($this->get('principal')->isAdmin()["is_admin"], $pid);
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
        $entityids = $this->get('entity_id')->cget();
        $totalnumber = $entityids['item_number'];
        $totalpages = ceil($totalnumber / 25);
        $offset = 25;
        $pagesize = 25;
        $verbose = "normal";
        $entitysperpage = array();
        array_push($entitysperpage, $entityids['items']);
        for ($i = 1; $i < $totalpages; $i++) {
            $entityperpage = $this->get('entity_id')->cget($verbose, $offset, $pagesize);
            array_push($entitysperpage, $entityperpage['items']);
            $offset = $offset +25;
        }

        return $this->render(
            'AppBundle:Admin:entity.html.twig',
            array(
                "organizations" => $this->get('organization')->cget(),
                "services" => $this->get('service')->cget(),
                "admin" => $this->get('principal')->isAdmin()["is_admin"],
                "submenu" => "true",
                'adminsubmenubox' => $this->getAdminSubmenupoints(),
                'entityids_accordion' => $this->entityIDsToAccordion($entitysperpage),
                'total_pages' => $totalpages,
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

        $principal = $this->get('principal')->getSelf();

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
                $managers = $this->get('organization')->getManagers($organizationID);

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
                $managers = $this->get('service')->getManagers($serviceID);
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
                "organizations" => $this->get('organization')->cget(),
                "services" => $this->get('service')->cget(),
                "admin" => $this->get('principal')->isAdmin()["is_admin"],
                "submenu" => "true",
                "orgEmailSended" => $orgEmailSended,
                "adminsubmenubox" => $this->getAdminSubmenupoints(),
                "formOrgManagers" => $orgManagersForm->createView(),
                "formManagers" => $managersForm->createView(),
                "servicesName" => $servicesNames,
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
        $this->get('attribute_spec')->deleteAdmin($id);
        $this->get('session')->getFlashBag()->add('success', 'The attribute specification has been deleted.');

        return $this->redirectToRoute("app_admin_attributes", array("admin" => $this->get('principal')->isAdmin()["is_admin"]));
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
     * @param $attributespecifications
     * @return array
     */
    private function attributesToAccordion($attributespecifications)
    {
        $attributesAccordion = array();
        foreach ($attributespecifications['items'] as $attributespecification) {
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
}
