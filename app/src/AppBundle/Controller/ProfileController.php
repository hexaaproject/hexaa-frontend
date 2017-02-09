<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Model\Organization;
use AppBundle\Model\Service;
/**
 * @Route("profile")
 */
class ProfileController extends Controller
{
    /**
     * @Route("/index")
     */
    public function indexAction()
    {
        $client = $this->getUser()->getClient();
        $user = $this->get('principal')->principalinfo($client);
        return $this->render(
            'AppBundle:Profile:index.html.twig',
                array(
                    'propertiesbox' => $this->getPropertiesBox(),
                    'main' => $user
                )
        );
        /*try {
            $organizations = $this->get('organization')->cget($client);
            $services = $this->get('service')->cget($client);
            $user = $this->get('principal')->principalinfo($client);
            
            $verbose = "expanded";
            $userattributes = $this->get('principal')->attributeget($client, $verbose);
            $newuserattribute = $this->get('principal')->attributespecsget($client, $verbose);


        } catch (ClientException $e) {            
            $this->token = null;
            return $this->render('error.html.twig', array('clientexception'=>$e));
        } catch (ServerException $e) {
            $this->token = null;
            return $this->render('error.html.twig', array('serverexception'=>$e));
        } finally {
            if (!isset($organizations)){
                $organizations = [];
            }
            if (!isset($services)){
                $services = [];
            }
            if (!isset($user)){
                $user = [];
            }
        }

        return $this->render('AppBundle:Profile:index.html.twig', array(
            'organizations' => $organizations, 'services'=>$services, 'user'=>$user, 'userattributes'=>$userattributes, 'userattr'=>$newuserattribute
        ));*/
    }
    
     /**
     * @Route("/history")
     */
    public function historyAction(){
        
    }
    
    private function getPropertiesBox() {
        $propertiesbox = array(
            "Name" => "display_name",
            "Email" => "email",
            "Federal ID" => "fedid",
        );

        return $propertiesbox;
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
