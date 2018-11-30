<?php
/**
 * Created by PhpStorm.
 * User: yihua
 * Date: 2018/11/28
 * Time: 下午 2:14
 */

namespace TsaiYiHua\LinePay\Constants;


class PageLanguage
{
    const JAPAN = 'ja';
    const KOREA = 'ko';
    const ENGLISH = 'en';
    const SIMPLY_CHINESE = 'zh-Hans';
    const TRADITIONAL_CHINESE = 'zh-Hant';
    const THAILAND = 'th';

    /**
     * @return \Illuminate\Support\Collection
     */
    static public function getConstantValues()
    {
        return collect([
            self::JAPAN,
            self::KOREA,
            self::ENGLISH,
            self::SIMPLY_CHINESE,
            self::TRADITIONAL_CHINESE,
            self::THAILAND,
        ])->unique();
    }
}