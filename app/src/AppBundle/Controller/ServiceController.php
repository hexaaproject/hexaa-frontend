<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/service")
 */
class ServiceController extends Controller
{
    /**
     * @Route("/index")
     */
    public function indexAction()
    {

    	// copy + paste from service.php
    	try {
		    $serviceid = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
		    $menu = filter_input(INPUT_GET,'menu');
		    if (!$menu) {
		        $menu = "main";
		    }
		    if ($serviceid) {
		        $service = \Hexaa\Newui\Model\Service::get($client, $serviceid);
		    }
		    $organizations = \Hexaa\Newui\Model\Organization::cget($client);
		 
		    $services = \Hexaa\Newui\Model\Service::cget($client);
		    
		} catch (ClientException $e) {
		    return $this->render('error.html.twig', array('clientexception'=>$e));
		} catch (ServerException $e) {
		    return $this->render('error.html.twig', array('serverexception'=>$e));
		} finally {
		    //?

        return $this->render('AppBundle:Service:index.html.twig', array(
            'service' => $service,
            'organizations' => $organizations,
            'services' => $services,
            'menu' => $menu
        	)
        );
    }

}
