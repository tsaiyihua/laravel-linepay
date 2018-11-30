<?php
/**
 * Created by PhpStorm.
 * User: yihua
 * Date: 2018/11/30
 * Time: 上午 11:23
 */

namespace TsaiYiHua\LinePay;

use TsaiYiHua\LinePay\Constants\Currency;
use TsaiYiHua\LinePay\Exceptions\LinePayException;

/**
 * Class Capture
 * @package TsaiYiHua\LinePay
 * @todo 解決不能傳送 capture 為 false 的問題。
 *  LinePayException (2103) Parameter is not allowed. [capture:`false`]
 *  看起來不是我的問題啊。。。QQ
 */
class Capture extends LinePayAbstract
{
    use LinePayTrait;

    protected $requestUri = '/v2/payments/authorizations/{transactionId}/capture';

    public function __construct()
    {
        parent::__construct();
        $this->requestMethod = 'post';
    }

    /**
     * @param $data
     * @return $this
     * @throws LinePayException
     */
    public function setPostData($data)
    {
        $transactionId = $data['transactionId'];
        $this->requestUri = str_replace('{transactionId}', $transactionId, $this->requestUri);
        $this->postData->put('amount', $data['amount']);
        $this->postData->put('currency', $data['currency'] ?? Currency::TWD);
        return $this;
    }

    /**
     * @param \Closure $function
     * @return mixed
     * @throws LinePayException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function capture(\Closure $function)
    {
        return $function($this->response);
    }
}