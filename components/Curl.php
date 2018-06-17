<?php

namespace app\components;

class Curl {

    private static $curl;
    private static $codes = ['200', '301', '302', '304'];

    private static function curlInit(){
        if (gettype(self::$curl) != 'resource') {
            self::$curl = curl_init();
            curl_setopt(self::$curl, CURLOPT_RETURNTRANSFER, true);
        }
    }

    /**
     * @param string $url
     * @param array $data ($key => $value pairs)
     * @param array $headers
     * @return mixed
     */
    public static function GETRequest($url, $data = [], $headers = []){
        self::curlInit();

        if (!empty($data))
            $url = self::buildQuery($url, $data);
        if (!empty($headers))
            curl_setopt(self::$curl, CURLOPT_HTTPHEADER, $headers);

        curl_setopt(self::$curl, CURLOPT_URL, $url);
        curl_setopt(self::$curl, CURLOPT_FRESH_CONNECT, true);

        return self::execute();
    }

    /**
     * @param string $url
     * @param array $data ($key => $value pairs)
     * @param array $headers
     * @return mixed
     */
    public static function POSTRequest($url, $data = [], $headers = []){
        self::curlInit();

        if (!empty($headers))
            curl_setopt(self::$curl, CURLOPT_HTTPHEADER, $headers);
        if (!empty($data))
            curl_setopt(self::$curl, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt(self::$curl, CURLOPT_URL, $url);
        curl_setopt(self::$curl, CURLOPT_POST, true);
        curl_setopt(self::$curl, CURLOPT_FRESH_CONNECT, true);

        return self::execute();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private static function execute() {
        if ($answer = curl_exec(self::$curl)) {
            $status = curl_getinfo(self::$curl);
            if (!in_array($status['http_code'], self::$codes))
                throw new \Exception('Status code error - ' . $status['http_code']);
        } else {
            throw new \Exception('Curl error: ' . curl_error(self::$curl));
        }
        curl_close(self::$curl);
        return $answer;
    }

    /**
     * @param array $dataArray ($key => $value pairs)
     * @param string $url
     * @return string
     */
    public static function buildQuery($url, $dataArray){
        return $url . '?' . urldecode(http_build_query($dataArray));
    }
}