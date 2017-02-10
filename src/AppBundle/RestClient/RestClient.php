<?php

namespace AppBundle\RestClient;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;

class RestClient
{
    public function __construct($circleRestClient, Router $router)
    {
        $this->circleRestClient = $circleRestClient;
        $this->router = $router;
    }

    /**
     * Generate an array from an url
     *
     * @param string $method
     * @param string $url
     * @param array|null $data
     *
     * @return array
     */
    public function generate($method, $url, $data = [])
    {
        $restClient = $this->circleRestClient;

        switch(strtoupper($method)){
            case 'GET': $response = $restClient->get($url); break;
            case 'POST': $response = $restClient->post($url, $data); break;
            case 'PUT': $response = $restClient->put($url, $data); break;
            case 'PATCH': $response = $restClient->patch($url, $data); break;
            case 'DELETE': $response = $restClient->delete($url, $data); break;
        }

        $decoder = new JsonDecode;

        return $decoder->decode($response->getContent(), null);
    }

    /**
     * Generate an array from a route
     *
     * @param string $method
     * @param string $route
     * @param array|null $data
     *
     * @return array
     */
    public function generateFromRoute($method, $route, $data = [])
    {
        $url = $this->router->generate($route, $data, UrlGeneratorInterface::ABSOLUTE_URL);

        return $this->generate($method, $url, $data);
    }
}