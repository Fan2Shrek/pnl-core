<?php

namespace Pnl\Client;

abstract class AbstractClient implements ClientInterface
{
    public function request(string $path, array $body = [], array $header = []): mixed
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $path,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_USERAGENT => "pnl",
            CURLOPT_RETURNTRANSFER => 1
        ]);

        $response = curl_exec($curl);

        $info = curl_getinfo($curl);

        if ($info['http_code'] !== 200) {
            throw new \Exception(sprintf('The request failed with code %s', $info['http_code']));
        }

        curl_close($curl);

        return json_decode($response, true);
    }
}
