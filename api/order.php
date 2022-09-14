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

if (empty($_GET["product"])) {
	Response::json(true, 400, "Missing product GET-Parameter", true);
}

// instantiate database and server object
$database = new Database();
$db = $database->getConnection();

// get product
$query = "SELECT id, name, price, stock, available FROM products WHERE id=:id OR name=:name LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(":id", $_GET["product"]);
$stmt->bindParam(":name", $_GET["product"]);
$stmt->execute();

if ($stmt->rowCount() == 1) {		
	$stmt->bindColumn("id", $productID);
	$stmt->bindColumn("name", $productName);
	$stmt->bindColumn("price", $productPrice);
	$stmt->bindColumn("stock", $productStock);
	$stmt->bindColumn("available", $productAvailability);
	$stmt->fetch();
	
	if($productStock < 1){
		Response::json(true, 400, "Product is out of stock", true);
	}
	
	if($productAvailability == 0){
		Response::json(true, 400, "Product is unavailable", true);
	}
	
} else {
	Response::json(true, 400, "Unknown product", true);
}

$seat = $_GET["seat"];
$paid = 0;
$time = time() - 3600;

// close expired invoices
$newStatus = "expired";
$currentStatus = "active";

$query = "UPDATE invoices SET status=:newStatus WHERE paid=:paid AND status=:currentStatus AND time<:time";
$stmt = $db->prepare($query);
$stmt->bindParam(":newStatus", $newStatus);
$stmt->bindParam(":paid", $paid);
$stmt->bindParam(":currentStatus", $currentStatus);
$stmt->bindParam(":time", $time);

if ($stmt->execute()) {
	// expired orders successfully closed
} else {
    Response::json(true, 400, "Could not close expired invoices", true);
}

// create new invoice if needed
$status = "active";

$query = "SELECT id FROM invoices WHERE seat=:seat AND paid=:paid AND time>=:time AND status=:status ORDER BY id DESC LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(":seat", $seat);
$stmt->bindParam(":paid", $paid);
$stmt->bindParam(":time", $time);
$stmt->bindParam(":status", $status);
$stmt->execute();

$currentTime = time();

if ($stmt->rowCount() == 1) {		
	$stmt->bindColumn("id", $invoiceid);
	$stmt->fetch();
} else {
	$query = "INSERT INTO invoices (seat, status, time) VALUES (:seat, :status, :time)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":seat", $seat);
    $stmt->bindParam(":status", $status);
    $stmt->bindParam(":time", $currentTime);

    if ($stmt->execute()) {
        // new invoice created
		$invoiceid = $db->lastInsertId();
    } else {
        Response::json(true, 400, "Could not create a new invoice", true);
    }
}

// decrement product stock by 1
$newProductStock = $productStock - 1;
$query = "UPDATE products SET stock=:stock WHERE id=:id";
$stmt = $db->prepare($query);
$stmt->bindParam(":stock", $newProductStock);
$stmt->bindParam(":id", $productID);

if ($stmt->execute()) {
	// stock successfully decremented
} else {
    Response::json(true, 400, "Could not change product stock", true);
}
										
// add product to order
$query = "INSERT INTO orders (invoiceid, productid, price, time) VALUES (:invoiceid, :productid, :price, :time)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":invoiceid", $invoiceid);
    $stmt->bindParam(":productid", $productID);
	$stmt->bindParam(":price", $productPrice);
    $stmt->bindParam(":time", $currentTime);

    if ($stmt->execute()) {
		Response::json(false, 200, "Product successfully ordered", false);
    } else {
        Response::json(true, 400, "Could not add the ordered product to the invoice", true);
    }
?>