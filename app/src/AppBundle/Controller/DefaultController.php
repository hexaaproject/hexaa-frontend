<?php

namespace AppBundle\Controller;

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
            $organizations = $this->get('organization')->cget($client);
            $services = $this->get('service')->cget($client);

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

        return $this->render('AppBundle:Default:index.html.twig', array('organizations' => $organizations, 'services'=>$services));
    }
}
