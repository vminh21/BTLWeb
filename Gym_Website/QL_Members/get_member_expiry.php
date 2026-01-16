<?php
$conn = new mysqli("localhost", "root", "", "GymManagement");
if (isset($_GET['member_id'])) {
    $id = intval($_GET['member_id']);
    $result = $conn->query("SELECT end_date FROM member_subscriptions WHERE member_id = $id LIMIT 1");
    if ($row = $result->fetch_assoc()) {
        echo json_encode(['end_date' => $row['end_date']]);
    } else {
        echo json_encode(['end_date' => null]);
    }
}
?>