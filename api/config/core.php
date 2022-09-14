<?php
// enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Base URL
$baseUrl = "http://116.203.93.23";

// Number of available seats
$availableSeats = 2;

// required headers
header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
?>