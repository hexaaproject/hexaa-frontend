<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Model\Organization;
use AppBundle\Model\Service;

/**
 * @Route("/organization")
 */
class OrganizationController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
		try {
		    $organizationid = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
		    $menu = filter_input(INPUT_GET,'menu');
		    if (!$menu) {
		        $menu = "main";
		    }
		    $client = $this->getUser()->getClient();

		    $organization = null;
		    $name='';
		    $roles = array();
		    $principals = array();
		    $managers = array();
		    $members = array();

		    if ($organizationid) {
		        $organization = Organization::get($client, $organizationid);
		        $droleid=$organization['default_role_id'];
		        $verbose="expanded";
		        $roles=Organization::rget($client, $organizationid, $verbose);
		        foreach ($roles as $value){
		            if($value['id']==$droleid){
		                $name=$value['name'];
		            }
		        }
		    }
		    $organizations = Organization::cget($client);
		    $services = Service::cget($client);
		    
		    $managers = Organization::managersget($client, $organizationid);
		    $members = Organization::membersget($client, $organizationid);
		    
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

		return $this->render('AppBundle:Organization:index.html.twig', array('organization' => $organization, 'organizations' => $organizations, 'services' => $services, 'menu' => $menu, 'drolename' => $name, 'roles'=>$roles, 'principals'=>$principals, 'managers'=>$managers, 'members'=>$members));
		// return array('organization' => $organization, 'organizations' => $organizations, 'services' => $services, 'menu' => $menu, 'drolename' => $name, 'roles'=>$roles, 'principals'=>$principals, 'managers'=>$managers, 'members'=>$members); TODO template para a twig engine-ben : https://github.com/symfony/symfony/pull/21177
    }

    /**
     * @Route("/add")
     * @Template()
     */
    public function addAction(Request $request)
    {
    	return $this->render('AppBundle:Organization:add.html.twig', array());
    }

}
