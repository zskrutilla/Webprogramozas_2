<?php

declare(strict_types=1);
/** @var string $title */
?>
<!doctype html>
<html lang="hu">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo h($title); ?></title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <h1><?php echo h($title); ?></h1>
  </header>