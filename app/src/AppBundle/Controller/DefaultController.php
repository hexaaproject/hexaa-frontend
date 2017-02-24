<?php

namespace AppBundle\Controller;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction(Request $request)
    {

        $client = $this->getUser()->getClient();
        try {
            $organizations = $this->get('organization')->cget();
            $services = $this->get('service')->cget();
            

        } catch (ClientException $e) {            
            return $this->render('error.html.twig', array('clientexception'=>$e));
        } catch (ServerException $e) {
            return $this->render('error.html.twig', array('serverexception'=>$e));
        } finally {
            if (!isset($organizations)){
                $organizations = [];
            }
            if (!isset($services)){
                $services = [];
            }
        }

        return $this->render('AppBundle:Default:index.html.twig', array('organizations' => $organizations, 'services' =>$services, 'orgsubmenubox' => $this->getOrgSubmenuPoints(), 'servsubmenubox' => $this->getServSubmenuPoints()));
    }
    
    private function getOrgSubmenuPoints(){
        $submenuBox = array(
            "app_organization_properties" => "Properties",
            "app_organization_users" => "Users",
            "app_organization_roles" => "Roles",
            "app_organization_connectedservices" => "Conencted services",
        );
        
        return $submenuBox;
    }
    
    private function getServSubmenuPoints(){
        $submenuBox = array(
            "app_service_properties" => "Properties",
            "app_service_managers" => "Managers",
            "app_service_attributes" => "Attributes",
            "app_service_permissions" => "Permissions",
            "app_service_permissionssets" => "Permissions sets",
            "app_service_connectedorganizations" => "Connected organizations"
        );
        
        return $submenuBox;
    }
}
