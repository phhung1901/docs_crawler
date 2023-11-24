<?php

namespace App\Crawler\Browsers;

use InvalidArgumentException;

class BrowserManager
{

    protected static $drivers = [];

    /**
     * @param $driver
     * @param array $option
     * @return BrowserInterface
     */
    public static function get($driver, array $option = [])
    {
        if (!isset(self::$drivers[$driver])) {
            self::$drivers[$driver] = self::makeBrowser($driver, $option);
        }
        return self::$drivers[$driver];
    }


    protected static function makeBrowser($driver = null, $options = []): BrowserInterface
    {
        $driver = $driver ?? config('crawler.default');
        $options = array_merge(config('crawler.browsers.' . $driver, []), $options);

        return match ($driver) {
            'guzzle' => new Guzzle($options),
            'browserless' => new Browserless($options),
            default => throw new InvalidArgumentException("No browser match with driver " . $driver),
        };
    }

}
