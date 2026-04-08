<?php

use RadishesFlight\Pay\TongLian\TlPay;


require_once __DIR__ . '/../vendor/autoload.php';


$config = [
    // 接口域名（生产环境）
    'api_host' => 'https://vsp.allinpay.com',
    // 接口域名（测试环境）
    // 'api_host' => 'https://syb-test.allinpay.com',

    // 商户配置
    'cusid' => '564821089swQFP0',  // 商户号
    'appid' => '00396464',          // 应用ID
    'version' => '11',              // 版本号
    'orgid' => '',                  // 机构号（可为空）

    // 密钥配置
    'private_key' => '+3qE672wr5vHCRziKfi+8fZHW2Tc1kvZTQNha4GCYAWw6euxfTNS53rwKrj4nw0GKyigHsGbFhxDTG3yHNzmIURItsfuC2HFUGE0EyCUfLeGGuFMmGfqQE21JbktnMzGYR3z0+rWU11lHHxXkuj04aEo5o4yXlzk3AfICilZ7CdkwIQv7RL/8wRLs9iFq4xRGIyjv/ylZVf1RicO5TO4PmCHi3kcQdnIKQ/U29bG9YHeftUx0pT9e5x/hmhKC8sRjuF6/BTERVtkoJCKY906pMphilwEbuIqqUjHXkN57g7UU7Pjv48CCEeCjaJX2r9vxHlwjxHQIDAQABAoIBADGkoR5ZQ1AJL/LYM47c4EBVu2KlsYJAL25AqHh8HKbx16VMMdgLifU8QKZ1Xznbue4QrXq2ZsHPF9BcFQ7XxK55HdM1o3kInb7Xr1B5J9a9ON6gLwz692RX5L0d0+uPBiyiv6RkSwtaV0FFtQewf8FUH9fRYx+lkEUYIH+00RKeKBItkfDMpTHr/nKQWmb3q+50rNOLweBfk+ukao5zMu/iN63GD6GdkaOmUn2HJQ0szIMIe7gw8lEPZeSYHlps6S7kiQZ0HveJ2IfLtm0rmk6x8F1oNchLTCoQeEbfDv45vObNv+ffwbPfTqqLqSCdXSYSaaaz3Nrr5lPp30qYH8cCgYEA+cY+gxhBbgS9iD5TMyNziO0/dtNQ/47A2vlXxLLyEJl7RHNdTVYZxi69w9mu3peUNrvDHT6GNDT49qzDR0+RtukTfWkv/ys/sa2XRoqIXaErAqARazG4ZJwn4XXb+mYOJY9d110roiTfc4bKxwUXmnV96AyS0dMUo657eU55C3sCgYEA6w0ON9YDGLGXsfB2RCQr3pN0aDnet4/iIfLzOkyd/dL43ghjSv51nCQP6MiF7rvzlP3Dy+cLadFSb2jAetbw4LBg5dWHBuTdIWV4W23mGgwNUHbHxEaqD96dokieg3eWHuKrXWp8Ha6469sG9fbRDt0+Jg8TkqJbAbM156x/pkcCgYAEMTujX6jZe+LigolK9nFd/v1ttSZK486A3maGuqotdSYIhBcw0R0loms0+lZhDhJCyOwBdaczASCco1GzxLYhZ9AX7sgLdGJhTRSY7oJTb/0U0jL4paD+r1BMDHpgvY3HO5zLnJi64/uMKNsGdCNtSEOQvYVJWE2kYa1Y3+RQOQKBgAMNWVjqQ4IHlFOwLqj120f5nDJaRgUWLjaIpBXmtsp7+dVQQJHRug87/KTmLa8K67/Mh8VXC7PlDu/5aT5vGhOg1rFFU4qIYEK1wZlWVP2TmHyp/jATRtQL7PoVfVFxtRZTlSwSXOg5w5b0ciOxf8d0ogD2gyeNSic8f3+xorqzAoGBAPXsON/olUZX/9gNJriPoT/YDz6yTKipN4KqNQwJGFyBJTMo+VxZhOa5+wgGiciSM0Fxllwwrc8F9Q+mbGAvB91YK/RuVj4ttp9qbxrImfj28Hw6i3aLaY/5TMhsJmX9Hwx2NWWCRQ9vFxAtmsikENw7v/WsEK+jrWjSyQMFzy/0',
    'public_key' => '/ZnAVYHscEELdCNfNTHGuBv1nYYEY9FrOzE0/4kLl9f7Y9dkWHlc2ocDwbrFSm0Vqz0q2rJPxXUYBCQl5yW3jzuKSXif7q1yOwkFVtJXvuhf5WRy+1X5FOFoMvS7538No0RpnLzmNi3ktmiqmhpcY/1pmt20FHQQIDAQAB',
];

//支付
$reconciliationService = new TlPay($config);
$billDate =  date('Ymd');
$billType = 1;
$reconciliationService->setRefundParams('trxamt', 1);//交易金额 单位为分
 $reconciliationService->setRefundParams('reqsn', mt_rand(100000000, 999999999));// 商户交易单号 商户的交易订单号
 $reconciliationService->setRefundParams('paytype', 'W03');// 支付类型：W01 微信扫码支付 W02 微信JS支付 W03 微信APP支付 W06 微信小程序支付 W11 微信订单支付 A01 支付宝扫码支付 A02 支付宝JS支付 A03 支付宝APP支付 U01 银联扫码支付(CSB) U02 银联JS支付 U03 银联APP支付 S01 数币扫码支付 S03 数字货币H5/APP N03 网联支付
