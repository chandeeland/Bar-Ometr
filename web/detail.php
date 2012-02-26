<?php
session_start();
if (array_key_exists('pics', $_SESSION) && array_key_exists('i', $_REQUEST)) {
    $pics = $_SESSION['pics'];
    $i = $_REQUEST['i'];
} else {
    header('Location: /report.php');
}
?>

<img src="<?= $pics[$i]; ?>">
