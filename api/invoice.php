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

// instantiate database and server object
$database = new Database();
$db = $database->getConnection();

// get invoice
$invoiceID = $_GET["id"];

$query = "SELECT seat, status, paid, time FROM invoices WHERE id=:id LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(":id", $invoiceID);
$stmt->execute();
		
$stmt->bindColumn("seat", $seat);
$stmt->bindColumn("status", $status);
$stmt->bindColumn("paid", $paid);
$stmt->bindColumn("time", $time);

if ($stmt->fetch()) {
	$newTime = date("Y-m-d H:i:s", date($time));
	$formattedInvoiceID = sprintf('%04d', $_GET["id"]); // Add leading zeros: 0001 (4 digits)
	
	$response = ["error" => false, "id" => $formattedInvoiceID, "seat" => $seat, "status" => $status, "paid" => $paid, "time" => $newTime, "total" => 0];
} else {
	Response::json(true, 400, "Invoice not found", true);
}

// get products
$query = "SELECT id, name, description FROM products";
//$query = "SELECT id, name, description, price FROM products";
$stmt = $db->prepare($query);
$stmt->execute();
		

// initialise an array for the results
$products = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$products[] = $row;
}

if (count($products) > 0) {
	Response::json(true, 400, "No products found", true);
}

// get ordered products
$query = "SELECT productid, price, time FROM orders WHERE invoiceid=:id";
$stmt = $db->prepare($query);
$stmt->bindParam(":id", $invoiceID);
$stmt->execute();

$items = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$key = array_search($row["productid"], array_column($products, 'id'));
	$newTime = date("Y-m-d H:i:s", date($row["time"]));
	$response["total"] += $row["price"];
	if ($key !== false) { // product  found
		$items[] = ["id" => $row["productid"], "price" => $row["price"], "name" => $products[$key]["name"], "description" => $products[$key]["description"], "time" => $newTime];
	} else { // product not found
		$items[] = ["id" => "0", "price" => $row["price"], "name" => "Unknown", "description" => "", "time" => $newTime];
	}
}
$response["items"] = $items;
	
if (count($items) > 0) {
	Response::json(true, 400, "The order is empty", true);
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

// add two decimal places
$response["total"] = number_format((float)$response["total"], 2, '.', '');

echo json_encode($response);
?>
