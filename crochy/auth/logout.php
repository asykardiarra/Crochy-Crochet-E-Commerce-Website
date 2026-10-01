<?php
require "../config.php";

unset($_SESSION['user_id'], $_SESSION['user_nama'], $_SESSION['user_role'], $_SESSION['user_whatsapp'], $_SESSION['user_alamat']);

header("Location: ../index.php");
exit;
