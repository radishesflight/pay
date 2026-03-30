<?php


namespace RadishesFlight\Pay\ZhDg;

use Rtgm\sm\RtSm2;
use Rtgm\sm\RtSm3;

class Cmb
{
    public $payConfig;

    public function __construct($payConfig)
    {
        $this->payConfig = $payConfig;

    }

    public function SM2_Asn1ToRaw($apisign)
    {
        $apisign = hex2bin($apisign);
        $bytes = array_values(unpack('C*', $apisign));
        $pos = 3;
        if ($bytes[$pos] == 32) {
            $pos += 1;
        } elseif ($bytes[$pos] == 33) {
            $pos += 2;
        }

        $data = array_slice($bytes, $pos, 32);
        $pos += 32;
        $pos += 1;
        if ($bytes[$pos] == 32) {
            $pos += 1;
        } elseif ($bytes[$pos] == 33) {
            $pos += 2;
        }

        $data = array_merge($data, array_slice($bytes, $pos, 32));

        $str = pack('C*', ...$data);

        return bin2hex($str);
    }

    /**
     * sm2  header
     * @param $sign
     * @return string[]
     */
    public function sm2($sign)
    {
        $sm2 = new RtSm2();
        $time = time();
        $postDataApiSign = [
            "appid" => $this->payConfig["appid"],
            "secret" => $this->payConfig["secret"],
            "timestamp" => $time,
            "sign" => $sign,
        ];
        ksort($postDataApiSign);
        reset($postDataApiSign);
        $strHeader = http_build_query($postDataApiSign);
        $strHeader = mb_convert_encoding($strHeader, "UTF-8");
        $apiSign = $sm2->doSign($strHeader, $this->payConfig["private_key"], $this->payConfig["user_id"]);
        $apiSign = $this->SM2_Asn1ToRaw($apiSign);
        return [
            "Content-Type:application/json;charset=UTF-8",
            "appid:" . $this->payConfig["appid"],
            "timestamp:" . $time,
            "apisign:" . $apiSign,
            "sign:" . $sign,
            "verify:SM3withSM2",
        ];
    }

    /**
     * sm3摘要计算
     * @param $jsonString
     * @return array|string
     */
    public function sm3($jsonString)
    {
        $sm3 = new RtSm3();
        return $sm3->digest($jsonString, 1);
    }

    public function get()
    {
    }

    /**
     * 发送post请求
     * @param $data
     * @param $url
     * @return mixed
     */
    public function post($data, $url)
    {
        $jsonString = json_encode($data, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES);
        $header = $this->sm2($this->sm3($jsonString));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonString);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }

    public static function verifySign(string $responseBody = '', string $cmbBodySign = '', string $cmbPublicKey = '')
    {

        $sm2 = new RtSm2('base64');
        $sm2->verifySign($responseBody, $cmbBodySign, $cmbPublicKey);

        return true;
    }
}