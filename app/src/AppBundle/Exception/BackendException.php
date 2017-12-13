<?php

namespace AppBundle\Exception;

use AppBundle\Exception;
use Throwable;

/**
 * Created by PhpStorm.
 * User: gyufi
 * Date: 2017. 12. 12.
 * Time: 15:09
 */
class BackendException extends Exception
{
    /**
     * BackendException constructor.
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
