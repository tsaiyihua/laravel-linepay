<?php
/**
 * Created by PhpStorm.
 * User: yihua
 * Date: 2018/11/28
 * Time: 下午 4:23
 */

namespace TsaiYiHua\LinePay;


use Illuminate\Support\Facades\App;
use TsaiYiHua\LinePay\Constants\Currency;
use TsaiYiHua\LinePay\Exceptions\LinePayException;
use TsaiYiHua\LinePay\Services\CacheService;

class Confirm extends LinePayAbstract
{
    use LinePayTrait;

    protected $requestUri = '/v2/payments/{transactionId}/confirm';
    protected $cacheSrv;
    protected $transCacheKey;

    public function __construct(CacheService $cacheSrv)
    {
        parent::__construct();
        $this->requestMethod = 'post';
        $this->cacheSrv = $cacheSrv;
    }

    /**
     * @param $data
     * @return $this
     * @throws LinePayException
     */
    public function setPostData($data)
    {
        $transData = $this->cacheSrv->setKey($data['orderId'])->getData();
        $transactionId = $data['transactionId'];
        $this->requestUri = str_replace('{transactionId}', $transactionId, $this->requestUri);
        $this->postData->put('amount', $transData['amount']);
        $this->postData->put('currency', $transData['currency'] ?? Currency::TWD);
        return $this;
    }

    /**
     * @param \Closure $function
     * @return mixed
     * @throws LinePayException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function confirm(\Closure $function)
    {
        return $function($this->response);
    }
}