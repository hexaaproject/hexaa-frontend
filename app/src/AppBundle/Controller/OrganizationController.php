<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Model\Organization;
use AppBundle\Model\Service;
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
        $organizations = $this->getOrganizations();
        $services = $this->getServices();
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
                'organizations' => $this->getOrganizations(),
                'services' => $this->getServices(),
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
        $roles = $this->getRoles($organization);
        return $this->render(
            'AppBundle:Organization:properties.html.twig',
            array(
                "organization" => $organization,
                "roles" => $roles,
                "organizations" => $this->getOrganizations(),
                "services" => $this->getServices()
            )
        );
    }

    /**
     * @Route("/users/{id}")
     * @Template()
     */
    public function usersAction($id)
    {
        $organization = $this->getOrganization($id);
        $managers = $this->getManagers($organization);
        $members = $this->getMembers($organization);
        return $this->render(
            'AppBundle:Organization:users.html.twig',
            array(
                "managers" => $managers,
                "members" => $members,
                "organization" => $organization,
                "organizations" => $this->getOrganizations(),
                "services" => $this->getServices()
            )
        );
    }

    /**
     * @Route("/roles/{id}")
     * @Template()
     */
    public function rolesAction($id)
    {
        $organization = $this->getOrganization($id);
        return $this->render(
            'AppBundle:Organization:roles.html.twig',
            array(
                "organization" => $organization,
                "roles" => $this->getRoles($organization)
            )
        );
    }

    /**
     * @Route("/connectedservices/{id}")
     * @Template()
     */
    public function connectedservicesAction($id)
    {
        return $this->render(
            'AppBundle:Organization:connectedservices.html.twig',
            array(
                "organization" => $this->getOrganization($id)
            )
        );
    }


    private function getOrganization($id)
    {
        $client = $this->getUser()->getClient();
        $organization = Organization::get($client, $id);
        return $organization;
    }

    private function getOrganizations()
    {
        $client = $this->getUser()->getClient();
        $organization = Organization::cget($client);
        return $organization;
    }

    private function getServices()
    {
        $client = $this->getUser()->getClient();
        $organization = Service::cget($client);
        return $organization;
    }

    private function getRoles($organization)
    {
        $verbose = "expanded";
        $roles = Organization::rget($this->getUser()->getClient(), $organization['id'], $verbose);
        return $roles;
    }

    private function getManagers($organization)
    {
        return Organization::managersget($this->getUser()->getClient(), $organization['id']);
    }

    private function getMembers($organization)
    {
        return Organization::membersget($this->getUser()->getClient(), $organization['id']);
    }
}
