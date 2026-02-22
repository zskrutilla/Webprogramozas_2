<?php

declare(strict_types=1);

// htmlspecialchars(): HTML-escape (XSS megelőzés)
function h(string $v): string
{
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Egyszerű "flash" üzenet session-ben (PRG-hez)
function flash_set(string $msg): void
{
    $_SESSION['flash'] = $msg;
}
function flash_get(): ?string
{
    if (!isset($_SESSION['flash'])) return null;
    $m = (string)$_SESSION['flash'];
    unset($_SESSION['flash']);
    return $m;
}
