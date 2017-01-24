<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Model\Organization;
use AppBundle\Model\Service;

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
                $service = \Hexaa\Newui\Model\Service::get($client, $serviceid);
            }
            $organizations = \Hexaa\Newui\Model\Organization::cget($client);

            $services = \Hexaa\Newui\Model\Service::cget($client);
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
            'service' => $this->getService($id)
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
            'service' => $this->getService($id)
            )
        );
    }

    /**
     * @Route("/managers/{id}")
     * @Template()
     */
    public function managersAction($id) {
        return $this->render(
                        'AppBundle:Service:managers.html.twig', array(
                    'organizations' => $this->getOrganizations(),
                    'services' => $this->getServices(),
                    'service' => $this->getService($id)
                        )
        );
    }

    /**
     * @Route("/attributes/{id}")
     * @Template()
     */
    public function attributesAction($id) {
        return $this->render(
                        'AppBundle:Service:attributes.html.twig', array(
                    'organizations' => $this->getOrganizations(),
                    'services' => $this->getServices(),
                    'service' => $this->getService($id)
                        )
        );
    }

    /**
     * @Route("/permissions/{id}")
     * @Template()
     */
    public function permissionsAction($id) {
        return $this->render(
                        'AppBundle:Service:permissions.html.twig', array(
                    'organizations' => $this->getOrganizations(),
                    'services' => $this->getServices(),
                    'service' => $this->getService($id)
                        )
        );
    }

    /**
     * @Route("/permissionssets/{id}")
     * @Template()
     */
    public function permissionssetsAction($id) {
        return $this->render(
                        'AppBundle:Service:permissionssets.html.twig', array(
                    'organizations' => $this->getOrganizations(),
                    'services' => $this->getServices(),
                    'service' => $this->getService($id)
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

    private function getOrganizations() {
        $client = $this->getUser()->getClient();
        $organization = Organization::cget($client);
        return $organization;
    }

    private function getServices() {
        $client = $this->getUser()->getClient();
        $services = Service::cget($client);
        return $services;
    }

    private function getService($id) {
        $client = $this->getUser()->getClient();
        $service = Service::get($client, $id);
        return $service;
    }

}
