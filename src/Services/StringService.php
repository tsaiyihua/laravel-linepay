<?php
namespace TsaiYiHua\LinePay\Services;

use TsaiYiHua\LinePay\Exceptions\LinePayException;

class StringService
{
    /**
     * Identify Number Generator
     * @return string
     * @throws
     */
    static public function identifyNumberGenerator($prefix='A')
    {
        if (strlen($prefix) > 2) {
            throw new LinePayException('ID prefix character maximum is 2 characters');
        }
        $intMsConst = 1000000;
        try {
            list($ms, $timestamp) = explode(" ", microtime());
            $msString = (string) substr('000000'.($ms*$intMsConst), -6);
            return $prefix . $timestamp . $msString . substr('00'.random_int(0, 99),-2);
        } catch (\Exception $e) {
            return $prefix . $timestamp . $msString . '00';
        }
    }
}