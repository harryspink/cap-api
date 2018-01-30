<?php

namespace MikeKBurkeTest\CapApi;

use GuzzleHttp\Client;

/**
 * Class CapApiInterface
 *
 * @category API
 *
 * @author   Mike Burke <mkburke@hotmail.co.uk>
 * @license  MIT https://opensource.org/licenses/MIT
 *
 * @link     https://github.com/mike-k-burke/cap-api
 */
class CapApiInterface
{
    /**
     * Base URL
     *
     * @var string
     */
    public static $base_url = 'https://soap.cap.co.uk';

    /**
     * Call the API
     *
     * @param string    $request_url Url to make request on
     * @param array     $credentials Authentication credentials
     * @param array     $call_data   Params
     * @return string|CapApiError|null
     */
    public static function callApi($request_url, array $credentials, array $call_data)
    {
        $client = new Client();

        $response = $client->get(
            self::$base_url . $request_url,
            [
                'query'  => array_merge(
                    $credentials,
                    $call_data
                ),
                'verify' => false
            ]
        );

        if ($response->getStatusCode() != 200) {
            return new CapApiError(isset($response->message) ? $response->message : 'Error', $response->getStatusCode());
        }

        $data = simplexml_load_string($response->getBody());

        return $data;
    }
}
