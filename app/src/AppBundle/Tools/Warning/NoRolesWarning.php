<?php
/**
 * Created by PhpStorm.
 * User: gyufi
 * Date: 2018. 03. 08.
 * Time: 8:49
 */

namespace AppBundle\Tools\Warning;


class NoRolesWarning extends Warning
{

    public function getClass()
    {
        return "No roles";
    }

    public function getShortDescription()
    {
        return "This organization hasn't got any role.";
    }
}
