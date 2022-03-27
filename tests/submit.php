<?php

require __DIR__ . '/common.php';

$simplePay = new \Midsmr\SimplePay\SimplePay(GATEWAY, PID, KEY);

$url = $simplePay->create('alipay', time(), 'call', 'call', 'te', '0.01');

header("Location: {$url}");
