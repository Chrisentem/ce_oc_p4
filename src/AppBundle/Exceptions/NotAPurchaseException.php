<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 23/10/2018
 * Time: 10:44
 */

namespace AppBundle\Exceptions;



class NotAPurchaseException extends \Exception
{
    /**
     * NotAPurchaseException constructor.
     * @param $message
     * @param int $code
     */
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->message;
    }
}