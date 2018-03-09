<?php
/**
 * Created by PhpStorm.
 * User: gyufi
 * Date: 2018. 03. 08.
 * Time: 8:49
 */

namespace AppBundle\Tools\Warning;

/**
 * Class InvalidServiceWarning
 *
 * @package AppBundle\Tools\Warning
 */
class InvalidServiceWarning extends Warning
{
    /**
     * @return string
     */
    public function getClass()
    {
        return "Invalid service";
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return "This service is not validated by the ServiceProvider owners yet.";
    }
}
