<?php
/**
 * Created by PhpStorm.
 * User: gyufi
 * Date: 2018. 03. 08.
 * Time: 8:49
 */

namespace AppBundle\Tools\Warning;

/**
 * Class NoRolesWarning
 *
 * @package AppBundle\Tools\Warning
 */
class NoRolesWarning extends Warning
{

    /**
     * @return string
     */
    public function getClass()
    {
        return "No roles";
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return "This organization hasn't got any role.";
    }
}
