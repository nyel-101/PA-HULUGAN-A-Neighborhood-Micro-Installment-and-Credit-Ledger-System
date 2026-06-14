<?php

header("Content-Type: application/json");


include '../config.php'; 
check_role('lender');



$sql = "SELECT id, full_name, account_status FROM users WHERE account_status='pending'";
$res = mysqli_query($conn, $sql);

$data = array();

if ($res) {
    while($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }
    
    echo json_encode(["status" => "success", "users" => $data]);
} else {
    
    echo json_encode(["status" => "error", "message" => "Query failed"]);
}
?>