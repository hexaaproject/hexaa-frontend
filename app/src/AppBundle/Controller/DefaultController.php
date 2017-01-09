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

        try {
            $organizations = $this->get('organization')->cget($client);
            $services = $this->get('service')->cget($client);
        } catch (ClientException $e) {
            
            $this->token = null;
            $templateerror = $twig->loadTemcpplate('error.html.twig');
            echo $templateerror->render(array('clientexception'=>$e));
            
           // echo('<br>___.--===(ClientException)===--.___<br>');
           // echo('Message: ' . $e->getMessage() . '<br>');
           //  echo('Call: ' . $e->getRequest()->getUri() . '<br>');
           // echo('Request method: ' . $e->getRequest()->getMethod() . ', body: <br>');
           // echo($e->getRequest()->getBody() . '<br>');
           // echo('Response code: ' . $e->getResponse()->getStatusCode() . ', body: <br>');
           // echo($e->getResponse()->getBody() . '<br>');
        } catch (ServerException $e) {
            $this->token = null;
            $templateerror = $twig->loadTemplate('error.html.twig');
            echo $templateerror->render(array('serverexception'=>$e));
            
            //echo('<br>___.--===(ServerException)===--.___<br>');
            //echo('Message: ' . $e->getMessage() . '<br>');
            //echo('Call: ' . $e->getRequest()->getUri() . '<br>');
            //echo('Request method: ' . $e->getRequest()->getMethod() . ', body: <br>');
            //echo($e->getRequest()->getBody() . '<br>');
            //echo('Response code: ' . $e->getResponse()->getStatusCode() . ', body: <br>');
            //echo($e->getResponse()->getBody() . '<br>');
        } finally {
            if (!isset($organizations)){
                $organizations = [];
            }
            if (!isset($services)){
                $services = [];
            }
        }

        return array('user' => $user, 'organizations' => $organizations, 'services'=>$services);
    }
}
