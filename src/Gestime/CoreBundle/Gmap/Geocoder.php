<?php

namespace Gestime\CoreBundle\Gmap;

/**
 * Helper GMAP
 *
 */
class Geocoder
{

    function encodeBase64UrlSafe($value)
    {
      return str_replace(array('+', '/'), array('-', '_'),
        base64_encode($value));
    }

    function decodeBase64UrlSafe($value)
    {
      return base64_decode(str_replace(array('-', '_'), array('+', '/'),
        $value));
    }

    function signUrl($myUrlToSign, $privateKey)
    {
      $url = parse_url($myUrlToSign);
      $urlPartToSign = $url['path'] . "?" . $url['query'];

      // Decode the private key into its binary format
      $decodedKey = decodeBase64UrlSafe($privateKey);

      // Create a signature using the private key and the URL-encoded
      // string using HMAC SHA1. This signature will be binary.
      $signature = hash_hmac("sha1",$urlPartToSign, $decodedKey,  true);

      $encodedSignature = encodeBase64UrlSafe($signature);

      return $myUrlToSign."&signature=".$encodedSignature;
    }

    private static $url = 'https://maps.google.com/maps/api/geocode/json?sensor=false&key=AIzaSyDZl_p4GvElS5VstE8L3Z2Da3YntKFfYeg&address=';

    /**
     * [getLocation description]
     * @param string $address
     * @return boolean
     */
    public static function getLocation($address)
    {
        $url = self::$url.urlencode($address);

        $respJson = self::curl_file_get_contents($url);
        $resp = json_decode($respJson, true);

        if ($resp['status'] == 'OK') {
            return $resp['results'][0]['geometry']['location'];
        } else {

            return false;
        }
    }

    private static function curl_file_get_contents($URL)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        $contents = curl_exec($c);
        curl_close($c);

        if ($contents) {
            return $contents;
        } else {
            return false;
        }
    }

    private function distance($lat1, $lon1, $lat2, $lon2, $unit) {

      $theta = $lon1 - $lon2;
      $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
      $dist = acos($dist);
      $dist = rad2deg($dist);
      $miles = $dist * 60 * 1.1515;

      $unit = strtoupper($unit);

      if ($unit == "K") {
        return ($miles * 1.609344);
      } else if ($unit == "N") {
        return ($miles * 0.8684);
      } else {
        return $miles;

      }
      //echo distance(32.9697, -96.80322, 29.46786, -98.53506, "K") . " Kilometers<br>";
    }
}
