<?php

namespace Vst\Controller;

//use Vst\Controller\Calendar\Google;

/**
 * Static calendar factory
 */
class CalendarFactory {
    public static function getProvider($provider, $credentials)
    {
        $className = __NAMESPACE__.'\Calendar\\'.ucfirst($provider);

        if (!class_exists($className)) {
            throw new \InvalidArgumentException('Specified provider ' . $provider . ' does not exist');
        }

        return new $className($credentials);
    }
}