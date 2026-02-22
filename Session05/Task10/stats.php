<?php

declare(strict_types=1);

// generate_series(): sorozat generálása (pl. 1..N)
function generate_series(int $n): array
{
    $arr = [];
    for ($i = 1; $i <= $n; $i++) $arr[] = $i;
    return $arr;
}

// compute_row(): egy sor kiszámítása (i, i^2, i^3, sqrt(i))
function compute_row(int $i): array
{
    return [
        'i' => $i,
        'i2' => $i * $i,
        'i3' => $i * $i * $i,
        'sqrt' => sqrt((float)$i),
    ];
}

// build_table_data(): tömbbe gyűjti a számolt sorokat
function build_table_data(int $n): array
{
    $rows = [];
    foreach (generate_series($n) as $i) {
        $rows[] = compute_row((int)$i);
    }
    return $rows;
}

// summarize(): összegzés (átlagok)
function summarize(array $rows): array
{
    $cnt = count($rows);
    if ($cnt === 0) return ['avg_i2' => 0, 'avg_i3' => 0];
    $sum2 = 0;
    $sum3 = 0;
    foreach ($rows as $r) {
        $sum2 += (int)$r['i2'];
        $sum3 += (int)$r['i3'];
    }
    return ['avg_i2' => $sum2 / $cnt, 'avg_i3' => $sum3 / $cnt];
}
