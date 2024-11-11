<?php
/**
 * Created by PhpStorm.
 * User: yihua
 * Date: 2018/11/28
 * Time: 下午 1:56
 */

namespace TsaiYiHua\LinePay\Validations;


use Illuminate\Support\Facades\Validator;
use TsaiYiHua\LinePay\Constants\ConfirmUrlType;
use TsaiYiHua\LinePay\Constants\Currency;
use TsaiYiHua\LinePay\Constants\PageLanguage;
use TsaiYiHua\LinePay\Constants\PayType;

class ReserveValidation
{
    /**
     * @param $data
     * @return \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     */
    static public function validator($data)
    {
        $validator = Validator::make($data, [
            'productName' => 'required_without:packages|max:400',
            'productImageUrl' => 'url|max:500',
            'amount' => 'required_without:packages|int',
            'packages' => 'required_without:productName|array',
            'packages.*.amount' => 'required_with:packages|numeric|min:1',
            'packages.*.id' => 'required_with:packages|max:50',
            'packages.*.name' => 'max:100',
            'packages.*.products' =>'required_with:packages|array',
            'packages.*.products.*.name' => 'required_with:packages.*.products|max:4000',
            'packages.*.products.*.price' => 'required_with:packages.*.products|numeric|min:1',
            'packages.*.products.*.quantity' => 'required_with:packages.*.products|numeric|min:1',
            'packages.*.products.*.id' => 'max:50',
            'packages.*.products.*.imageUrl' => 'max:500',
            'packages.*.products.*.originalPrice' => 'numeric|min:1',
            'currency' => 'max:3|in:'.implode(',', Currency::getConstantValues()->toArray()),
            'mid' => 'max:50',
            'oneTimeKey' => 'max:12',
            'confirmUrl' => 'url|max:500',
            'confirmUrlType' => 'in:'.implode(',', ConfirmUrlType::getConstantValues()->toArray()),
            'checkConfirmUrlBrowser' => 'boolean',
            'cancelUrl' => 'url|max:500',
            'packageName' => 'max:4000',
            'orderId' => 'required|max:100',
            'deliveryPlacePhone' => 'max:100',
            'payType' => 'in:'.implode(',', PayType::getConstantValues()->toArray()),
            'langCd' => 'in:'.implode(',', PageLanguage::getConstantValues()->toArray()),
            'capture' => 'boolean'
        ]);
        return $validator;
    }
}