<?php

namespace RadishesFlight\Pay\FuYou;

use GuzzleHttp\Client;

class FuYouPay
{
    public $config;
    public $data;


    public function __construct($payConfig)
    {
        $this->config = $payConfig;
    }

    public function setDataAll($data)
    {
        $this->data = $data;
        return $this;
    }

    public function setData($field, $value)
    {
        $this->data[$field] = $value;
        return $this;
    }

    public function execute($url, $data = [])
    {
        if (!empty($data)) {
            $this->data = $data;
        }
        if (!empty($this->data)) {
            $this->data['mchnt_cd'] = $this->config['mchnt_cd'];
            $message = $this->publicEncryptRsa(json_encode($this->data));
            return $this->httpQuery($url, $message);
        }
    }

    public function httpQuery($url, $message)
    {
        $client = new Client(['verify' => false]);
        $res = $client->request('POST', $url, [
            'json' => [
                'mchnt_cd' => $this->config['mchnt_cd'],
                'message' => $message
            ]
        ]);
        $result = $res->getBody()->getContents();
        if ($result) {
            $result = json_decode($result, true);
            if ($result['resp_code'] == '0000') {
                //鍙湁resp_code涓�0000鐨勬椂鍊欙紝鎵嶆湁message銆�
                $decrypted = $this->privateDecryptRsa($result['message']);
                if ($decrypted) {
                    return json_decode($decrypted, true);
                }
            }
        }
        return $result;
    }

    public function publicEncryptRsa($plainData = '')
    {
        if (!is_string($plainData)) {
            return null;
        }
        $encrypted = '';
        $partLen = $this->getPublicKenLen() / 8 - 11;
        $plainData = str_split($plainData, $partLen);
        $publicPEMKey = $this->getPublicKey();
        foreach ($plainData as $chunk) {
            $partialEncrypted = '';
            $encryptionOk = openssl_public_encrypt($chunk, $partialEncrypted, $publicPEMKey, OPENSSL_PKCS1_PADDING);
            if ($encryptionOk === false) {
                return false;
            }
            $encrypted .= $partialEncrypted;
        }
        return base64_encode($encrypted);
    }

    private function getPublicKenLen()
    {
        $pub_id = openssl_get_publickey($this->getPublicKey());
        return openssl_pkey_get_details($pub_id)['bits'];
    }

    private function getPublicKey()
    {
        $public_key = $this->config['public_key'];
        $pubic_pem = chunk_split($public_key, 64, "\n");
        $pubic_pem = "-----BEGIN PUBLIC KEY-----\n" . $pubic_pem . "-----END PUBLIC KEY-----\n";
        return $pubic_pem;
    }

    public function privateDecryptRsa($data = '')
    {
        if (!is_string($data)) {
            return null;
        }
        $decrypted = '';

        $partLen = $this->getPrivateKenLen() / 8;
        $data = str_split(base64_decode($data), $partLen);

        $privatePEMKey = $this->getPrivateKey();

        foreach ($data as $chunk) {
            $partial = '';
            $decryptionOK = openssl_private_decrypt($chunk, $partial, $privatePEMKey, OPENSSL_PKCS1_PADDING);
            if ($decryptionOK === false) {
                return false;
            }
            $decrypted .= $partial;
        }
        return $decrypted;
    }

    private function getPrivateKenLen()
    {
        $pub_id = openssl_get_privatekey($this->getPrivateKey());
        return openssl_pkey_get_details($pub_id)['bits'];
    }

    private function getPrivateKey()
    {
        $private_key = $this->config['private_key'];
        $private_pem = chunk_split($private_key, 64, "\n");
        $private_pem = "-----BEGIN PRIVATE KEY-----\n" . $private_pem . "-----END PRIVATE KEY-----\n";
        return $private_pem;
    }
}