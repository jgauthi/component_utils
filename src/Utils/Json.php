<?php
namespace Jgauthi\Component\Utils;

use Nette\InvalidArgumentException;
use Nette\Utils\{Json as NetteJson, JsonException};

// Use NetteJson for encode/deco: https://doc.nette.org/en/utils/json
class Json
{
    public const OPTION_ESCAPE_UNICODE = NetteJson::ESCAPE_UNICODE;
    public const OPTION_FORCE_ARRAY = NetteJson::JSON_OBJECT_AS_ARRAY;
    public const OPTION_PRETTY = NetteJson::PRETTY;

    /**
     * Converts value to JSON format. The flag can be Json::PRETTY, which formats JSON for easier reading and clarity,
     * and Json::ESCAPE_UNICODE for ASCII output.
     *
     * @param mixed $value   The value being encoded
     * @param int   $options The flag can be Json::PRETTY, which formats JSON for easier reading and clarity,
     * and Json::ESCAPE_UNICODE for ASCII output
     *
     * @return string
     * @throws JsonException if the JSON cannot be encoded.
     * @link https://doc.nette.org/en/utils/json#toc-encode
     */
    static public function encode($value, int $options = 0): string
    {
        return NetteJson::encode($value, $options);
    }

    /**
     * Parses JSON to PHP value.
     *
     * @param string $jsonContent  JSON data to parse
     * @param int    $options      The flag can be Json::FORCE_ARRAY, which forces an array instead of an object as the return value.
     * @return mixed
     * @throws JsonException if the JSON cannot be decoded.
     * @link https://doc.nette.org/en/utils/json#toc-decode
     */
    static public function decode(string $jsonContent, int $options = 0)
    {
        return NetteJson::decode($jsonContent, $options);
    }

    /**
     * Send HTTP JSON Response with status
     * Usage for Legacy Code, use instead if possible: Symfony\Component\HttpFoundation\JsonResponse
     * @param array $data
     * @param int $httpStatus
     * @param int $jsonOptions
     * @return bool
     */
    static public function response(array $data, int $httpStatus = 200, int $jsonOptions = 0): bool
    {
        if (headers_sent()) {
            return false;
        }

        header('content-type: application/json');
        http_response_code(intval($httpStatus));

        echo self::encode($data, $jsonOptions);
        return true;
    }

    /**
     * Returns a localized json error message in French
     * For english version, you can use the PHP function json_last_error_msg()
     */
    static public function error_msg(int $json_last_error): ?string
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