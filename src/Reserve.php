<?php
namespace TsaiYiHua\LinePay;

use Illuminate\Support\Str;
use TsaiYiHua\LinePay\Constants\ConfirmUrlType;
use TsaiYiHua\LinePay\Constants\Currency;
use TsaiYiHua\LinePay\Constants\PayType;
use TsaiYiHua\LinePay\Exceptions\LinePayException;
use TsaiYiHua\LinePay\Services\CacheService;
use TsaiYiHua\LinePay\Services\StringService;
use TsaiYiHua\LinePay\Validations\ReserveValidation;

class Reserve extends LinePayAbstract
{
    use LinePayTrait;

    protected $requestUri = '/v3/payments/request';
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
        $validator = ReserveValidation::validator($data);
        if ($validator->fails()) {
            throw new LinePayException($validator->getMessageBag());
        }
        /**
         * Set required fields
         */
        $orderId = $data['orderId'] ?? StringService::identifyNumberGenerator('O');
        $this->cacheSrv->setKey($orderId)->setData($data);
        $confirmUrl = route('linepay.confirm');
//        $this->postData->put('packages', [
//            'id' => Str::uuid()->toString(),
//            'amount' => $data['amount'],
//            'products' => array([
//                'name' => $data['productName'],
//                'quantity' => $data['quantity'],
//                'price' => $data['price'],
//            ])
//        ]);
        $this->postData->put('packages', $data['packages']);
//        $this->postData->put('amount', $data['amount']);
        $this->postData->put('currency', $data['currency'] ?? Currency::TWD);
        $this->postData->put('redirectUrls', [
            'confirmUrl' => $data['confirmUrl'] ?? $confirmUrl,
            'confirmType' => ConfirmUrlType::WEB
        ]);
        $this->postData->put('orderId', $data['orderId'] ?? StringService::identifyNumberGenerator('O'));

        /**
         * Optional fields
         */
//        $optionParams = [
//            'productImageUrl' ,'mid', 'oneTimeKey', 'confirmUrlType' ,'checkConfirmUrlBrowser',
//            'cancelUrl', 'packageName', 'deliveryPlacePhone', 'payType', 'langCd', 'capture',
//        ];
//        foreach($optionParams as $param) {
//            if (isset($data[$param])) {
//                $this->postData->put($param, $data[$param]);
//            }
//        }
        return $this;
    }

    /**
     * 預先認證，自動付款
     * @return $this
     * @throws LinePayException
     */
    public function preApproved()
    {
        if ($this->postData->isNotEmpty()) {
            $this->postData->put('payType', PayType::PRE_APPROVED);
        } else {
            throw new LinePayException('Empty post data');
        }
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

    public function reserve()
    {
        if ($this->postData->get('returnUrls')['confirmUrlType'] == ConfirmUrlType::WEB) {
            return redirect($this->response->paymentUrl->web);
        } else {
            return redirect($this->response->paymentUrl->app);
        }
    }
}
