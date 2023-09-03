<?php

namespace app\core\traits;

trait ExceptionTrait
{
    /**
     * @param string|int $code
     * @return int
     */
    public static function getHttpCode(string|int $code)
    {
        // Make sure the input is numeric
        if (!is_numeric($code)) {
            return 400;
        }

        // List of valid HTTP status codes
        $validCodes = array(
            100, 101, 102, 103,
            200, 201, 202, 203, 204, 205, 206, 207, 208, 226,
            300, 301, 302, 303, 304, 305, 306, 307, 308,
            400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410,
            411, 412, 413, 414, 415, 416, 417, 418, 421, 422, 423, 424, 425,
            426, 428, 429, 431, 451,
            500, 501, 502, 503, 504, 505, 506, 507, 508, 510, 511
        );

        // Check if the input code is in the list of valid codes
        return in_array($code, $validCodes) ? $code : 400;
    }
}
