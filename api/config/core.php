<?php
// enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Base URL
$baseUrl = "http://116.203.93.23";

// Number of available seats
$availableSeats = 2;

// Max. invoice duration
$invoiceDuration = 3600; // in seconds

// Order event notification duration
$orderEvent = 5; // in seconds

// Pay event notification duration
$payEvent = 5; // in seconds

// Cancel event notification duration
$cancelEvent = 5; // in seconds

// QR-Code event notification duration
$qrCodeEvent = 30; // in seconds

// required headers
header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
?>