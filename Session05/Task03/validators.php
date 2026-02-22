<?php

declare(strict_types=1);

// validate_required(): kötelező mező ellenőrzése, visszaad hibaüzenetet vagy null-t
function validate_required(string $value, string $msg): ?string
{
    return trim($value) === '' ? $msg : null;
}

// validate_email(): e-mail formátum ellenőrzése
function validate_email(string $value, string $msg): ?string
{
    if (trim($value) === '') return $msg;
    return filter_var($value, FILTER_VALIDATE_EMAIL) ? null : 'Az e-mail formátuma nem megfelelő.';
}

// validate_int_range(): egész szám tartomány ellenőrzése
function validate_int_range(string $raw, int $min, int $max, string $msg): ?string
{
    $v = filter_var($raw, FILTER_VALIDATE_INT);
    if ($raw === '' || $v === false) return $msg;
    return ($v < $min || $v > $max) ? $msg : null;
}
