<?php
/**
 * Created by PhpStorm.
 * User: gyufi
 * Date: 2018. 03. 08.
 * Time: 8:49
 */

namespace AppBundle\Tools\Warning;

/**
 * Class OrphanPermissionWarning
 *
 * @package AppBundle\Tools\Warning
 */
class OrphanPermissionWarning extends Warning
{

    /**
     * @return string
     */
    public function getClass()
    {
        return "Orphan permission";
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return "This permission is not binded to any Organization nor Permission set.";
    }
}
