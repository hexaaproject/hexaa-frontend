<?php
/**
 * Created by PhpStorm.
 * User: gyufi
 * Date: 2018. 03. 08.
 * Time: 8:49
 */

namespace AppBundle\Tools\Warning;


class MemberLessRoleWarning extends Warning
{

    public function getClass()
    {
        return "Memberless role";
    }

    public function getShortDescription()
    {
        return "This member hasn't got any role.";
    }
}
