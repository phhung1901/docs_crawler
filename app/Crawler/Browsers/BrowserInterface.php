<?php

namespace App\Crawler\Browsers;

interface BrowserInterface {

    public function __construct(array $options = []);

    public function getHtml($url, array $options = []);
}
