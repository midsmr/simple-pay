<?php

require __DIR__ . '/common.php';

$simplePay = new \Midsmr\SimplePay\SimplePay(GATEWAY, PID, KEY);

$url = $simplePay->mapi('alipay', time(), 'call', 'call', 'te', '0.01', '123.234.123.222', 'mobile');

header("Location: {$url}");
