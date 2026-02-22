<?php

declare(strict_types=1);

namespace App\Pricing;

/**
 * Alap provider: JSON katalógusból olvas.
 */
final class JsonCatalogPriceProvider implements PriceProvider
{
    public function __construct(private string $file) {}

    public function name(): string
    {
        return 'Katalógus (JSON)';
    }

    public function getPriceFt(string $productId): int
    {
        $raw = is_file($this->file) ? file_get_contents($this->file) : '[]';
        $arr = json_decode((string)$raw, true);
        if (!is_array($arr)) $arr = [];
        foreach ($arr as $row) {
            if ((string)($row['id'] ?? '') === $productId) {
                return max(0, (int)($row['price_ft'] ?? 0));
            }
        }
        throw new \RuntimeException('Ismeretlen termék ID.');
    }
}
