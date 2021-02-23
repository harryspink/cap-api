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
     * @param string $dvla_key  DVLA MOT API authentication key
     * @throws \InvalidArgumentException|\ErrorException
     */
    public function __construct($subscriber_id, $password, $dvla_key)
    {
        if (empty($subscriber_id) || empty($password)) {
            throw new \InvalidArgumentException(CapApiError::ERROR_AUTH_PARAMS);
        }

        $this->credentials['SubscriberID'] = $subscriber_id;
        $this->credentials['Password'] = $password;
        $this->dvla_key = $dvla_key;
    }

    /**
     * Valuation
     *
     * @param string  $vrm      VRM to lookup
     * @param string  $reg_date Optional date the vehicle was first registered
     * @return array|CapApiError|null
     */
    public function valuation($vrm, $mileage = 15000)
    {
        $vrm = preg_replace('/[^A-Z0-9]/', '', strtoupper($vrm));

        if (!strlen($vrm) || !self::isVRM($vrm)) {
            throw new \InvalidArgumentException(CapApiError::ERROR_VRM);
        }

        $data = CapApiInterface::callApi(
            '/Vrm/CapVrm.asmx/VRMValuation',
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
