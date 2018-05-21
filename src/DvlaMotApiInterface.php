<?php

namespace MikeKBurke\CapApi;

use Carbon\Carbon;
use MikeKBurke\DvlaMot\DvlaMot;
use MikeKBurke\DvlaMot\DvlaMotError;

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
    protected $dvla;
    protected $data;

    public function __construct($key, $vrm)
    {
        $this->dvla = new DvlaMot($key);
        try {
            $this->data = $this->dvla->lookup($vrm);
        } catch (\Exception $e) {
            $this->data = $e;
        }
    }

    /**
     * If there are MOT tests in the API response, find the last one with an
     * odometer reading, and return that reading along with the date it was taken
     *
     * @return null|object
     */
    public function getLastRecordedMileage()
    {
        if (
            $this->data instanceof DvlaMotError ||
            !isset($this->data->motTests) ||
            count($this->data->motTests) == 0
        ) {
            return null;
        }

        foreach ($this->data->motTests as $test) {
            if (
                isset($test->odometerValue) &&
                isset($test->completedDate) &&
                is_numeric($test->odometerValue) &&
                $test->odometerValue != 0
            ) {
                return (object) [
                    'date' => Carbon::createFromFormat('Y.m.d H:i:s', $test->completedDate),
                    'mileage' => $test->odometerValue
                ];
            }
        }

        return null;
    }
}
