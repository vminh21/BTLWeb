<?php
session_start();
session_unset();
session_destroy();
// Chuyển về trang login
header("Location: ../index.php");
exit();
?>