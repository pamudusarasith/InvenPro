<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? "Invenpro" ?></title>

    <link rel="stylesheet" href="/css/main.css">

    <?php if (isset($stylesheets)) {
        foreach ($stylesheets as $filename): ?>
            <link rel="stylesheet" href="/css/<?= $filename ?>.css">
    <?php endforeach;
    } ?>

</head>

<?php
$message = $_SESSION['message'] ?? null;
$messageType = $_SESSION['message_type'] ?? 'error';
switch ($messageType) {
    case 'success':
        $popupIcon = 'check_circle';
        break;
    case 'warning':
        $popupIcon = 'warning';
        break;
    default:
        $popupIcon = 'error';
}
unset($_SESSION['message'], $_SESSION['message_type']);
?>

<body>

    <?php if (isset($view)) {
        App\Core\View::render($view, $data ?? []);
    } ?>

    <div id="messagePopup" class="popup <?= $messageType ?>">
        <span class="icon"><?= $popupIcon ?></span>
        <span class="popup-message"><?= htmlspecialchars($message ?? "") ?></span>
        <button class="popup-close" onclick="closePopup()">
            <span class="icon">close</span>
        </button>
    </div>

    <?php if (isset($scripts)) {
        foreach ($scripts as $filename): ?>
            <script src="/js/<?= $filename ?>.js"></script>
    <?php endforeach;
    } ?>
    <script src="/js/main.js"></script>

</body>

</html>
