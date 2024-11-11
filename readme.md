# Laravel LinePay
Laravel LinePay 為串接Line Pay的非官方套件

## 前言
目前只有一般付款後自動請款的動作可以執行，建議只有在手機環境下才出現 LinePay 的付款方法。
- v1.x, v2.x 適用 LinePay V2 方法
- v3.x 適用 LinePay V3 方法

## 系統需求
- v2.x, v3.x
     - PHP >= 7.2
     - Laravel >= 6.0
     
- v1.x
     - PHP >= 7
     - Laravel >= 5.7 < 6.0
 
 ## 安裝
 ```composer require tsaiyihua/laravel-linepay```
 
 ## 環境設定
 ```php artisan vendor:publish --tag=linepay```  
 ### .env 裡加入
 ```
 LINE_PAY_CHANNEL_ID=
 LINE_PAY_CHANNEL_SECRET=
 ```
 #### 申請及測試流程說明網址
 ```https://developers-pay.line.me/zh/sandbox```
 
 ## 用法
 Line Pay 的付款流程一定要先準備(reserve)，之後再確認付款後(confirm)才能完成整個付款。
 Line pay v3 版本新增 price 及 quantity 為必要欄位。
 ### Reserve
 #### 單一產品購買
 ```php
<?php
namespace App\Http\Controllers;

use TsaiYiHua\LinePay\Constants\ConfirmUrlType;
use TsaiYiHua\LinePay\Reserve;
use TsaiYiHua\LinePay\Services\StringService;

class LinePayController extends Controller
{
    protected $linePayReserve;

    public function __construct(Reserve $linePayReserve)
    {
        $this->linePayReserve = $linePayReserve;
    }

    public function pay()
    {
        $orderId = StringService::identifyNumberGenerator('O');

        $postData = [
            'orderId' => $orderId,
            'productName' => 'Item Name',
            'productImageUrl' => 'https://tyh.idv.tw/images/tyhlogo.jpg',
            'price' => 50,
            'quantity' => 1,
            'amount' => 50,
            'confirmUrlType' => ConfirmUrlType::WEB
        ];
        return $this->linePayReserve->setPostData($postData)->chat()->reserve();
    }
}

```
### Confirm
Confirm 時會回傳 transactionId 以及 orderId
```php
$transId = $request->get('transactionId');
$orderId = $request->get('orderId');
$sendData = [
    'transactionId' => $transId,
    'orderId' => $orderId
];
return $this->linePayConfirm->setPostData($sendData)->chat()->confirm(function($response){
    if (!empty($response)) {
        // 處理完成付款後的動作
        ...
    }
});
```
 ## 已知問題
  - 當使用網頁登入或掃碼付款時，頁面會導回 confirmUrl 頁，但 Server-to-Server 也溝通了一次，所以會有兩次 Confirm Request。第二次的 Confirm Request 就會出現 1198 的要求處理中的錯誤，而前端網頁有時會先完成有時會慢於 Server-to-Server，於是就會有時成功有時失敗。
  - 測試時不能傳送 capture 為 false 時的狀態，會出現 ```LinePayException (2103) Parameter is not allowed. [capture:`false`]。```
