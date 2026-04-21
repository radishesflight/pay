<?php

namespace RadishesFlight\Pay\Hwc;

class HwcRequest
{
    /**
     * @var ClientResponseHandler
     */
    public $resHandler;
    /**
     * @var RequestHandler
     */
    public $reqHandler;
    /**
     * @var PayHttpClient
     */
    public $pay;
    /**
     * @var mixed
     */
    public $cfg;

    public function __construct($config)
    {
        $this->resHandler = new ClientResponseHandler();
        $this->reqHandler = new RequestHandler();
        $this->pay = new PayHttpClient();
        $this->cfg = $config;

        $this->reqHandler->setGateUrl($this->cfg['url']);

        $sign_type = $this->cfg['sign_type'];

        if ($sign_type == 'MD5') {
            $this->reqHandler->setKey($this->cfg['key']);
            $this->resHandler->setKey($this->cfg['key']);
            $this->reqHandler->setSignType($sign_type);
        } else if ($sign_type == 'RSA_1_1' || $sign_type == 'RSA_1_256') {
            $this->reqHandler->setRSAKey($this->cfg['private_rsa_key']);
            $this->resHandler->setRSAKey($this->cfg['public_rsa_key']);
            $this->reqHandler->setSignType($sign_type);
        }
        return $this;
    }

    /**
     * 提交订单信息
     */
    public function submitOrderInfo($params)
    {
        $this->reqHandler->setReqParams($params, ['method']);
//        $this->reqHandler->setParameter('service', 'unified.trade.native');//接口类型：unified.trade.micropay
        $this->reqHandler->setParameter('mch_id', $this->cfg['mchId']);//必填项，商户号，由平台分配

        $this->reqHandler->setParameter('version', $this->cfg['version']);

        $this->reqHandler->setParameter('sign_type', $this->cfg['sign_type']);
        $this->reqHandler->setParameter('nonce_str', mt_rand());//随机字符串，必填项，不长于 32 位


        $this->reqHandler->createSign();//创建签名

        $data = Utils::toXml($this->reqHandler->getAllParameters());
        //var_dump($data);
        Utils::dataRecodes(date("Y-m-d H:i:s", time()) . '支付请求XML', $data);//请求xml记录到result.txt
        $this->pay->setReqContent($this->reqHandler->getGateURL(), $data);
        if ($this->pay->call()) {
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            $res = $this->resHandler->getAllParameters();
            Utils::dataRecodes(date("Y-m-d H:i:s", time()) . '支付返回XML', $res);
            if ($this->resHandler->isTenpaySign()) {
                return $this->resHandler->getAllParameter();
            }
            return ['status' => 500, 'msg' => $this->resHandler->getParameter('message')];
        } else {
            return ['status' => 500, 'msg' => 'Response Code:' . $this->pay->getResponseCode() . ' Error Info:' . $this->pay->getErrInfo()];
        }
    }


    public function refund($params){
        $this->reqHandler->setReqParams($params,['method']);
        $reqParam = $this->reqHandler->getAllParameters();
        if(empty($reqParam['transaction_id']) && empty($reqParam['out_trade_no'])){
            return  ['status' => 500, 'msg' => '请输入商户订单号或平台订单号!'];
        }
        $this->reqHandler->setParameter('version', $this->cfg['version']);

        $this->reqHandler->setParameter('mch_id', $this->cfg['mchId']);//必填项，商户号，由平台分配


        $this->reqHandler->setParameter('sign_type', $this->cfg['sign_type']);
        $this->reqHandler->setParameter('op_user_id', $this->cfg['mchId']);
        $this->reqHandler->setParameter('nonce_str', mt_rand());//随机字符串，必填项，不长于 32 位
//        dd($this->reqHandler->getAllParameters());
        $this->reqHandler->createSign();//创建签名
        $data = Utils::toXml($this->reqHandler->getAllParameters());//将提交参数转为xml，目前接口参数也只支持XML方式

        $this->pay->setReqContent($this->reqHandler->getGateURL(),$data);
        if($this->pay->call()){
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if($this->resHandler->isTenpaySign()){
                //当返回状态与业务结果都为0时才返回支付二维码，其它结果请查看接口文档
                if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){
                    return $this->resHandler->getAllParameters();
                }else{
                    return ['status' => 500, 'msg' => $this->resHandler->getParameter('err_msg')];
                }
            }
            return ['status' => 500, 'msg' => $this->resHandler->getParameter('message')];
        }else{
            return ['status' => 500, 'msg' => $this->pay->getErrInfo()];
        }
    }

    public function callback($xml){
        $this->resHandler->setContent($xml);
//        $this->resHandler->setKey($this->cfg->C('key'));
        if($this->resHandler->isTenpaySign()){
            if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){
                $tradeno = $this->resHandler->getParameter('out_trade_no');
                // 此处可以在添加相关处理业务，校验通知参数中的商户订单号out_trade_no和金额total_fee是否和商户业务系统的单号和金额是否一致，一致后方可更新数据库表中的记录。
                //更改订单状态
                Utils::dataRecodes('接口回调收到通知参数',$this->resHandler->getAllParameters());
                ob_clean();
                return $this->resHandler->getAllParameter();
            }else{
                return ['status' => 500, 'msg' => $this->resHandler->getParameter('err_msg')];
            }
            return ['status' => 500, 'msg' => $this->resHandler->getParameter('message')];
        }else{
            return ['status' => 500, 'msg' => $this->pay->getErrInfo()];
        }
    }


    public function execute($params)
    {
        $this->reqHandler->setReqParams($params, ['method']);
//        $this->reqHandler->setParameter('service', 'unified.trade.native');//接口类型：unified.trade.micropay
        $this->reqHandler->setParameter('mch_id', $this->cfg['mchId']);//必填项，商户号，由平台分配

        $this->reqHandler->setParameter('version', $this->cfg['version']);
        $this->reqHandler->setParameter('op_user_id', $this->cfg['mchId']);
        $this->reqHandler->setParameter('sign_type', $this->cfg['sign_type']);
        $this->reqHandler->setParameter('nonce_str', mt_rand());//随机字符串，必填项，不长于 32 位


        $this->reqHandler->createSign();//创建签名

        $data = Utils::toXml($this->reqHandler->getAllParameters());
        //var_dump($data);
        Utils::dataRecodes(date("Y-m-d H:i:s", time()) . '支付请求XML', $data);//请求xml记录到result.txt
        $this->pay->setReqContent($this->reqHandler->getGateURL(), $data);
        if ($this->pay->call()) {
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            $res = $this->resHandler->getAllParameters();
            Utils::dataRecodes(date("Y-m-d H:i:s", time()) . '支付返回XML', $res);
            if ($this->resHandler->isTenpaySign()) {
                return $this->resHandler->getAllParameter();
            }
            return ['status' => 500, 'msg' => $this->resHandler->getParameter('message')];
        } else {
            return ['status' => 500, 'msg' => 'Response Code:' . $this->pay->getResponseCode() . ' Error Info:' . $this->pay->getErrInfo()];
        }
    }

}