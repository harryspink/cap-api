<?php

namespace MikeKBurkeTest\CapApi;

use GuzzleHttp\Client;

/**
 * Class CapApiApi
 *
 * @category API
 *
 * @author   Mike Burke <mkburke@hotmail.co.uk>
 * @license  MIT https://opensource.org/licenses/MIT
 *
 * @link     https://github.com/mike-k-burke/cap-api
 */
class DvlaMotApiInterface
{
    /**
     * URL
     *
     * @var string
     */
    public static $url = 'https://beta.check-mot.service.gov.uk/trade/vehicles/mot-tests';

    /**
     * Call the API
     *
     * @param string    $key        Authentication key
     * @param array     $call_data   Params
     * @return boolean|CapApiError|null
     */
    public static function callApi($key, array $call_data)
    {
        $client = new Client(['headers' => [
            'Accept'    => 'application/json+v3',
            'x-api-key' => $key
        ]]);

        $response = $client->get(
            self::$url,
            [
                'query' => $call_data,
                'verify' => false
            ]
        );

        if ($response->getStatusCode() != 200) {
            return new CapApiError(isset($response->message) ? $response->message : 'Error', $response->getStatusCode());
        }

        $data = json_decode($response->getBody()->getContents());
        return $data;
    }
}
