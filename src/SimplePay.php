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
     * 跳转提交
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
    public function submit(
        string $type,
        string $out_trade_no,
        string $notify_url,
        string $return_url,
        string $name,
        string $money,
        ?array $extends = []
    ): string
    {
        return $this->create('submit', $type, $out_trade_no, $notify_url, $return_url, $name, $money, $extends);
    }

    /**
     * 接口提交
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
    public function mapi(
        string $type,
        string $out_trade_no,
        string $notify_url,
        string $return_url,
        string $name,
        string $money,
        ?array $extends = []
    ): string
    {
        return $this->create('mapi', $type, $out_trade_no, $notify_url, $return_url, $name, $money, $extends);
    }

    /**
     * 创建订单方法
     *
     * @param string $method 提交类型
     * @param string $type 支付类型
     * @param string $out_trade_no 商户订单号
     * @param string $notify_url 异步通知地址
     * @param string $return_url 同步通知地址
     * @param string $name 商品名称
     * @param string $money 支付金额
     * @param array|null $extends 拓展参数
     * @return string
     */
    protected function create(
        string $method,
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

        if ($method == 'submit') {
            $file = 'submit.php';
        } elseif ($method == 'mapi') {
            $file = 'mapi.php';
        } else {
            throw new \LogicException("错误的提交方式:{$method}");
        }

        return "{$this->gateway}?{$query}";
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