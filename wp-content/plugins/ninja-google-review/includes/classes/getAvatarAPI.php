<?php

if (!class_exists('Njt_Avatar_Google_API')) {
    define('NJT_USER_AGENT', 'NJT-GGRVPlugin/1.0');
    define('NJT_SOCKET_TIMEOUT', 10);
    class Njt_Avatar_Google_API
    {

        public static function njt_getAvatarUrl($url)
        {
            $response = array(
                'data' => '',
                'code' => 0,
            );

            $url = preg_replace('/\s+/', '+', $url);

            if (function_exists('curl_init')) {
                if (!function_exists('curl_setopt_array')) {
                    function curl_setopt_array(&$ch, $curl_options)
                    {
                        foreach ($curl_options as $option => $value) {
                            if (!curl_setopt($ch, $option, $value)) {
                                return false;
                            }
                        }
                        return true;
                    }
                }
                return self::njt__curl_urlopen($url, $response);
            }
            return null;
        }
        public static function njt__curl_urlopen($url, &$response)
        {
            $c = curl_init($url);
            $postdata_str = '';

            $c_options = array(
                CURLOPT_USERAGENT => NJT_USER_AGENT,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => ($postdata_str ? 1 : 0),
                CURLOPT_HEADER => true,
                CURLOPT_HTTPHEADER => array_merge(array('Expect:'), array()),
                CURLOPT_TIMEOUT => NJT_SOCKET_TIMEOUT,
            );
            // if ($postdata) {
            //     $c_options[CURLOPT_POSTFIELDS] = $postdata_str;
            // }
            curl_setopt_array($c, $c_options);

            $open_basedir = ini_get('open_basedir');
            if (empty($open_basedir) && filter_var(ini_get('safe_mode'), FILTER_VALIDATE_BOOLEAN) === false) {
                curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
            }
            curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
            $data = curl_exec($c);
            curl_close($c);

            if (stripos($data, "HTTP/1.0 200 Connect Failed\r\n\r\n") !== false) {
                $data = str_ireplace("HTTP/1.0 200 Connection Failed\r\n\r\n", '', $data);
            }

            list($resp_headers, $response['data']) = explode("\r\n\r\n", $data, 2);
            $response['headers'] = self::njt__get_response_headers($resp_headers, $response);
            foreach ($response['headers'] as $header) {
                if (strpos($header, 'Location: ') !== false) {
                    $avatar_url = str_replace('Location: ', '', $header);
                    break;
                }

                if (strpos($header, 'location: ') !== false) {
                    $avatar_url = str_replace('location: ', '', $header);
                    break;
                }
            }

            return $avatar_url ? $avatar_url : NJT_PLUGIN_URL . '/assets/frontend/img/google_place.svg';
        }
        public static function njt__get_response_headers($headers, &$response)
        {
            $headers = explode("\r\n", $headers);
            list($unused, $response['code'], $unused) = explode(' ', $headers[0], 3);
            $headers = array_slice($headers, 1);
            foreach ($headers as $unused => $header) {
                $header = explode(':', $header);
                $header[0] = trim($header[0]);
                $header[1] = trim($header[1]);
                $headers[strtolower($header[0])] = $header[1];
            }
            return $headers;
        }

    }
}
