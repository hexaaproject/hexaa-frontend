<?php
/**
 * Created by PhpStorm.
 * User: gyufi
 * Date: 2018. 03. 08.
 * Time: 8:49
 */

namespace AppBundle\Tools\Warning;

/**
 * Class DisabledServiceWarning
 *
 * @package AppBundle\Tools\Warning
 */
class DisabledServiceWarning extends Warning
{
    /**
     * @return string
     */
    public function getClass()
    {
        return "Service not enabled";
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return "This service is not enabled by the ServiceProvider owners yet.";
    }
}
