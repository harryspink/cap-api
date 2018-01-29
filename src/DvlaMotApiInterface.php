<?php

namespace MikeKBurke\CapApi;

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
            'Accept'    => 'application/json',
            'x-api-key' => $key
        ]]);

        $response = $client->post(
            self::$url,
            [
                'query' => $call_data,
                'verify' => false
            ]
        );

        if ($response->status_code != 200) {
            return new CapApiError($response->message, $response->status_code);
        }

        $data = json_decode($response->getBody()->getContents());
        return $data;
    }
}
