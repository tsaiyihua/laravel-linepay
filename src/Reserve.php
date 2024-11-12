<?php
namespace TsaiYiHua\LinePay;

use Illuminate\Support\Str;
use TsaiYiHua\LinePay\Collections\RequestCollection;
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
        $confirmUrl = route('linepay.confirm');
        $cancelUrl = route('linepay.cancel');
        if (isset($data['productName'])) {
            $packageAmount = $data['price'] * $data['quantity'];
            $this->postData->put('packages', [
                'id' => Str::uuid()->toString(),
                'amount' => $packageAmount,
                'products' => array([
                    'name' => $data['productName'],
                    'quantity' => $data['quantity'],
                    'price' => $data['price'],
                    'imageUrl' => $data['productImageUrl']
                ])
            ]);
        } else {
            $amount = 0;
            foreach ($data['packages'] as $i => $package) {
                $productAmount = 0;
                $packageAmount = 0;
                foreach ($package['products'] as $product) {
                    $productAmount += $product['price'] * $product['quantity'];
                }
                $userFee = (isset($package['userFee'])) ? $package['userFee'] : 0;
                $packageAmount += $productAmount + $userFee;
                $amount += $packageAmount;
                $data['packages'][$i]['amount'] = $packageAmount;
            }
        }
        $data['amount'] = $amount;
        $this->cacheSrv->setKey($orderId)->setData($data);
        $this->postData->put('packages', $data['packages']);
        $this->postData->put('amount', $amount);
        $this->postData->put('currency', $data['currency'] ?? Currency::TWD);
        $this->postData->put('redirectUrls', [
            'confirmUrl' => $data['confirmUrl'] ?? $confirmUrl,
            'cancelUrl' => $data['cancelUrl'] ?? $cancelUrl,
            'confirmUrlType' => $data['confirmUrlType'] ?? ConfirmUrlType::WEB
        ]);
        $this->postData->put('orderId', $data['orderId'] ?? StringService::identifyNumberGenerator('O'));
        /**
         * Optional fields
         */
        $optionParams = [
            /** 請求付款時的畫面訊息 */
            'display' => [
                'checkConfirmUrlBrowser',
                'locale'
            ],
            /** 合作商店附加訊息 */
            'extra' => [
                'branchId',
                'branchName',
                /** 點數限制資訊 TW only */
                'promotionRestriction' => [
                    'rewardLimit',
                    'useLimit'
                ]
            ],
            'familyService' => [
                'addFriends' => array([
                    'idList' => array([]),
                    'type'
                ])
            ],
            'payment' => [
                'capture',
                'payType'
            ],
            /** 配送訊息JP only */
            'shipping' => [
                'address' => [
                    'city',
                    'country',
                    'detail',
                    'optional',
                    'postalCode',
                    'recipient' => [
                        'email',
                        'firstName',
                        'firstNameOptional',
                        'lastName',
                        'lastNameOptional',
                        'phoneNo',
                    ],
                    'state'
                ],
                'feeAmount',
                'feeInquiryType',
                'feeInquiryUrl',
                'type'
            ]
        ];
        foreach ($optionParams as $key => $optionSubParams) {
            if ($key == 'extra') {
                foreach ($optionSubParams as $key2 => $optionSubParam) {
                    if ($key2 == 'promotionRestriction') {
                        foreach ($optionSubParam as $param) {
                            if (isset($data['options'][$key][$key2][$param])) {
                                $options = $data['options'][$key][$key2][$param];
                            }
                        }
                    } else {
                        if (isset($data['options'][$key][$optionSubParam])) {
                            $options = $data['options'][$key][$optionSubParam];
                        }
                    }
                }
            } else {
                foreach ($optionSubParams as $optionSubParam) {
                    if (isset($data['options'][$key][$optionSubParam])) {
                        $options[$key][$optionSubParam] = $data['options'][$key][$optionSubParam];
                    }
                }
            }
        }
        if (!empty($options))   $this->postData->put('options', $options);
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
        if ($this->postData->get('redirectUrls')['confirmUrlType'] == ConfirmUrlType::WEB) {
            return redirect($this->response->paymentUrl->web);
        } else {
            return redirect($this->response->paymentUrl->app);
        }
    }
}
