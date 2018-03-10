<?php
/**
 * Created by PhpStorm.
 * User: gyufi
 * Date: 2018. 03. 08.
 * Time: 8:49
 */

namespace AppBundle\Tools\Warning;

/**
 * Class RoleLessMemberWarning
 *
 * @package AppBundle\Tools\Warning
 */
class RoleLessMemberWarning extends Warning
{
    /**
     * @return string
     */
    public function getClass()
    {
        return "Roleless member";
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return "This member hasn't got any role.";
    }
}
