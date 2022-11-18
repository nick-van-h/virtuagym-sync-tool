<?php

namespace Vst\Model\Calendar;

//use Vst\Model\Calendar\Google;

/**
 * Static calendar factory
 */
class CalendarFactory
{
    public static function getProvider($provider, $credentials)
    {
        $className = __NAMESPACE__ . '\\' . ucfirst($provider);

        if (!class_exists($className)) {
            throw new \InvalidArgumentException('Specified provider ' . (!empty($provider) ? $provider : '<null>') . ' does not exist');
        }

        return new $className($credentials);
    }
}
