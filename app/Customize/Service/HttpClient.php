<?php
namespace Customize\Service;

class HttpClient
{
    /**
     * Request to url return content of request
     *
     * @param $url
     * @return bool|string
     */
    public function request($url)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_URL, $url);
        $result = curl_exec($c);
        curl_close($c);

        return $result;
    }
}
