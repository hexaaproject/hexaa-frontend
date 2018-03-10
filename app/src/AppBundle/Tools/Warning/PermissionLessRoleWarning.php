<?php
/**
 * Created by PhpStorm.
 * User: gyufi
 * Date: 2018. 03. 08.
 * Time: 8:49
 */

namespace AppBundle\Tools\Warning;

/**
 * Class PermissionLessRoleWarning
 *
 * @package AppBundle\Tools\Warning
 */
class PermissionLessRoleWarning extends Warning
{
    /**
     * @return string
     */
    public function getClass()
    {
        return "Permissionless role";
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return "This role hasn't got any permission.";
    }
}
