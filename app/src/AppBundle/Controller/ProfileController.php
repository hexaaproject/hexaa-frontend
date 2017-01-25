<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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
        try {
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
        ));
    }
    
     /**
     * @Route("/history")
     */
    public function historyAction(){
        
    }

}
