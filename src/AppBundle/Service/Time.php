<?php declare(strict_types=1);

namespace AppBundle\Service;

use DateTime;

class Time
{
    /**
     * @return DateTime
     */
    public static function currentDay(): DateTime
    {
        return DateTime::createFromFormat('U', (string) time())->setTime(0, 0);
    }

    /**
     * @return DateTime
     */
    public static function currentDateTime(): DateTime
    {
        return DateTime::createFromFormat('U', (string) time());
    }
}