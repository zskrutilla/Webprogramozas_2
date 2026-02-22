<?php

declare(strict_types=1);

// JSON "tároló" modul: load/save + append
function storage_load(string $file): array
{
    if (!is_file($file)) return [];
    $raw = file_get_contents($file);
    $data = json_decode((string)$raw, true);
    return is_array($data) ? $data : [];
}

function storage_save(string $file, array $data): void
{
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($file, (string)$json, LOCK_EX);
}

function storage_append(string $file, array $row): void
{
    $data = storage_load($file);
    $data[] = $row;
    storage_save($file, $data);
}