//W01 微信扫码支付 W02 微信JS支付
$reconciliationService->setRefundParams('body', 1);//订单标题订单商品名称，为空则以商户名作为商品名称
$reconciliationService->setRefundParams('remark', 1);//备注信息
$reconciliationService->setRefundParams('validtime', 1);//订单有效时间，以分为单位，
$reconciliationService->setRefundParams('notify_url', 'https://www.tsdsd.com/api/pay/notify');//交易结果通知地址https只支持默认端口
$response= $reconciliationService->pay();
print_r($response);
exit();


//对账单
$config = [
    // 接口域名（生产环境）
    'api_host' => 'https://cus.allinpay.com',
    // 接口域名（测试环境）
    // 'api_host' => 'https://syb-test.allinpay.com',

    // 商户配置
    'cusid' => '56482108999QFP0',  // 商户号
    'appid' => '00396264',          // 应用ID
    'version' => '11',              // 版本号
    'orgid' => '',                  // 机构号（可为空）

    // 密钥配置
    'private_key' => 'MIIEowIBAAKCAQEA5VW4UtW9onroC5+3qE672wr5vHCRziKfi+8fZHW2Tc1kvZTQNha4GCYAWw6euxfTNS53rwKrj4nw0GKyigHsGbFhxDTG3yHNzmIURItsfuC2HFUGE0EyCUfLeGGuFMmGfqQE21JbktnMzGYR3z0+rWU11lHHxXkuj04aEo5o4yXlzk3AfICilZ7CdkwIQv7RL/8wRLs9iFq4xRGIyjv/ylZVf1RicO5TO4PmCHi3kcQdnIKQ/U29bG9YHeftUx0pT9e5x/hmhKC8sRjuF6/BTERVtkoJCKY906pMphilwEbuIqqUjHXkN57g7UU7Pjv48CCEeCjaJX2r9vxHlwjxHQIDAQABAoIBADGkoR5ZQ1AJL/LYM47c4EBVu2KlsYJAL25AqHh8HKbx16VMMdgLifU8QKZ1Xznbue4QrXq2ZsHPF9BcFQ7XxK55HdM1o3kInb7Xr1B5J9a9ON6gLwz692RX5L0d0+uPBiyiv6RkSwtaV0FFtQewf8FUH9fRYx+lkEUYIH+00RKeKBItkfDMpTHr/nKQWmb3q+50rNOLweBfk+ukao5zMu/iN63GD6GdkaOmUn2HJQ0szIMIe7gw8lEPZeSYHlps6S7kiQZ0HveJ2IfLtm0rmk6x8F1oNchLTCoQeEbfDv45vObNv+ffwbPfTqqLqSCdXSYSaaaz3Nrr5lPp30qYH8cCgYEA+cY+gxhBbgS9iD5TMyNziO0/dtNQ/47A2vlXxLLyEJl7RHNdTVYZxi69w9mu3peUNrvDHT6GNDT49qzDR0+RtukTfWkv/ys/sa2XRoqIXaErAqARazG4ZJwn4XXb+mYOJY9d110roiTfc4bKxwUXmnV96AyS0dMUo657eU55C3sCgYEA6w0ON9YDGLGXsfB2RCQr3pN0aDnet4/iIfLzOkyd/dL43ghjSv51nCQP6MiF7rvzlP3Dy+cLadFSb2jAetbw4LBg5dWHBuTdIWV4W23mGgwNUHbHxEaqD96dokieg3eWHuKrXWp8Ha6469sG9fbRDt0+Jg8TkqJbAbM156x/pkcCgYAEMTujX6jZe+LigolK9nFd/v1ttSZK486A3maGuqotdSYIhBcw0R0loms0+lZhDhJCyOwBdaczASCco1GzxLYhZ9AX7sgLdGJhTRSY7oJTb/0U0jL4paD+r1BMDHpgvY3HO5zLnJi64/uMKNsGdCNtSEOQvYVJWE2kYa1Y3+RQOQKBgAMNWVjqQ4IHlFOwLqj120f5nDJaRgUWLjaIpBXmtsp7+dVQQJHRug87/KTmLa8K67/Mh8VXC7PlDu/5aT5vGhOg1rFFU4qIYEK1wZlWVP2TmHyp/jATRtQL7PoVfVFxtRZTlSwSXOg5w5b0ciOxf8d0ogD2gyeNSic8f3+xorqzAoGBAPXsON/olUZX/9gNJriPoT/YDz6yTKipN4KqNQwJGFyBJTMo+VxZhOa5+wgGiciSM0Fxllwwrc8F9Q+mbGAvB91YK/RuVj4ttp9qbxrImfj28Hw6i3aLaY/5TMhsJmX9Hwx2NWWCRQ9vFxAtmsikENw7v/WsEK+jrWjSyQMFzy/0',
    'public_key' => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCm9OV6zH5DYH/ZnAVYHscEELdCNfNTHGuBv1nYYEY9FrOzE0/4kLl9f7Y9dkWHlc2ocDwbrFSm0Vqz0q2rJPxXUYBCQl5yW3jzuKSXif7q1yOwkFVtJXvuhf5WRy+1X5FOFoMvS7538No0RpnLzmNi3ktmiqmhpcY/1pmt20FHQQIDAQAB',
];
$reconciliationService = new TlPay($config);
$billDate =  date('Ymd');
$billType = 1;
$billContent = $reconciliationService->getReconciliationBill($billDate, $billType);
$billContent = $billContent ? json_decode($billContent, true) : [];
print_r($billContent);
exit();
