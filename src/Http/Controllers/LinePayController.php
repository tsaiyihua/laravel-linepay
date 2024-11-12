<?php
namespace TsaiYiHua\LinePay\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use TsaiYiHua\ECPay\Services\StringService;
use TsaiYiHua\LinePay\Capture;
use TsaiYiHua\LinePay\Confirm;
use TsaiYiHua\LinePay\Constants\Currency;

class LinePayController extends Controller
{
    protected $linePayConfirm;
    protected $linePayCapture;

    public function __construct()
    {
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \TsaiYiHua\LinePay\Exceptions\LinePayException
     */
    public function confirmUrl(Confirm $linePayConfirm, Request $request)
    {
        $this->linePayConfirm = $linePayConfirm;
        $transId = $request->get('transactionId');
        $orderId = $request->get('orderId');
        $sendData = [
            'transactionId' => $transId,
            'orderId' => $orderId
        ];
        return $this->linePayConfirm->setPostData($sendData)->chat()->confirm(function($response){
            if (!empty($response)) {
                // 處理完成付款後的動作
                dd($response);
            }
//            return redirect(route('line-pay-complete'));
        });
    }

    public function cancelUrl()
    {
        return response()->json(['message' => 'cancelled']);
    }
    /**
     * @param Capture $linePayCapture
     * @param Request $request
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \TsaiYiHua\LinePay\Exceptions\LinePayException
     */
    public function capture(Capture $linePayCapture, Request $request)
    {
        $this->linePayCapture = $linePayCapture;
        $transId = $request->get('transactionId');
        $sendData = [
            'transactionId' => $transId,
            'amount' => $request->get('amount')
        ];
        return $this->linePayCapture->setPostData($sendData)->chat()->capture(function($response){
            if (!empty($response)) {
                // 處理完成付款後的動作
                dd($response);
            }
//            return redirect(route('line-pay-complete'));
        });
    }
}