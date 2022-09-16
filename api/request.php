<?php
include_once "./config/core.php";
include_once "./config/database.php";

include_once "./objects/Response.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
	Response::json(true, 400, "Invalid request method", true);
}

if (empty($_GET["seat"])) {
	Response::json(true, 400, "Missing seat GET-Parameter", true);
}

if (!is_numeric($_GET["seat"])) {
	Response::json(true, 400, "Invalid input for seat", true);
}

if ($_GET["seat"] > $availableSeats OR $_GET["seat"] < 1) {
	Response::json(true, 400, "Invalid seat number", true);
}

// instantiate database and server object
$database = new Database();
$db = $database->getConnection();

$seat = $_GET["seat"];
$paid = 0;
$time = time() - 3600;
$status = "active";

// get latest invoice
$query = "SELECT id FROM invoices WHERE seat=:seat AND paid=:paid AND time>=:time AND status=:status ORDER BY id DESC LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(":seat", $seat);
$stmt->bindParam(":paid", $paid);
$stmt->bindParam(":time", $time);
$stmt->bindParam(":status", $status);
$stmt->execute();

$stmt->bindColumn("id", $invoiceId);
$invoiceIdFormatted = sprintf("%04d", $invoiceId);

if ($stmt->fetch()) {
	$url = urlencode("$baseUrl/invoice.html?id=$invoiceId");
	$response = ["error" => false, "qrcode" => "$baseUrl/qrcode.php?url=$url"];
} else {
	Response::json(true, 400, "No active invoice found for seat $seat", true);
}

// update invoice

$newStatus = "completed";

$query = "UPDATE invoices SET status=:newStatus WHERE id=:id";
$stmt = $db->prepare($query);
$stmt->bindParam(":newStatus", $newStatus);
$stmt->bindParam(":id", $invoiceId);

if ($stmt->execute()) {
	// order status successfully set to completed
} else {
    Response::json(true, 400, "Could not set invoice status to completed", true);
}

// add event
$image = "$baseUrl/qrcode.php?url=$url";
$text = "Seat: $seat | Invoice: #$invoiceIdFormatted";

$query = "INSERT INTO events (image, text, duration) VALUES (:image, :text, :duration)";
$stmt = $db->prepare($query);
$stmt->bindParam(":image", $image);
$stmt->bindParam(":text", $text);
$stmt->bindParam(":duration", $qrCodeEvent);

if ($stmt->execute()) {
    // new event created
} else {
    Response::json(true, 400, "Could not create a new event", true);
}

echo json_encode($response);
?>