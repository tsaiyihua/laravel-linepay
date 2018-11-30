<?php
namespace TsaiYiHua\LinePay\Constants;

class Currency
{
    const USD = 'USD';
    const JPY = 'JPY';
    const TWD = 'TWD';
    const THB = 'THB';

    /**
     * @return \Illuminate\Support\Collection
     */
    static public function getConstantValues()
    {
        return collect([
            self::USD,
            self::JPY,
            self::TWD,
            self::TWD
        ])->unique();
    }
}