<?php
namespace TsaiYiHua\LinePay\Services;

use Illuminate\Support\Facades\Cache;
use TsaiYiHua\LinePay\Exceptions\LinePayException;

class CacheService
{
    static protected $cacheKeyPrefix = 'linepay_';
    protected $cacheKey;
    protected $aliveTime = 86400;

    public function setKey($id)
    {
        $this->cacheKey = self::$cacheKeyPrefix.$id;
        return $this;
    }

    public function setAliveTime($seconds)
    {
        $this->aliveTime = $seconds;
    }

    /**
     * @param $data
     * @throws LinePayException
     */
    public function setData($data)
    {
        if (empty($this->cacheKey)) {
            throw new LinePayException('Cache key must be set first');
        }
        Cache::put($this->cacheKey, $data, now()->addSeconds($this->aliveTime));
    }

    /**
     * @return mixed
     * @throws LinePayException
     */
    public function getData()
    {
        if (empty($this->cacheKey)) {
            throw new LinePayException('Cache key must be set first');
        }
        return Cache::get($this->cacheKey);
    }
}