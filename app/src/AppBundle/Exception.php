<?php
/**
 * Created by PhpStorm.
 * User: gyufi
 * Date: 2017. 12. 12.
 * Time: 14:31
 */

namespace AppBundle;

use Throwable;

/**
 * Class Exception
 *
 * @package AppBundle
 */
class Exception extends \Exception
{
    /**
     * Exception constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
