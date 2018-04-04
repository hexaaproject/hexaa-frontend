<?php
/**
 * Created by PhpStorm.
 * User: gyufi
 * Date: 2017. 12. 12.
 * Time: 13:40
 */

namespace AppBundle\Controller;

use AppBundle\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class BaseController
 *
 * @package AppBundle\Controller
 */
class BaseController extends Controller
{
    /**
     * @param $entity
     *
     * @return bool
     * @throws \Exception
     *
     *  TODO
     */
    protected function amIManagerOfThis($entity)
    {
        if (!$entity) {
            throw new Exception("TODO");
        }

        return true;
    }

    /**
     *
     * @return array
     * @throws \Exception
     *
     *  TODO
     */
    protected function orgWhereManager()
    {
        return $this->get('principal')->orgsWhereUserIsManager($this->get('session')->get('hexaaAdmin'));
    }

    protected function getEntityShowPath($entity, $manager)
    {
        if ($this instanceof OrganizationController) {
            if ($manager == "true") {
                return $this->generateUrl("app_organization_show", ["id" => $entity["id"]]);
            } else {
                return $this->generateUrl("app_organization_properties", ["id" => $entity["id"]]);
            }
        }

        return "#";
    }
}
