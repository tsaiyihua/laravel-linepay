<?php
/**
 * Created by PhpStorm.
 * User: yihua
 * Date: 2018/11/28
 * Time: 下午 2:04
 */

namespace TsaiYiHua\LinePay\Constants;


class ConfirmUrlType
{
    const MOBILE = 'CLIENT';
    const WEB = 'SERVER';

    /**
     * @return \Illuminate\Support\Collection
     */
    static public function getConstantValues()
    {
        return collect([
            self::MOBILE,
            self::WEB
        ])->unique();
    }
}