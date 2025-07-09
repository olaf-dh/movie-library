<?php
declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExternalApiHelper
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param $request
     * @param $host
     * @param $key
     * @return mixed
     */
    public function getAdditionalMovieInfo($request, $host, $key): mixed
    {
        $query = [
            'apikey' => $key,
            't' => $request['movieTitle'],
            'y' => $request['movieYear'],
            'r' => 'json',
        ];
        $suffix = http_build_query($query);

        try {
            $url = $host . '?' . $suffix;

            $response = $this->httpClient->request(
                'GET',
                $url
            );

            return $response->toArray();
        } catch (TransportExceptionInterface | ClientExceptionInterface | DecodingExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface $e) {
            return ['error' => 'da ist was schief gelaufen', 'message' => $e->getMessage()];
        }
    }
}
