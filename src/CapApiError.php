<?

namespace MikeKBurke\CapApi;

/**
 * Class CapApiError.
 *
 * @category API
 *
 * @author   Mike Burke <mkburke@hotmail.co.uk>
 * @license  MIT https://opensource.org/licenses/MIT
 *
 * @link     https://github.com/mike-k-burke/cap-api
 */
class CapApiError extends \Exception implements \JsonSerializable
{
    const ERROR_AUTH_PARAMS = 'Required authentication parameter not found.';
    const ERROR_VRM = 'You must pass a valid VRM for this request';

    /**
     * CapApiError constructor.
     *
     * @param string $message Message
     * @param null   $code    Code
     */
    public function __construct($message, $code = null)
    {
        $this->message = $message;
        $this->code = $code;
    }

    /**
     * JSON array.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'error' => $this->getMessage(),
            'code'  => $this->getCode(),
        ];
    }
}
