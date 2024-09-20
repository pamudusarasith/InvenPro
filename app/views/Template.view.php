<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>

    <link rel="stylesheet" href="/css/styles.css">
    <link rel="stylesheet" href="/css/adminstyles.css">

</head>

<body>
<?php App\View::render($view, $data); ?>
<script src="/js/main.js"></script>
</body>

</html>