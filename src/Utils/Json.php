<?php
namespace Jgauthi\Component\Utils;

use InvalidArgumentException;

class Json
{
    /**
     * Wrapper for JSON encoding that throws when an error occurs.
     *
     * @param mixed $value   The value being encoded
     * @param int    $options JSON encode option bitmask
     * @param int    $depth   Set the maximum depth. Must be greater than zero.
     *
     * @return string
     * @throws InvalidArgumentException if the JSON cannot be encoded.
     * @link http://www.php.net/manual/en/function.json-encode.php
     */
    static public function encode($value, $options = 0, $depth = 512)
    {
        $json = json_encode($value, $options, $depth);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException('[Json Encode] '. json_last_error_msg() );
        }

        return $json;
    }

    /**
     * Wrapper for json_decode that throws when an error occurs.
     *
     * @param string $jsonContent    JSON data to parse
     * @param bool $assoc     When true, returned objects will be converted
     *                        into associative arrays.
     * @param int    $depth   User specified recursion depth.
     * @param int    $options Bitmask of JSON decode options.
     *
     * @return mixed
     * @throws InvalidArgumentException if the JSON cannot be decoded.
     * @link http://www.php.net/manual/en/function.json-decode.php
     */
    static public function decode($jsonContent, $assoc = false, $depth = 512, $options = 0)
    {
        $jsonContent = json_decode($jsonContent, $assoc, $depth, $options);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException('[Json Decode] '. json_last_error_msg() );
        }

        return $jsonContent;
    }

    /**
     * Send HTTP JSON Response with status
     * Usage for Legacy Code, use instead if possible: Symfony\Component\HttpFoundation\JsonResponse
     * @param array $data
     * @param int $httpStatus
     * @return bool
     */
    static public function response(array $data, $httpStatus = 200)
    {
        if (headers_sent()) {
            return false;
        }

        header('content-type: application/json');
        http_response_code(intval($httpStatus));

        echo self::encode($data, JSON_UNESCAPED_UNICODE);
        return true;
    }

    /**
     * Returns a localized json error message in French
     * For english version, you can use the PHP function json_last_error_msg()
     * @param int $json_last_error
     * @return string|null
     */
    static public function error_msg($json_last_error)
    {
        switch ($json_last_error) {
            case JSON_ERROR_NONE:
                return null; // OK

            case JSON_ERROR_DEPTH:
                return 'Erreur JSON: Profondeur maximale atteinte';

            case JSON_ERROR_STATE_MISMATCH:
                return 'Erreur JSON: Inadéquation des modes ou underflow';

            case JSON_ERROR_CTRL_CHAR:
                return 'Erreur JSON: Erreur lors du contrôle des caractères';

            case JSON_ERROR_SYNTAX:
                return 'Erreur JSON: Erreur de syntaxe ; JSON malformé';

            case JSON_ERROR_UTF8:
                return 'Erreur JSON: Erreur de syntaxe ; Caractères UTF-8 malformés, probablement une erreur d\'encodage';
        }

        return 'Erreur JSON: Erreur inconnue';
    }
}