<?php
include_once "./config/core.php";
include_once "./config/database.php";

include_once "./objects/Response.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
	Response::json(true, 400, "Invalid request method", true);
}

// instantiate database and server object
$database = new Database();
$db = $database->getConnection();

$paid = 0;
$time = time() - 3600;
$status = "ready";

// get latest invoice
$query = "SELECT id FROM invoices WHERE paid=:paid AND time>=:time AND status=:status ORDER BY id DESC LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(":paid", $paid);
$stmt->bindParam(":time", $time);
$stmt->bindParam(":status", $status);
$stmt->execute();

if ($stmt->rowCount() == 1) {		
	$stmt->bindColumn("id", $invoiceID);
	$stmt->fetch();
	
	$url = urlencode("$baseUrl/invoice.html?id=$invoiceID");
	$response = ["error" => false, "id" => (int) $invoiceID, "qrcode" => "$baseUrl/qrcode.php?url=$url"];
} else {
	Response::json(true, 400, "No active invoice found", true);
}

// update invoice

$newStatus = "completed";

$query = "UPDATE invoices SET status=:newStatus WHERE id=:id";
$stmt = $db->prepare($query);
$stmt->bindParam(":newStatus", $newStatus);
$stmt->bindParam(":id", $invoiceID);

if ($stmt->execute()) {
	// order status successfully set to completed
} else {
    Response::json(true, 400, "Could not set invoice status to completed", true);
}

echo json_encode($response);
?>