<?php
declare(strict_types=1);
namespace RadishesFlight\Pay\FuYou;
use function app\extend\WechatTools\env;

class WechatTools {
    private $appid;
    private $appsecret;

    public function __construct($appid, $appsecret)
    {
        $this->appid = $appid;
        $this->appsecret = $appsecret;
    }
    /**
     * 获取微信用户信息
     *
     * @return array 微信用户信息数组
     */
    public function getUserAll($code)
    {
        $data = $this->get_access_token($code); //获取网页授权access_token和用户openid
        $data_all = $this->get_user_info($data['access_token'], $data['openid']); //获取微信用户信息
        return $data_all;
    }
    // /**
    //  * 2、用户授权并获取code
    //  * @param string $callback 微信服务器回调链接url
    //  */
    // private function get_code($callback)
    // {
    //     $appid = $this->appid;
    //     $scope = 'snsapi_userinfo';
    //     $state = md5(uniqid(rand(), TRUE)); //唯一ID标识符绝对不会重复
    //     $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . urlencode($callback) .  '&ponse_type=code&scope=' . $scope . '&state=' . $state . '#wechat_redirect';
    //     header("Location:$url");
    // }

    /**
     * 3、使用code换取access_token
     * @param string 用于换取access_token的code，微信提供
     * @return array access_token和用户openid数组
     */
    private function get_access_token($code)
    {
        $appid = $this->appid;
        $appsecret = $this->appsecret;
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $appsecret . '&code=' . $code . '&grant_type=authorization_code';
        //返回的json数组转换成array数组
        $user = json_decode(file_get_contents($url),true);
        if (isset($user['errcode'])) {
            throw new \Exception($user['errmsg'],$user['errcode']);
        }
        return $user;
    }
    /**
     * 4、使用access_token获取用户信息
     * @param string access_token
     * @param string 用户的openid
     * @return array 用户信息数组
     */
    private function get_user_info($access_token, $openid)
    {
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';
        //返回的json数组转换成array数组
        $user = json_decode(file_get_contents($url),true);
        if (isset($user['errcode'])) {
            throw new \Exception($user['errmsg'],$user['errcode']);
        }
        return $user;
    }


    /**
     * 5、使用code获取用户信息（小程序）
     * @param string access_token
     * @param string 用户的openid
     * @return array 用户信息数组
     */
    public function get_user_openid($code)
    {
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$this->appid}&secret={$this->appsecret}&js_code={$code}&grant_type=authorization_code";
        //返回的json数组转换成array数组
        $user = json_decode(file_get_contents($url),true);
        if (isset($user['errcode']) && $user['errcode'] != '0') {
            throw new \Exception($user['errmsg'],$user['errcode']);
        }
        return $user;
    }
}
