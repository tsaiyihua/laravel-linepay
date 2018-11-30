<?php
/**
 * Created by PhpStorm.
 * User: yihua
 * Date: 2018/11/29
 * Time: 上午 11:35
 */

namespace TsaiYiHua\LinePay;


use TsaiYiHua\LinePay\Constants\Currency;
use TsaiYiHua\LinePay\Exceptions\LinePayException;
use TsaiYiHua\LinePay\Services\CacheService;

class PreApprovedPay extends LinePayAbstract
{
    use LinePayTrait;
    protected $requestUri = '/v2/payments/preapprovedPay/{regKey}/payment';
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
        $regKey = $data['regKey'];
        $this->requestUri = str_replace('{regKey}', $regKey, $this->requestUri);
        $this->postData->put('productName', $transData['productName']);
        $this->postData->put('amount', $transData['amount']);
        $this->postData->put('currency', $transData['currency'] ?? Currency::TWD);
        $this->postData->put('orderId', $data['orderId']);
        return $this;
    }

    /**
     * 不要直接請款，處理到授權後需要呼叫「請款 API」才能完成交易
     * @return $this
     * @throws LinePayException
     */
    public function doNotCapture()
    {
        if ($this->postData->isNotEmpty()) {
            $this->postData->put('capture', false);
        } else {
            throw new LinePayException('Empty post data');
        }
        return $this;
    }

    public function autoPay()
    {
        return $this->response;
    }
}