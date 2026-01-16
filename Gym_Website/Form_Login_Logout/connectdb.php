<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "GymManagement";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối Database thất bại: " . $conn->connect_error);
}
$conn->set_charset("utf8");
?>