<?php
/**
 * Created by PhpStorm.
 * User: yihua
 * Date: 2018/11/28
 * Time: 下午 1:38
 */

namespace TsaiYiHua\LinePay;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use TsaiYiHua\LinePay\Exceptions\LinePayException;

trait LinePayTrait
{
    protected $apiUrl;
    protected $headers;
    /** @var Collection */
    protected $postData;

    protected $response;

    /**
     * @return $this;
     */
    public function setHeaders()
    {
        $deviceType = '';
        $this->headers = [
            'Content-Type' => 'application/json',
            'X-LINE-ChannelId' =>  config('linepay.channel-id'),
            'X-LINE-ChannelSecret' => config('linepay.channel-secret'),
        ];
        if (!empty($deviceType)) {
            $this->headers['X-LINE-MerchantDeviceType'] = $deviceType;
        }
        return $this;
    }

    /**
     * Send data to LinePay
     * @return $this
     * @throws LinePayException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function chat()
    {
        $this->postData = $this->postData->filter(function($data){
            return !($data==='');
        });
        $this->setHeaders();
        $client = new Client([
            'base_uri' => $this->apiUrl
        ]);
        $response = $client->request($this->requestMethod, $this->requestUri, [
            'headers' => $this->headers,
            'json' => $this->postData->all()
        ]);
        $res = $this->parseResponse((string)$response->getBody());
        /**
         * 當使用網頁登入或掃碼付款時，頁面會導回 confirmUrl 頁，但 Server-to-Server 也溝通了一次，所以會有兩次 Confirm Request。
         * 第二次的 Confirm Request 就會出現 1198 的要求處理中的錯誤，而前端網頁有時會先完成有時會慢於 Server-to-Server，於是就會
         * 有時成功有時失敗。。。。。真的是很鳥的設計。。囧。
         */
        if ($res->returnCode == '0000' || $res->returnCode == '1198') {
            if ($res->returnCode == '0000') {
                $this->response = $res->info;
            }
        } else {
            throw new LinePayException($res->returnMessage, $res->returnCode);
        }
        return $this;
    }

    protected function parseResponse($response)
    {
        $response = json_decode($response);
        return $response;
    }
}