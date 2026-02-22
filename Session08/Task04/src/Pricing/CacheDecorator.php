<?php

declare(strict_types=1);

namespace App\Pricing;

/**
 * Decorator: egyszerű fájl cache.
 */
final class CacheDecorator implements PriceProvider
{
    private int $hits = 0;
    private int $misses = 0;

    public function __construct(private PriceProvider $inner, private string $cacheFile) {}

    public function name(): string
    {
        return $this->inner->name() . ' + Cache';
    }

    public function getPriceFt(string $productId): int
    {
        $cache = $this->readCache();
        if (array_key_exists($productId, $cache)) {
            $this->hits++;
            return (int)$cache[$productId];
        }

        $this->misses++;
        $price = $this->inner->getPriceFt($productId);
        $cache[$productId] = $price;
        $this->writeCache($cache);
        return $price;
    }

    /** @return array<string,int> */
    private function readCache(): array
    {
        $raw = is_file($this->cacheFile) ? file_get_contents($this->cacheFile) : '{}';
        $arr = json_decode((string)$raw, true);
        return is_array($arr) ? $arr : [];
    }

    /** @param array<string,int> $arr */
    private function writeCache(array $arr): void
    {
        file_put_contents($this->cacheFile, (string)json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
    }

    public function stats(): array
    {
        return ['hits' => $this->hits, 'misses' => $this->misses];
    }
}
