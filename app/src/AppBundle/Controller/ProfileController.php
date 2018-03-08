<?php

namespace AppBundle\Controller;

use AppBundle\Model\AttributeValuePrincipal;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\ProfilePropertiesType;
use AppBundle\Form\ProfileAttributesType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Extension\Core\Type\FormType;

/**
 * @Route("profile")
 */
class ProfileController extends BaseController
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
            "label_attr" => array('class' => 'col-sm-4 profileformlabel'),
            'data' => $propertiesDatas["principalFedID"],
            'attr' => array(
              'disabled' => true,
            ),
            'required' => false,
        ));

        $profilePropertiesForm->handleRequest($request);

        if ($profilePropertiesForm->isSubmitted() && $profilePropertiesForm->isValid()) {
            $data = $request->request->all();
            $modified = array('display_name' => $data['profile_properties']['principalName'],
              'email' => $data['profile_properties']['principalEmail'], );

            $this->get('principal')->editPrincipal($admin, $user['id'], $modified);

            return $this->redirect($request->getUri());
        }

        $services = $this->get('service')->cget();
        $attributespecs = array();
        $attributespecsdetails = array ();
        foreach ($services['items'] as $service) {
            $attributespecsofservice = $this->get('service')->getAttributeSpecs($service['id']);
            $attributespecarray = array();
            $i = 0;
            $len = $attributespecsofservice['item_number'];
            foreach ($attributespecsofservice['items'] as $attributespecofservice) {
                array_push($attributespecarray, $this->get('attribute_spec')->get($attributespecofservice['attribute_spec_id']));
                if ($i == $len -1) {
                    $attributespecsdetails[$attributespecofservice['service_id']] = $attributespecarray;
                }
                $i++;
            }
            array_push($attributespecs, $attributespecsofservice);
        }
        $attributevaluesforprincipal = $this->get('principal')->getAttributeValues('normal', 0, 500);
        $attributespecids = array();
        $linkedservices = array();
        $attributevaluearray = null;
        foreach ($attributevaluesforprincipal['items'] as $attributevalueforprincipal) {
            $linkedservices[$attributevalueforprincipal['id']] = $this->get('attribute_value_principal')->getServicesLinkedToAttributeValue($attributevalueforprincipal['id']);
            if (! in_array($attributevalueforprincipal['attribute_spec_id'], $attributespecids)) {
                $attributevaluearray[$attributevalueforprincipal['attribute_spec_id']] = array();
                array_push($attributespecids, $attributevalueforprincipal['attribute_spec_id']);
            }
            array_push($attributevaluearray[$attributevalueforprincipal['attribute_spec_id']], $attributevalueforprincipal);
        }

        $attributeValuesToAccordion = $this->attributeValuesToAccordion($attributevaluearray, $attributespecsdetails, $services, $request);
        if (false === $attributeValuesToAccordion) { // belső form rendesen le lett kezelve, vissza az alapokhoz
            return $this->redirectToRoute('app_profile_index', array());
        }

        return $this->render(
            'AppBundle:Profile:index.html.twig',
            array(
              'propertiesbox' => $this->getPropertiesBox(),
              'main' => $user,
              'profilePropertiesForm' => $profilePropertiesForm->createView(),
              "organizations" => $this->get('organization')->cget(),
              "services" => $services,
              "attributeValuesToAccordion" => $attributeValuesToAccordion,
              'admin' => $this->get('principal')->isAdmin()["is_admin"],
              'organizationsWhereManager' => $this->orgWhereManager(),
              'manager' => "false",
              'ismanager' => "true",
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
            'organizationsWhereManager' => $this->orgWhereManager(),
            'manager' => "false",
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
     * @Route("/{servId}/attribute/{id}/{attributespecid}/delete")
     * @Template()
     * @return Response
     * @param int $servId          Service id
     * @param int $id              attributevalue Id
     * @param int $attributespecid attribute specification Id
     *
     */
    public function attributeDeleteAction($servId, $id, $attributespecid)
    {
        $this->get('attribute_value_principal')->delete($id);

        return $this->redirectToRoute("app_profile_index");
    }

    /**
     * @param $attributevaluearray
     * @param $attributespecs
     * @param $services
     * @param $request
     * @return array
     */
    private function attributeValuesToAccordion($attributevaluearray, $attributespecs, $services, $request)
    {
        $attributevaluesAccordion = array();
        $formFactory = Forms::createFormFactory();

        foreach ($services['items'] as $service) {
            $claim = false;
            $servicespecs = $this->get('service')->getAttributeSpecs($service['id']);
            if ($servicespecs['item_number'] != 0) {
                $data = [];
                foreach ($attributespecs as $key => $values) {
                    if ($key == $service['id']) {
                        foreach ($values as $value) {
                            foreach ($attributevaluearray as $key => $attributevalues) {
                                if ($key == $value['id']) {
                                    $attribute = [];
                                    if ($value['is_multivalue'] == true) {
                                        foreach ($attributevalues as $attributevalue) {
                                            if (in_array($service['id'], $attributevalue['service_ids'])) {
                                                array_push($attribute, $attributevalue['value']);
                                            }
                                        }
                                    } else {
                                        foreach ($attributevalues as $attributevalue) {
                                            if (in_array($service['id'], $attributevalue['service_ids'])) {
                                                $attribute = $attributevalue['value'];
                                            }
                                        }
                                    }
                                    $name = str_replace(' ', '', $value['name']);
                                    $data[$value['id']] = $attribute;
                                }
                            }
                        }
                    }
                }
                $formBuilder = $formFactory->createNamedBuilder($service['id'], FormType::class, $data);

                $attributevaluesAccordion[$service['id']]['id'] = $service['id'];
                $attributevaluesAccordion[$service['id']]['title'] = $service['name'];
                $attributespecnames = [];
                foreach ($attributespecs as $key => $values) {
                    if ($key == $service['id']) {
                        foreach ($values as $value) {
                            $attributevaluestring = null;
                            $namevalue = [];
                            $deleteurls = [];
                            foreach ($attributevaluearray as $key => $attributevalues) {
                                if ($key == $value['id']) {
                                    if ($value['is_multivalue'] == true) {
                                        $formBuilder->add($value['id'], CollectionType::class, [
                                            "entry_type" => TextType::class,
                                            "allow_add" => true,
                                            /* 'prototype_data' => 'New Tag Placeholder',*/
                                            "label" => $value['name'],
                                            "entry_options" => [
                                              "label" => false,
                                              "attr" => ['class' => 'multivalue'],
                                            ],
                                        ]);
                                    } else {
                                        $formBuilder->add($value['id'], TextType::class, ["label" => $value['name'], "attr" => ['class' => 'nonmultivalue'], "data" => $data[$value['id']]]);
                                    }
                                    foreach ($attributevalues as $attributevalue) {
                                        if (in_array($service['id'], $attributevalue['service_ids'])) {
                                            array_push($namevalue, $attributevalue['value']);

                                            array_push($deleteurls, $this->generateUrl("app_profile_attributedelete", [
                                                'servId' => $service['id'],
                                                'id' => $attributevalue['id'],
                                                'attributespecid' => $attributevalue['attribute_spec_id'],
                                                'action' => "delete",
                                            ]));
                                        }
                                    }
                                }
                            }
                            if (empty($namevalue)) {
                                $namevalue = ["Még nincs érték"];
                                if ($value['is_multivalue'] == true) {
                                      $formBuilder->add($value['id'], CollectionType::class, [
                                          "entry_type" => TextType::class,
                                          "allow_add" => true,
                                          /* 'prototype_data' => 'New Tag Placeholder',*/
                                          "label" => $value['name'],
                                          "entry_options" => ["label" => false],
                                      ]);
                                } else {
                                    $formBuilder->add($value['id'], TextType::class, ["label" => $value['name']]);
                                }
                            }
                            array_push($attributespecnames, [
                                'key' => ($value['name']),
                                'values' => $namevalue,
                                'deleteurl' => $deleteurls,
                            ]);
                            foreach ($namevalue as $onenamevalue) {
                                if ($onenamevalue == "Még nincs érték") {
                                    if ($claim != true) {
                                        $claim = true;
                                    }
                                }
                            }
                        }
                    }
                }

                $attributevaluesAccordion[$service['id']]['contents'] = $attributespecnames;
                $form = $formBuilder->getForm();
                $form->handleRequest();
                if ($form->isSubmitted()) {
                    try {
                        $data = $form->getData();
                        $attributevalues = $this->get('principal')->getAttributeValues();
                        $attributespecswithvalues = [];
                        $attributespecswithvalues2 = [];
                        $attributevaluesname = [];
                        foreach ($attributevalues['items'] as $attributevalue) {
                            if (in_array($form->getName(), $attributevalue['service_ids'])) {
                                array_push($attributespecswithvalues, $attributevalue['attribute_spec_id']);
                                $attributevaluesname[$attributevalue['attribute_spec_id']][$attributevalue['id']] = $attributevalue['value'];
                            }
                            array_push($attributespecswithvalues2, $attributevalue['attribute_spec_id']);
                        }
                        $principal = $this->get('principal')->getSelf();
                        foreach ($data as $key => $value) {
                            if ($value != null) {
                                $servicesids = [];
                                $services = $this->get('attribute_spec')->getServicesLinkedToAttributeSpec($key);
                                if (!in_array($key, $attributespecswithvalues)) {
                                    foreach ($services['items'] as $servicesone) {
                                        array_push($servicesids, $servicesone["service_id"]);
                                    }
                                    $justmodifyinformactive = false;
                                    foreach ($attributevalues['items'] as $attributevalue) {
                                        foreach ($servicesids as $oneserviceid) {
                                            if (in_array($oneserviceid, $attributevalue['service_ids']) && $attributevalue['attribute_spec_id'] == $key) {
                                                $justmodifyinformactive = true;
                                            }
                                        }
                                    }
                                    if ($justmodifyinformactive == true) {
                                        if (is_array($value)) {
                                            foreach ($value as $onevalue) {
                                                if ($onevalue != null) {
                                                    $this->get('attribute_value_principal')->postAttributeValue([$form->getName()], $onevalue, $key, $principal['id']);
                                                }
                                            }
                                        } else {
                                            if ($value != null) {
                                                $this->get('attribute_value_principal')->postAttributeValue([$form->getName()], $value, $key, $principal['id']);
                                            }
                                        }
                                    } else {
                                        if (is_array($value)) {
                                            foreach ($value as $onevalue) {
                                                foreach ($servicesids as $servicesid) {
                                                    if ($onevalue != null) {
                                                        $this->get('attribute_value_principal')->postAttributeValue([$servicesid], $onevalue, $key, $principal['id']);
                                                    }
                                                }
                                            }
                                        } else {
                                            foreach ($servicesids as $servicesid) {
                                                if ($value != null) {
                                                    $this->get('attribute_value_principal')->postAttributeValue([$servicesid], $value, $key, $principal['id']);
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    if (is_array($value)) {
                                        foreach ($value as $onevalue) {
                                            if (!in_array($onevalue, $attributevaluesname[$key])) {
                                                $allvaluefromuser = array();
                                                foreach ($data as $key2 => $value2) {
                                                    if ($key2 == $key) {
                                                        array_push($allvaluefromuser, $value2);
                                                    }
                                                }
                                                $allvaluefrombackend = array();
                                                $attributespectoname = $this->get('attribute_spec')->get($key);
                                                foreach ($attributespecnames as $attributespecname) {
                                                    if ($attributespecname['key'] == $attributespectoname['name']) {
                                                        $allvaluefrombackend = $attributespecname['values'];
                                                    }
                                                }
                                                $missingvalues = array_diff($allvaluefrombackend, $allvaluefromuser[0]);
                                                if (empty($missingvalues) or $missingvalues[0] == "Még nincs érték") {
                                                    if ($onevalue != null) {
                                                        $this->get('attribute_value_principal')->postAttributeValue([$form->getName()], $onevalue, $key, $principal['id']);
                                                    }
                                                }

                                                foreach ($missingvalues as $missingvalue) {
                                                    foreach ($attributevalues['items'] as $attributevalue) {
                                                        if ($attributevalue['value'] == $missingvalue && $attributevalue['attribute_spec_id'] == $key && in_array($form->getName(), $attributevalue['service_ids'])) {
                                                            $missingvalueid = $attributevalue['id'];
                                                            $servids = $attributevalue['service_ids'];
                                                            if (($keyarray = array_search($form->getName(), $servids)) !== false) {
                                                                unset($servids[$keyarray]);
                                                            }
                                                            $this->get('attribute_value_principal')->patch($missingvalueid, [
                                                                'services' => $servids,
                                                                'principal' => $principal['id'],
                                                                'attribute_spec' => $key,
                                                            ]);
                                                            if ($onevalue != null) {
                                                                $this->get('attribute_value_principal')->postAttributeValue([$form->getName()], $onevalue, $key, $principal['id']);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        if (!in_array($value, $attributevaluesname[$key])) {
                                            $allvaluefromuser = array();
                                            foreach ($data as $key2 => $value2) {
                                                if ($key2 == $key) {
                                                    array_push($allvaluefromuser, $value2);
                                                }
                                            }
                                            $allvaluefrombackend = array();
                                            $attributespectoname = $this->get('attribute_spec')->get($key);
                                            foreach ($attributespecnames as $attributespecname) {
                                                if ($attributespecname['key'] == $attributespectoname['name']) {
                                                    $allvaluefrombackend = $attributespecname['values'];
                                                }
                                            }
                                            $missingvalues = array_diff($allvaluefrombackend, $allvaluefromuser);
                                            if (empty($missingvalues) or $missingvalues[0] == "Még nincs érték") {
                                                if ($value != null) {
                                                    $this->get('attribute_value_principal')->postAttributeValue([$form->getName()], $value, $key, $principal['id']);
                                                }
                                            }

                                            foreach ($missingvalues as $missingvalue) {
                                                foreach ($attributevalues['items'] as $attributevalue) {
                                                    if ($attributevalue['value'] == $missingvalue && $attributevalue['attribute_spec_id'] == $key && in_array($form->getName(), $attributevalue['service_ids'])) {
                                                        $missingvalueid = $attributevalue['id'];
                                                        $servids = $attributevalue['service_ids'];
                                                        if (($keyarray = array_search($form->getName(), $servids)) !== false) {
                                                            unset($servids[$keyarray]);
                                                        }
                                                        $this->get('attribute_value_principal')->patch($missingvalueid, [
                                                            'services' => $servids,
                                                            'principal' => $principal['id'],
                                                            'attribute_spec' => $key,
                                                        ]);
                                                        if ($value != null) {
                                                            $this->get('attribute_value_principal')->postAttributeValue([$form->getName()], $value, $key, $principal['id']);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } catch (\AppBundle\Exception $exception) {
                        $form->addError(new FormError($exception->getMessage()));
                    }
                    if ($form->getErrors(true)->count() == 0) {
                        $this->get('session')->getFlashBag()->add('success', 'Attribute values modified succesfully.');
                    }
                    if (!$form->getErrors(true)->count()) { // false-szal térünk vissza, ha nincs hiba. Mehessen a redirect az alaphoz.
                        return false;
                    }
                }
                $attributevaluesAccordion[$service['id']]['form'] = $form->createView();
                $attributevaluesAccordion[$service['id']]['claim'] = $claim;
            }
        }

        return $attributevaluesAccordion;
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
