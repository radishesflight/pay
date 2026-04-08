<?php

namespace RadishesFlight\Pay\Hwc;

class RequestHandler
{
    /** 网关url地址 */
    public $gateUrl;

    /** 密钥 */
    public $key;

    /* RSA私钥*/
    public $private_rsa_key;

    public $signtype;

    /** 请求的参数 */
    public $parameters;

    /** debug信息 */
    public $debugInfo;

    public function __construct()
    {
        $this->RequestHandler();
    }

    public function RequestHandler()
    {
        $this->gateUrl = "";
        $this->key = "";
        $this->private_rsa_key = "";
        $this->signtype = "";
        $this->parameters = array();
        $this->debugInfo = "";
    }

    /**
     *初始化函数。
     */
    public function init()
    {
        //nothing to do
    }

    /**
     *获取入口地址,不包含参数值
     */
    public function getGateURL()
    {
        return $this->gateUrl;
    }

    /**
     *设置入口地址,不包含参数值
     */
    public function setGateURL($gateUrl)
    {
        $this->gateUrl = $gateUrl;
    }

    public function setSignType($type)
    {
        $this->signtype = $type;
    }

    /**
     *获取MD5密钥
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     *设置MD5密钥
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /*设置RSA私钥*/
    public function setRSAKey($key)
    {
        $this->private_rsa_key = $key;
    }

    /**
     *获取参数值
     */
    public function getParameter($parameter)
    {
        return isset($this->parameters[$parameter]) ? $this->parameters[$parameter] : '';
    }

    /**
     *设置参数值
     */
    public function setParameter($parameter, $parameterValue)
    {
        $this->parameters[$parameter] = $parameterValue;
    }

    /**
     * 一次性设置参数
     */
    public function setReqParams($post, $filterField = null)
    {
        if ($filterField !== null) {
            foreach ($filterField as $k => $v) {
                unset($post[$v]);
            }
        }

        //判断是否存在空值，空值不提交
        foreach ($post as $k => $v) {
            if (empty($v)) {
                unset($post[$k]);
            }
        }

        $this->parameters = $post;
    }

    /**
     *获取所有请求的参数
     * @return array
     */
    public function getAllParameters()
    {
        return $this->parameters;
    }

    /**
     *获取带参数的请求URL
     */
    public function getRequestURL()
    {

        $this->createSign();

        $reqPar = "";
        ksort($this->parameters);
        foreach ($this->parameters as $k => $v) {
            $reqPar .= $k . "=" . urlencode($v) . "&";
        }

        //去掉最后一个&
        $reqPar = substr($reqPar, 0, strlen($reqPar) - 1);

        $requestURL = $this->getGateURL() . "?" . $reqPar;

        return $requestURL;

    }

    /**
     *获取debug信息
     */
    public function getDebugInfo()
    {
        return $this->debugInfo;
    }

    /**
     *创建md5摘要,规则是:按参数名称a-z排序,遇到空值的参数不参加签名。
     */
    public function createSign()
    {
        if ($this->signtype == 'MD5') {
            $this->createMD5Sign();
        } else {
            $this->createRSASign();
        }
    }

    public function createMD5Sign()
    {
        $signPars = "";
        ksort($this->parameters);
        foreach ($this->parameters as $k => $v) {
            if ("" != $v && "sign" != $k) {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        $signPars .= "key=" . $this->getKey();
        $sign = strtoupper(md5($signPars));
        $this->setParameter("sign", $sign);

        //debug信息
        $this->_setDebugInfo($signPars . " => sign:" . $sign);
    }

    public function createRSASign()
    {

        $signPars = "";
        ksort($this->parameters);

        foreach ($this->parameters as $k => $v) {
            if ($v !== "" && $k !== "sign") {
                $signPars .= $k . "=" . $v . "&";
            }
        }

        $signPars = rtrim($signPars, "&");

        // ✅ 1. 修复私钥格式（核心）
        $key = $this->private_rsa_key;

        $key = str_replace([
            "-----BEGIN RSA PRIVATE KEY-----",
            "-----END RSA PRIVATE KEY-----",
            "\r", "\n", " "
        ], "", $key);

        $key = "-----BEGIN RSA PRIVATE KEY-----\n"
            . chunk_split($key, 64, "\n")
            . "-----END RSA PRIVATE KEY-----";

        // ✅ 2. 正确获取私钥
        $res = openssl_pkey_get_private($key);

        if ($res === false) {
            while ($msg = openssl_error_string()) {
                echo "OpenSSL error: " . $msg . PHP_EOL;
            }
            throw new Exception("私钥解析失败");
        }

        // ✅ 3. 签名
        if ($this->signtype == 'RSA_1_1') {
            $ok = openssl_sign($signPars, $sign, $res);
        } else if ($this->signtype == 'RSA_1_256') {
            $ok = openssl_sign($signPars, $sign, $res, OPENSSL_ALGO_SHA256);
        } else {
            throw new Exception("未知签名类型");
        }

        if (!$ok) {
            throw new Exception("签名失败");
        }

        // ❌ 不要再用 openssl_free_key（PHP8自动释放）

        $sign = base64_encode($sign);
        $this->setParameter("sign", $sign);

        // debug
        $this->_setDebugInfo($signPars . " => sign:" . $sign);
    }

    /**
     *设置debug信息
     */
    public function _setDebugInfo($debugInfo)
    {
        $this->debugInfo = $debugInfo;
    }
}