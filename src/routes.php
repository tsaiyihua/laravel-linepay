<?php

Route::prefix('linepay')->group(function(){
    Route::get('confirm', 'TsaiYiHua\LinePay\Http\Controllers\LinePayController@confirmUrl')
        ->name('linepay.confirm');
    Route::get('capture', 'TsaiYiHua\LinePay\Http\Controllers\LinePayController@capture')
        ->name('linepay.capture');
});