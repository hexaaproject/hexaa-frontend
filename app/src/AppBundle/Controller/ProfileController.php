<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\ProfilePropertiesType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
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
            'label' => 'FedID',
            "label_attr" => array('class' => 'formlabel'),
            'data' => $propertiesDatas["principalFedID"],
            'attr' => array(
                'class' => 'pull-right',
                'readonly' => true,
            ),
            'required' => false,
        ));

        $profilePropertiesForm->handleRequest($request);

        if ($profilePropertiesForm->isSubmitted() && $profilePropertiesForm->isValid()) {
            $data = $request->request->all();
            $modified = array('display_name' => $data['profile_properties']['principalName'],
                'email' => $data['profile_properties']['principalEmail'], );
            $modified['fedid'] = $data['profile_properties']['principalFedID'];

            $this->get('principal')->editPrincipal($admin, $user['id'], $modified);

            return $this->redirect($request->getUri());
        }


        return $this->render(
            'AppBundle:Profile:index.html.twig',
            array(
                'propertiesbox' => $this->getPropertiesBox(),
                'main' => $user,
                'profilePropertiesForm' => $profilePropertiesForm->createView(),
                'organizations' => $this->getOrganizations(),
                'services' => $this->getServices(),
                'admin' => $this->get('principal')->isAdmin()["is_admin"],
            )
        );
    }

    /**
     * @Route("/history")
     */
    public function historyAction()
    {
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

    /**
     * @return mixed
     */
    private function getOrganizations()
    {
        $organization = $this->get('organization')->cget();

        return $organization;
    }

    /**
     * @return mixed
     */
    private function getServices()
    {
        $services = $this->get('service')->cget();

        return $services;
    }
}
