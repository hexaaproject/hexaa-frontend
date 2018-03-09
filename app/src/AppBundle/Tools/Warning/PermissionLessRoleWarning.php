<?php
/**
 * Created by PhpStorm.
 * User: gyufi
 * Date: 2018. 03. 08.
 * Time: 8:49
 */

namespace AppBundle\Tools\Warning;


class PermissionLessRoleWarning extends Warning
{
    public function getClass()
    {
        return "Permissionless role";
    }

    public function getShortDescription()
    {
        return "This role hasn't got any permission.";
    }
}
