<?php

namespace App\Crawler\Browsers;

use GuzzleHttp\Client;

class Guzzle implements BrowserInterface {

    protected Client $client;

    public function __construct(array $options = [])
    {
        $this->client = new Client($options);
    }


    public function getHtml($url, array $options = [])
    {
        $response = $this->client->get($url, $options);
        return $response->getBody()->getContents();
    }

}
