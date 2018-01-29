<?php

namespace MikeKBurke\CapApi;

use BespokeSupport\Reg\Reg;
use Carbon\Carbon;

/**
 * Class CapApi
 *
 * @category API
 *
 * @author   Mike Burke <mkburke@hotmail.co.uk>
 * @license  MIT https://opensource.org/licenses/MIT
 *
 * @link     https://github.com/mike-k-burke/cap-api
 */
class CapApi
{
    /**
     * Required CAP credentials
     *
     * @var array
     */
    protected $credentials = [
        'SubscriberID' => null,
        'Password' => null
    ];

    /**
     * DVLA MOT API authentication key
     *
     * @var string
     */
    protected $dvla_key = null;

    /**
     * Constructor
     *
     * @param string $subscriber_id  CAP API subscriber ID
     * @param string $password  CAP API password
     * @param string $dvla_key  Optional DVLA MOT API authentication key
     * @throws \InvalidArgumentException|\ErrorException
     */
    public function __construct($subscriber_id, $password, $dvla_key = null)
    {
        if (empty($subscriber_id) || empty($password)) {
            throw new \InvalidArgumentException(CapApiError::ERROR_AUTH_PARAMS);
        }

        $this->credentials['SubscriberID'] = $subscriber_id;
        $this->credentials['Password'] = $password;
    }

    /**
     * Valuation
     *
     * @param string  $vrm      VRM to lookup
     * @param string  $reg_date Date the vehicle was first registered
     * @return string|CapApiError|null
     */
    public function valuation($vrm, $reg_date)
    {
        $vrm = preg_replace('/[^A-Z0-9]/', '', strtoupper($vrm));

        if (!strlen($vrm) || !self::isVRM($vrm)) {
            throw new \InvalidArgumentException(CapApiError::ERROR_VRM);
        }

        // get the mileage for the vehicle, if available, from the DVLA
        $mileage = null;
        if (isset($dvla_key)) {
            $dvla_data = DvlaMotApiInterface::callApi(
                $this->key,
                ['registration' => $vrm]
            );
            if (!$dvla_data instanceof CapApiError &&
                isset($dvla_data[0]) &&
                isset($dvla_data[0]['motTests']) &&
                isset($dvla_data[0]['motTests'][0]) &&
                isset($dvla_data[0]['motTests']['odometerValue']) &&
                is_numeric($dvla_data[0]['motTests']['odometerValue'])
            ) {
                $mileage = $dvla_data[0]['motTests'][0]['odometerValue'];
            }
        }

        // if unable to get the mileage, calculate a rough value
        if (!isset($mileage)) {
            $reg_date = Carbon::parse($reg_date);
            $years = $reg_date->diffinYears(Carbon::today());
            $mileage = 15000 * $years;
        }

        $data = CapApiInterface::callApi(
            '/Vrm/CapVrm.asmx/VRMInternetPricesValuation',
            $this->credentials,
            [
                'VRM' => $vrm,
                'Mileage' => $mileage,
                'StandardEquipmentRequired' => 'false'
            ]
        );

        return $data;
    }

    /**
     * Check if the search term is a VRM
     *
     * @param string $search Search term
     * @return boolean
     */
    public static function isVRM($search)
    {
        if (strlen($search) != 17) {
            $reg = Reg::create($search);
            if ($reg) {
                return true;
            }
        }

        return false;
    }
}