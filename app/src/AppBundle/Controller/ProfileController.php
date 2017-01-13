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
        }

        return $this->render('AppBundle:Profile:index.html.twig', array(
            'organizations' => $organizations, 'services'=>$services, 'client'=>$client
        ));
    }

}
