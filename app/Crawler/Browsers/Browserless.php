<?php

namespace App\Crawler\Browsers;

use DokLibs\Browserless\Client;
use DokLibs\Browserless\Options\CommonOptions;

class Browserless implements BrowserInterface
{

    protected Client $client;

    public function __construct(array $options = [])
    {
        $this->client = new Client(servers: $options['servers'], timeout: $options['timeout']);
    }

    public function getHtml($url, array $options = [])
    {
        $br_options = $options['browser_options'] ?? (new \DokLibs\Browserless\Options\CommonOptions());
        if(!empty($options['proxy'])) {
            $br_options->setProxy($options['proxy']);
        }

        if (!empty($options['browser_endpoint'])) {
            $response = $this->client->{$options['browser_endpoint']}($url, $br_options);
        } else {
            $response = $this->client->content($url, $br_options);
        }

        return $response->getBody()->getContents();

    }
}
