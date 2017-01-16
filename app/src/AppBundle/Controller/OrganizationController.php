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
        try {
            $organizationid = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            $menu = filter_input(INPUT_GET, 'menu');
            if (!$menu) {
                $menu = "main";
            }
            $client = $this->getUser()->getClient();

            $organization = null;
            $name = '';
            $roles = array();
            $principals = array();
            $managers = array();
            $members = array();

            if ($organizationid) {
                $organization = Organization::get($client, $organizationid);
                $droleid = $organization['default_role_id'];
                $verbose = "expanded";
                $roles = Organization::rget($client, $organizationid, $verbose);
                foreach ($roles as $value) {
                    if ($value['id'] == $droleid) {
                        $name = $value['name'];
                    }
                }
            }
            $organizations = Organization::cget($client);
            $services = Service::cget($client);

            $managers = Organization::managersget($client, $organizationid);
            $members = Organization::membersget($client, $organizationid);
        } catch (ClientException $e) {
            $this->token = null;
            return $this->render('error.html.twig', array('clientexception' => $e));
        } catch (ServerException $e) {
            $this->token = null;
            return $this->render('error.html.twig', array('serverexception' => $e));
        } finally {
            if (!isset($organizations)) {
                $organizations = [];
            }
            if (!isset($services)) {
                $services = [];
            }
        }

        return $this->render('AppBundle:Organization:index.html.twig', array('organization' => $organization, 'organizations' => $organizations, 'services' => $services, 'menu' => $menu, 'drolename' => $name, 'roles' => $roles, 'principals' => $principals, 'managers' => $managers, 'members' => $members));
        // return array('organization' => $organization, 'organizations' => $organizations, 'services' => $services, 'menu' => $menu, 'drolename' => $name, 'roles'=>$roles, 'principals'=>$principals, 'managers'=>$managers, 'members'=>$members); TODO template para a twig engine-ben : https://github.com/symfony/symfony/pull/21177
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
        return $this->render(
            'AppBundle:Organization:properties.html.twig',
            array(
                "organization" => $this->getOrganization($id),
            )
        );
    }

    /**
     * @Route("/users/{id}")
     * @Template()
     */
    public function usersAction($id)
    {
        return $this->render(
            'AppBundle:Organization:users.html.twig',
            array(
                "organization" => $this->getOrganization($id),
            )
        );
    }

    /**
     * @Route("/roles/{id}")
     * @Template()
     */
    public function rolesAction($id)
    {
        return $this->render(
            'AppBundle:Organization:roles.html.twig',
            array(
                "organization" => $this->getOrganization($id)
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
}
