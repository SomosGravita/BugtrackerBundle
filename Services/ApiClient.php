<?php

namespace Elemento115\BugtrackerBundle\Services;

use Symfony\Bridge\Monolog\Logger;
use GuzzleHttp\Client;

/**
 * Description of ApiClient
 *
 * @author Elemento115
 */
class ApiClient
{
    /**
     * Constructor
     *
     * @param Guzzle $guzzle
     */
    public function __construct(Logger $logger, string $url)
    {
        $this->guzzle = $guzzle;
        $this->logger = $logger;
        $this->url = $url;
    }

    /**
     * @param array  $data
     *
     * @return array
     */
    public function post($data = array())
    {
        try {
            $response = $this->guzzle->post($this->url, ['json' => $data]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $ex) {
            $this->logger->error($ex->getMessage());

            return [
                'Result' => false,
                'error' => $ex->getMessage()
            ];
        }
    }

    public function get($param)
    {

    }
}
