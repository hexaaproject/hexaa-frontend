<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\ProfilePropertiesType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("profile")
 */
class ProfileController extends Controller
{
    /**
     * @Route("/index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $user = $this->get('principal')->getSelf();
        $admin = $this->get('principal')->isAdmin()["is_admin"];

        $propertiesDatas["principalFedID"] = $user["fedid"];
        $propertiesDatas["principalName"] = $user["display_name"];
        $propertiesDatas["principalEmail"] = $user["email"];

        $profilePropertiesForm = $this->createForm(
            ProfilePropertiesType::class,
            array(
                'properties' => $propertiesDatas,
            )
        );

        $profilePropertiesForm->add('principalFedID', TextType::class, array(
            'label' => 'Federal ID',
            "label_attr" => array('class' => 'col-sm-4 formlabel'),
            'data' => $propertiesDatas["principalFedID"],
            'attr' => array(
                'class' => ' pull-right',
                'disabled' => true,
            ),
            'required' => false,
        ));

        $profilePropertiesForm->handleRequest($request);

        if ($profilePropertiesForm->isSubmitted() && $profilePropertiesForm->isValid()) {
            $data = $request->request->all();
            $modified = array('display_name' => $data['profile_properties']['principalName'],
                'email' => $data['profile_properties']['principalEmail'], );
           // $modified['fedid'] = $data['profile_properties']['principalFedID'];

            $this->get('principal')->editPrincipal($admin, $user['id'], $modified);

            return $this->redirect($request->getUri());
        }

        return $this->render(
            'AppBundle:Profile:index.html.twig',
            array(
                'propertiesbox' => $this->getPropertiesBox(),
                'main' => $user,
                'profilePropertiesForm' => $profilePropertiesForm->createView(),
                "organizations" => $this->get('organization')->cget(),
                "services" => $this->get('service')->cget(),
                'admin' => $this->get('principal')->isAdmin()["is_admin"],
            )
        );
    }

    /**
     * Get the history of the principal.
     * @Route("/history")
     * @Template()
     * @return array
     */
    public function historyAction()
    {

        return array(
            "organizations" => $this->get('organization')->cget(),
            "services" => $this->get('service')->cget(),
            "admin" => $this->get('principal')->isAdmin()["is_admin"],
        );
    }

    /**
     * @Route("/history/json")
     * @param integer|null $offset   Offset
     * @param integer      $pageSize Pagesize
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function historyJSONAction($offset = null, $pageSize = 25)
    {
        $data = $this->get('principal')->getHistory();
        for ($i = 0; $i < $data['item_number']; $i++) {
            $dateTime = new \DateTime($data['items'][$i]['created_at']);
            $data['items'][$i]['created_at'] =  "<div style='white-space: nowrap'>".$dateTime->format('Y-m-d H:i')."</div>";
        }
        $response = new JsonResponse($data);

        return $response;
    }

    /**
     * @return array
     */
    private function getPropertiesBox()
    {
        $propertiesbox = array(
            "Name" => "display_name",
            "Email" => "email",
            "Federal ID" => "fedid",
        );

        return $propertiesbox;
    }
}
