<?php
/**
 * Created by PhpStorm.
 * User: gyufi
 * Date: 2018. 03. 08.
 * Time: 8:49
 */

namespace AppBundle\Tools\Warning;


class RoleLessMemberWarning extends Warning
{

    public function getClass()
    {
        return "Roleless member";
    }
}
