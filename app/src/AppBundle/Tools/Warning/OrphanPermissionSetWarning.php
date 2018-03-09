<?php
/**
 * User: gyufi
 * Date: 2018. 03. 08.
 * Time: 8:49
 */

namespace AppBundle\Tools\Warning;

/**
 * Class OrphanPermissionSetWarning
 *
 * @package AppBundle\Tools\Warning
 */
class OrphanPermissionSetWarning extends Warning
{
    /**
     * @return string
     */
    public function getClass()
    {
        return "Orphan permission set";
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return "This permission set is not binded to any Organization.";
    }
}
