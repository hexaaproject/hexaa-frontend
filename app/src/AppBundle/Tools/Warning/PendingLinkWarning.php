<?php
/**
 * Created by PhpStorm.
 * User: gyufi
 * Date: 2018. 03. 08.
 * Time: 8:49
 */

namespace AppBundle\Tools\Warning;

/**
 * Class PendingLinkWarning
 *
 * @package AppBundle\Tools\Warning
 */
class PendingLinkWarning extends Warning
{
    /**
     * @return string
     */
    public function getClass()
    {
        return "Pending connections";
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return "There is some pending connections to organization.";
    }
}
