<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
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
        App\View::render($view, $data ?? []);
    } ?>

    <script src="/js/main.js"></script>

    <?php if (isset($scripts)) {
        foreach ($scripts as $filename): ?>
            <script src="/js/<?= $filename ?>.js">
        <?php endforeach;
    } ?>

</body>
</html>