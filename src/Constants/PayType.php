<?php
/**
 * Created by PhpStorm.
 * User: yihua
 * Date: 2018/11/28
 * Time: 下午 2:11
 */

namespace TsaiYiHua\LinePay\Constants;


class PayType
{
    const NORMAL = 'NORMAL';
    const PRE_APPROVED = 'PREAPPROVED';

    /**
     * @return \Illuminate\Support\Collection
     */
    static public function getConstantValues()
    {
        return collect([
            self::NORMAL,
            self::PRE_APPROVED
        ])->unique();
    }
}