<?php
include_once "./config/core.php";
include_once "./config/database.php";

include_once "./objects/Response.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
	Response::json(true, 400, "Invalid request method", true);
}

if (!isset($_GET["text"]) AND !isset($_GET["image"])) {
	Response::json(true, 400, "Missing text/image GET-Parameter", true);
}

if (!empty($_GET["duration"])){
	if (!is_numeric($_GET["duration"])) {
		Response::json(true, 400, "Invalid input for durtation", true);
	}
} else {
	$duration = -1;
}

// instantiate database and server object
$database = new Database();
$db = $database->getConnection();

$text = empty($_GET["text"]) ? '' : $_GET["text"];
$image = empty($_GET["image"]) ? '' : "images/" . $_GET["image"];

// check if image exists
if($filename != ""){
	if (!file_exists($filename)) {
		Response::json(true, 400, "Image does not exist", true);
	}
}


if(!isset($duration)){
	$duration = $_GET["duration"];
}

// add event to database

$query = "INSERT INTO events (image, text, duration) VALUES (:image, :text, :duration)";
$stmt = $db->prepare($query);
$stmt->bindParam(":image", $image);
$stmt->bindParam(":text", $text);
$stmt->bindParam(":duration", $duration);

if ($stmt->execute()) {
    Response::json(false, 200, "Event successfully created", false);
} else {
    Response::json(true, 400, "Could not create a new event", true);
}
?>
