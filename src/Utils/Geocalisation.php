<?php
namespace Jgauthi\Component\Utils;

use InvalidArgumentException;

class Geocalisation
{
    /**
     * selon un nombre de latitude longitude retourne la taille du zomm et la latitude longitude centrale.
     * @param iterable $liste_points
     * @return array
     */
    static public function enableAutoZoom($liste_points = [])
    {
        $latitude_array = $longitude_array = [];
        foreach ($liste_points as $key => $value) {
            $latitude_array[] = $value['lat'];
            $longitude_array[] = $value['lng'];
        }
        $minimal_latitude = min($latitude_array);
        $maximal_latitude = max($latitude_array);
        $minimal_longitude = min($longitude_array);
        $maximal_longitude = max($longitude_array);

        $central_latitude = $minimal_latitude + ($maximal_latitude - $minimal_latitude) / 2;
        $central_longitude = $minimal_longitude + ($maximal_longitude - $minimal_longitude) / 2;

        $miles = (3958.75 * acos(sin($minimal_latitude / 57.2958) * sin($maximal_latitude / 57.2958) + cos($minimal_latitude / 57.2958) * cos($maximal_latitude / 57.2958) * cos($maximal_longitude / 57.2958 - $minimal_longitude / 57.2958)));

        switch ($miles) {
            case $miles < 0.2: $zoom = 18; break;
            case $miles < 0.5: $zoom = 17; break;
            case $miles < 1:	 $zoom = 16; break;
            case $miles < 2: 	 $zoom = 15; break;
            case $miles < 3:   $zoom = 14; break;
            case $miles < 7:   $zoom = 13; break;
            case $miles < 15:  $zoom = 12; break;
            case $miles < 50:  $zoom = 9; break;
            case $miles < 300: $zoom = 8; break;
            case $miles < 600: $zoom = 6; break;
            default:             $zoom = 2; break;
        }

        return [
            'zoom' => $zoom,
            'lat' => $central_latitude,
            'lgt' => $central_longitude,
        ];
    }

    /**
     * Récupérer la véritable adresse IP d'un visiteur.
     * @todo Get ip from terminal mode
     * @return string|null
     */
    static public function get_ip()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) { // IP si internet partagé
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) { // IP derrière un proxy
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        } else {
            return null;
        }
    }

    /**
     * https://stackoverflow.com/a/10054282
     * @param string $lat1
     * @param string $lng1
     * @param string $lat2
     * @param string $lng2
     * @param int $earthRadius
     * @return float
     */
    static public function geoDistance($lat1, $lng1, $lat2, $lng2, $earthRadius = 6371000)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lng1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lng2);

        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
            pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);

        return $angle * $earthRadius;
    }

    /**
     * transport: driving, bicycling, transit (transport en commun), walking
     * @param string $lat1
     * @param string $lng1
     * @param string $lat2
     * @param string $lng2
     * @param string|null $apikey
     * @param string $transport
     * @param string $lang
     * @return array|null
     */
    static public function GetDrivingDistance($lat1, $lng1, $lat2, $lng2, $apikey = null, $transport = 'driving', $lang = 'fr-FR')
    {
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?key={$apikey}&origins={$lat1},{$lng1}&destinations={$lat2},{$lng2}&mode={$transport}&language={$lang}";
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL	=> $url,
            CURLOPT_RETURNTRANSFER	=> true,
            CURLOPT_PROXYPORT		=> 3128,
            CURLOPT_SSL_VERIFYHOST	=> false,
            CURLOPT_SSL_VERIFYPEER	=> false,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response, true);

        $compare = $response_a['rows'][0]['elements'][0];
        if (in_array($compare['status'], ['ZERO_RESULTS', 'NOT_FOUND'], true)) {
            return null;
        }

        return [
            'distance' => $compare['distance']['value'],
            'distance_formated' => $compare['distance']['text'],
            'time' => $compare['duration']['value'],
            'time_formated' => $compare['duration']['text'],
        ];
    }

    /**
     * @param string $lat1
     * @param string $lng1
     * @param array $destinataires
     * @param string|null $apikey
     * @param string $lang
     * @return string|null
     */
    static public function GetDrivingDistances($lat1, $lng1, $destinataires, $apikey = null, $lang = 'fr-FR')
    {
        // $destinataires = array('lat1,lng1', 'lat2,lng2', 'lat3,lng3', 'lat4,lng4');
        $target = implode('|', $destinataires);

        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?key={$apikey}&origins={$lat1},{$lng1}&destinations={$target}&mode=driving&language={$lang}";
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL	=> $url,
            CURLOPT_RETURNTRANSFER	=> true,
            CURLOPT_PROXYPORT		=> 3128,
            CURLOPT_SSL_VERIFYHOST	=> false,
            CURLOPT_SSL_VERIFYPEER	=> false,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response, true);

        if ('OK' !== $response_a['status'] || empty($response_a['rows'][0]['elements'])) {
            return null;
        }

        return $response_a['rows'][0]['elements'];
    }

    /**
     * Permet d'avoir la géolocalisation selon une adresse.
     * @param string $address
     * @param string|null $google_apikey_public
     * @return string|null
     */
    static public function getAddress($address, $google_apikey_public = null)
    {
        if (empty($address)) {
            throw new InvalidArgumentException('Address require.');
        }

        $args = ['ssl' => ['verify_peer' => false]];

        // Proxy support
        if (defined('PROXY_HOST') && PROXY_HOST !== '') {
            $args['http'] = ['proxy' => 'tcp://'.PROXY_HOST, 'request_fulluri' => true];
            if (defined('PROXY_PORT') && PROXY_PORT !== '') {
                $args['http']['proxy'] .= ':'.PROXY_PORT;
            }

            // Auth suport
            if (defined('PROXY_USERNAME') && PROXY_USERNAME !== '') {
                $auth = PROXY_USERNAME;
                if (defined('PROXY_PASSWORD') && PROXY_PASSWORD !== '') {
                    $auth .= ':'.PROXY_PASSWORD;
                }

                $args['http']['header'] = 'Proxy-Authorization: Basic '.base64_encode($auth);
            }
        }
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?key='.$google_apikey_public.'&limit=1&address='.urlencode($address);
        $json = file_get_contents($url, false, stream_context_create(['ssl' => ['verify_peer' => false]]));

        $json = json_decode($json, true);
        if (empty($json['status']) || 'OK' !== $json['status'] || empty($json['results'][0]['geometry']['location'])) {
            return null;
        }

        return $json['results'][0]['geometry']['location'];
    }
}