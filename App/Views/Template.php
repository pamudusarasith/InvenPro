<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? "Invenpro" ?></title>

    <link rel="stylesheet" href="/css/styles.css">

    <?php if (isset($stylesheets)) {
        foreach ($stylesheets as $filename): ?>
            <link rel="stylesheet" href="/css/<?= $filename ?>.css">
    <?php endforeach;
    } ?>

</head>

<body>

    <?php if (isset($view)) {
        App\Core\View::render($view, $data ?? []);
    } ?>

    <?php if (isset($scripts)) {
        foreach ($scripts as $filename): ?>
            <script src="/js/<?= $filename ?>.js"></script>
    <?php endforeach;
    } ?>
    <script src="/js/main.js"></script>

</body>

</html>