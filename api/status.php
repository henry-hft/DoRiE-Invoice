<?php
include_once "./config/core.php";
include_once "./config/database.php";

include_once "./objects/Response.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
	Response::json(true, 400, "Invalid request method", true);
}

if (empty($_GET["id"])) {
	Response::json(true, 400, "Missing id GET-Parameter", true);
}

if (!is_numeric($_GET["id"])) {
	Response::json(true, 400, "Invalid input for id", true);
}

if (empty($_GET["function"])) {
	Response::json(true, 400, "Missing function GET-Parameter", true);
}

if($_GET["function"] != "pay" AND $_GET["function"] != "cancel" AND $_GET["function"] != "check"){
	Response::json(true, 400, "Invalid/unknown function", true);
}

// instantiate database and server object
$database = new Database();
$db = $database->getConnection();

$invoiceID = $_GET["id"];
$query = "SELECT paid, status FROM invoices WHERE id=:id LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(":id", $invoiceID);
$stmt->execute();

if ($stmt->rowCount() == 1) {
	$stmt->bindColumn("paid", $paid);
	$stmt->bindColumn("status", $status);
	$stmt->fetch();
} else {
	Response::json(true, 400, "Invoice not found", true);
}

if($_GET["function"] == "pay"){
	
	if($paid == 1){
		Response::json(true, 400, "Invoice already paid", true);
	}
	
	if($status == "canceled"){
		Response::json(true, 400, "Invoice already canceled", true);
	}
	
	$paid = 1;

	$query = "UPDATE invoices SET paid=:paid WHERE id=:id";
	$stmt = $db->prepare($query);
	$stmt->bindParam(":paid", $paid);
	$stmt->bindParam(":id", $invoiceID);

	if ($stmt->execute()) {
		Response::json(false, 200, "Invoice successfully paid", false);
	} else {
		Response::json(true, 400, "The invoice could not be paid", true);
	}
}

if($_GET["function"] == "cancel"){
	
	if($paid == 1){
		Response::json(true, 400, "Invoice already paid", true);
	}
	
	if($status == "canceled"){
		Response::json(true, 400, "Invoice already canceled", true);
	}
	
	$newStatus = "canceled";

	$query = "UPDATE invoices SET status=:newStatus WHERE id=:id";
	$stmt = $db->prepare($query);
	$stmt->bindParam(":newStatus", $newStatus);
	$stmt->bindParam(":id", $invoiceID);

	if ($stmt->execute()) {
		Response::json(false, 200, "Invoice successfully canceled", false);
	} else {
		Response::json(true, 400, "The invoice could not be canceled", true);
	}
}

if($_GET["function"] == "check"){
	
	if($status == "canceled"){
		$canceled = 1;
	} else {
		$canceled = 0;
	}
	
	$response = ["error" => false, "paid" => (int) $paid, "canceled" => $canceled];
	echo json_encode($response);
}
?>