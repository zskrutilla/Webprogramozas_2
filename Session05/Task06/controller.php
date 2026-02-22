<?php

declare(strict_types=1);

require_once __DIR__ . '/lib.php';

// handle_contact_form(): feldolgozza a POST adatokat, visszaad (data, errors) tömböt
function handle_contact_form(array $post): array
{
    $name = trim((string)($post['name'] ?? ''));
    $email = trim((string)($post['email'] ?? ''));
    $msg = trim((string)($post['msg'] ?? ''));

    $errors = [];
    if ($name === '') $errors['name'] = 'Kérem, adja meg a nevét.';
    if ($email === '') $errors['email'] = 'Kérem, adja meg az e-mail címét.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Az e-mail formátuma nem megfelelő.';
    if ($msg === '') $errors['msg'] = 'Kérem, írjon üzenetet.';
    elseif (mb_strlen($msg, 'UTF-8') > 500) $errors['msg'] = 'Az üzenet maximum 500 karakter lehet.';

    $data = ['name' => $name, 'email' => $email, 'msg' => $msg];
    return [$data, $errors];
}
