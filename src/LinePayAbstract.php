<?php
namespace TsaiYiHua\LinePay;

use Illuminate\Support\Collection;

abstract class LinePayAbstract
{
    /** @var string  */
    protected $apiUrl;
    /** @var string */
    protected $requestUri;
    /** @var string */
    protected $requestMethod;
    /** @var Collection  */
    protected $postData;

    public function __construct()
    {
        if (config('app.env') == 'production') {
            $this->apiUrl = 'https://api-pay.line.me';
        } else {
            $this->apiUrl = 'https://sandbox-api-pay.line.me';
        }
        $this->postData = new Collection();
    }
}