<?php
/**
 * Created by PhpStorm.
 * User: yihua
 * Date: 2018/11/28
 * Time: 下午 4:23
 */

namespace TsaiYiHua\LinePay;

use TsaiYiHua\LinePay\Exceptions\LinePayException;

class Refund extends LinePayAbstract
{
    use LinePayTrait;

    protected $requestUri = '/v3/payments/{transactionId}/refund';

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
        $this->postData->put('refundAmount', $data['refundAmount']);
        return $this;
    }

    /**
     * @return mixed
     * @throws LinePayException
     */
    public function refund()
    {
        if (empty($this->response)) {
            throw new LinePayException('No response from line pay');
        }
        return $this->response;
    }
}