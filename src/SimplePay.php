<?php

namespace Midsmr\SimplePay;

class SimplePay
{
    public function __construct(
        public ?string $gateway = null,
        public ?string $pid = null,
        public ?string $key = null
    )
    {
        //
    }

    /**
     * 创建订单方法
     *
     * @param string $type 支付类型
     * @param string $out_trade_no 商户订单号
     * @param string $notify_url 异步通知地址
     * @param string $return_url 同步通知地址
     * @param string $name 商品名称
     * @param string $money 支付金额
     * @param array|null $extends 拓展参数
     * @return string
     */
    public function create(
        string $type,
        string $out_trade_no,
        string $notify_url,
        string $return_url,
        string $name,
        string $money,
        ?array $extends = []
    ): string
    {
        if (empty($this->gateway) || empty($this->pid) || empty($this->key)) {
            throw new \LogicException('缺少所需参数:gateway|pid|key');
        }

        $params = [
            'pid' => $this->pid,
            'type' => $type,
            'out_trade_no' => $out_trade_no,
            'notify_url' => $notify_url,
            'return_url' => $return_url,
            'name' => $name,
            'money' => (float) $money,
            'extends' => json_encode($extends)
        ];

        $params['sign'] = $this->sign($params);
        $params['sign_type'] = 'MD5';

        $query = http_build_query($params);

        return "{$this->gateway}submit.php?{$query}";
    }

    public function verify(array $params): bool
    {
        if (empty($this->key)) {
            throw new \LogicException('参数key为空');
        }

        if (!isset($params['sign'])) {
            throw new \LogicException('签名参数为空');
        }

        if ($params['sign'] != $this->sign($params)) {
            return false;
        }

        return true;
    }

    public function sign(array $params): string
    {
        if (empty($this->key)) {
            throw new \LogicException('参数key为空');
        }

        $str = '';

        ksort($params);

        foreach ($params as $key => $value) {
            if ($key == 'sign' || $key == 'sign_type' || $value == '') {
                continue;
            }
            $str .= "{$key}={$value}&";
        }

        $str = trim($str, '&');

        return md5(($str.$this->key));
    }
}