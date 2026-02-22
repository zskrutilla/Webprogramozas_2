<?php

declare(strict_types=1);
require_once __DIR__ . '/lib.php';
require_once __DIR__ . '/controller.php';

$page = render_products_page();

// Layout betöltése a view-ból
$title = (string)$page['title'];
$content = (string)$page['content'];
include __DIR__ . '/views/layout.php';
