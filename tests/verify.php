<?php

require __DIR__ . '/common.php';

parse_str('money=0.01&out_trade_no=1648407775&pid=1023&trade_no=2022032803025623672&trade_status=TRADE_SUCCESS&type=alipay&sign=5108f85d2467e635e275c1e8d39fd6c3&sign_type=MD5', $params);

$simplePay = new \Midsmr\SimplePay\SimplePay(GATEWAY, PID, KEY);

if ($simplePay->verify($params)) {
    die('yes');
} else {
    die('no');
}
