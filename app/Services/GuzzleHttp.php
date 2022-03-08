<?php


namespace App\Services;
use GuzzleHttp\Client;

class GuzzleHttp
{

    public $address;
    public $currence_first;
    public $currence_last;

    public function getHttp()
    {
        $guzzle = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ]);
        try {
            $response = $guzzle->get($this->url.$this->currence_first);

        } catch (\Exception $e) {
            return (object) array(
                "success" => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            );
        }
        return (object) array(
            "success" => true,
            "data" => json_decode($response->getBody()),
            "code" => $response->getStatusCode()
        );
    }


}
