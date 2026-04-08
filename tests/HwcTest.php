<?php

use RadishesFlight\Pay\Hwc\HwcRequest;

require_once __DIR__ . '/../vendor/autoload.php';
$config = [
    'url' => 'https://pay.hstypay.com/v2/pay/gateway',     /*支付接口请求地址，请联系技术支持确认 */
    'mchId' => '1030189499',   /* 商户号，建议用正式的，于申请成功后的开户邮件中获取，若未开户需用测试的请联系技术支持 */
    'version' => '2.0',
    'sign_type' => 'RSA_1_256',
    'public_rsa_key' => '-----BEGIN PUBLIC KEY-----MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtS2jzJJqkOS65yHoeGjKve7Jkcj+VmbiiK9iVvnIkkQjp8/Vr80/+J1GqeQQMDNtme2RLkhmI4x408pzuas1QkuNpauQ5EX9ooXR1fPQspOZrg4MkRNI1FBol5lC+jOfNOrn38dZy9GKuAPNiOL3m6buYgpJtymwReVso10yBj8x8GDQexd87JTEGA/zqcbvUgzGnTWaZJX68ZhhcjAai9V+tsvaSeEtRPlgGDSn2Fgz6bpJRe7wq9OZJ81jMIi0RRwm4OrlAaot8Kx/ZAYyiN5Q2oHqPnAcY+2v32rjrg21jtOzh64slBMpmDP5E+n8G8plAu423LxueHMW5+CMqQIDAQAB-----END PUBLIC KEY-----', //* RSA验签平台公钥，建议用正式的，登录商户后台查看，若未开户需用测试的请联系技术支持 */
    'private_rsa_key' => '-----BEGIN RSA PRIVATE KEY-----MIIEogIBAAKCAQEAhvoihbMT0FyBudz+DHk1y/ud5fnsdDCTojggPHJ3cWSyEhG53Q9vMrBuCymfL6eJ+eJSDIURK4HWMOKlAyhdI3PJ29ZBzq7uq4zDFbLgfvNHAsQJL/EqMhi85rLq0BlkObiAhr2c6m4wfi7JHNxfBm+I1xsdg6f0Ex186jWo8/YVxlkx2SoygukfqVUqjS9yg+/NI+agHvpaWWzm8hH+nfFLVOAN4FZQQM7PE07Qv0B9fXnvht17QRHYpZa///tpH8qNOPb4oexRGnBrtGiMHBqwE/tOVV8Q8y/FA1sH8p1Z1aEPgp2YwRkZYXvx5JImIn5rgoi3F0bD1ZTElPb7QQIDAQABAoIBAAHQgQBNHPoTFEdcWinuwzNfyYqa5nvVX1ax7HUDkOl8Ugx+IoJ/P55t0rzhrVslpGwwh6vVahqsnyV/FnrJYx8j4lXLNo1BusGhncm/4tayDIVT/0+erVOJE5kCVLfBb6B7A3dMOzzHiG1Z0ahKqhhbDXreyXHIozZSkihP2lqVMiyCEsib6xGUYHp7pNZC0wUcg8n8xTlagVW/2n7L+D3yjhy+WeMYFJv0mBgOjORkYmx7NAyDRyBJ4H5bs1S6DlqC7WSONmOFHIT6FPsU3hmop9YMW3KzMiwGKPKwhqxsylrSwK58flLycRxizw07yp/lh+Q72/I8qYWGr8ImMIUCgYEA6DZK00mUgNoKFTFBO7sk4B8GCoUfGOhu977H7kbflk6LwEZrLDz0aykBEeHPined63d+VB4fHe2OQkNQk6yh4VY3wPJVtAsRlCMDfQmp7pzLAD3XEiOD3Gi6+GXBMnpkiRr2G+p/NgtyOYW09rIX6xDt2+nWzAkgflvLv8D6EbcCgYEAlM3gqenANk8+VXqoU2Nt3kEEO1rpdueV78BdavfGTQURQgjNCLysFCgEWn95xLLHPKHXa9YyaRNLkaqSZSemgq7CMK0HRv3dV4dgvYL6tlWJ8wvXqy3kQBmdfQpuJDsQx4r7S5H3Nmng+TBb68jAl5A2QKCc8bXtY1nC1YLuescCgYAu4kdZQZHqMhu3C6rQFIjtd1YQ6a/Np6BABRT41vZtso4k1BLva58tw6mjoqP0oRIRaJ7o/Ovrbvs6Bb1PE5vbkzzOiB8lqtZxwmAB7uGQe7fA5Lt3vhPxfHPDk0femTeTNw5ZtI7aqpT1aDmRVYPewhxEOoJTz8Pvvzj0DlJvIQKBgAdjaYJGPurzaE7qNi1dxHjClak0zF7BBOrQjFLhVpFAbSjwMu36IDkn+39a0Pr5PXc/Oej6y6n38UqcQ4SOQXA/qRitnqzhsfnEmQMP287t1Fmi/uRa9PhRzUYGHI3j+ONPfUa1SqcC/s6gng/I+fcMjAUNdH1z4QOL02ayh5DFAoGAdAE3RmaKQyiP4wUsbKYW5SIjC+eVN3aUQrrOFu31zBxpKyflghE4du0bQZXAZ/K5UJD3YEo9j8wYh14Io6DI2slf3HPWjpB7Js7q3IDzKjvrGjdFx6eb0q2xq7lW9gOWXwsEKtzIO0omz1tKW8MtNe+qqikWYTWtfYRTsYPW/Zw=-----END RSA PRIVATE KEY-----'//* RSA签名私钥，建议用
];
$req = new HwcRequest($config);
$response = $req->submitOrderInfo([
    'total_fee' => 1,//Int 总金额，以分为单位，不允许包含任何字、符号
    'mch_create_ip' => '127.0.0.1', //String 终端IP
    'service' => 'pay.alipay.app', //String(32) 接口类型：统一扫码：unified.trade.native app微信支付：pay.weixin.raw.app app支付宝：pay.alipay.app
    'out_trade_no' => mt_rand(100000, 999999),// String(32) 商户系统内部的订单号 ,32个字符内、 可包含字母,确保在商户系统唯一
    'body' => '测试商品',// String(128) 商品描述
    'notify_url' => 'https://www.sfgd.com/notify.php',//String(256) 异步通知地址
//    'appid'=>"wx41fb8a8b8101bada",//String 商户app对应的微信开放平台移动应用APPID app微信支付pay.weixin.raw.app必传
]);
print_r($response);