<?php
/**
 * Created by PhpStorm.
 * User: gyufi
 * Date: 2018. 03. 09.
 * Time: 9:09
 */

namespace AppBundle\Tools\Warning;

/**
 * Interface WarningableInterface
 *
 * @package AppBundle\Tools\Warning
 */
interface WarningableInterface
{
    /**
     * @param string $hexaaAdmin
     * @param string $id
     * @param array  $resources
     *
     * @return mixed
     */
    public function getWarnings(string $hexaaAdmin, $id, array $resources);
}
