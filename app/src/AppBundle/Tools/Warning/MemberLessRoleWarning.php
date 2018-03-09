<?php
/**
 * Created by PhpStorm.
 * User: gyufi
 * Date: 2018. 03. 08.
 * Time: 8:49
 */

namespace AppBundle\Tools\Warning;

/**
 * Class MemberLessRoleWarning
 *
 * @package AppBundle\Tools\Warning
 */
class MemberLessRoleWarning extends Warning
{
    /**
     * @return string
     */
    public function getClass()
    {
        return "Memberless role";
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return "This member hasn't got any role.";
    }
}
