<?php

namespace Elemento115\BugtrackerBundle\Services;

use GuzzleHttp;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Description of ApiClient
 *
 * @author Elemento115
 */
class ApiClient
{
    const TOKEN_NAME = 'jwt';

    /** @var Client */
    protected $client;
    /** @var  string */
    protected $user;
    /** @var  string */
    protected $password;
    /** @var  string */
    protected $registry;
    /** @var  string */
    protected $version;
    /** @var Session */
    protected $session = null;

    /**
     * Constructor
     *
     * @param array $arguments
     *
     * @throws \Exception if any of the required arguments is not present
     *
     */
    public function __construct(array $arguments)
    {
        if ( ! isset($arguments['client'])) {
            throw new \Exception(
                sprintf('An instance of class %s should be provided with key "client"', Client::class)
            );
        }
        if ( ! isset($arguments['user'])) {
            throw new \Exception('Missing key "user" on arguments');
        }
        if ( ! isset($arguments['password'])) {
            throw new \Exception('Missing key "password" on arguments');
        }
        if ( ! isset($arguments['registry'])) {
            throw new \Exception('Missing key "registry" on arguments');
        }
        if ( ! isset($arguments['api_version'])) {
            throw new \Exception('Missing key "api_version" on arguments');
        }

        $this->client   = $arguments['client'];
        $this->user     = $arguments['user'];
        $this->password = $arguments['password'];
        $this->registry = $arguments['registry'];
        $this->version  = $arguments['api_version'];

        if ($this->session === null) {
            $this->session = new Session();
        }
        if ( ! $this->session->has(self::TOKEN_NAME)) {
            $this->refreshToken();
        }

    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function post($data = [])
    {
        if ( ! isset($data['OK'])) {
            $this->session->set(self::TOKEN_NAME, 'asdlkas');
        }
        try {
            $response = $this->client->post(
                $this->version.'/applications/'.$this->registry.'/logs',
                [
                    GuzzleHttp\RequestOptions::JSON    => $data,
                    GuzzleHttp\RequestOptions::HEADERS => [
                        'Authorization' => "Bearer {$this->getToken()}",
                        'Content-Type'  => 'application/json',
                    ],
                ]
            );

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleHttp\Exception\ClientException $clientException) {
            $this->refreshToken();
            $data['OK'] = true;
            $this->post($data);
        } catch (\Exception $ex) {
            return [
                'Result' => false,
                'error'  => $ex->getMessage(),
            ];
        }
    }

    public function refreshToken()
    {
        $response = $this->client->post(
            'login_check',
            [
                'form_params' => [
                    '_username' => $this->user,
                    '_password' => $this->password,
                ],
            ]
        );
        $this->session->set(self::TOKEN_NAME, json_decode($response->getBody()->getContents())->token);
    }

    /**
     * Retrieve the JWT
     *
     * @return mixed
     */
    public function getToken()
    {
        return $this->session->get(self::TOKEN_NAME);
    }
}
