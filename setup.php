<?php
$dbFile = "api/db/dorie.db";

// check if folder already exists
if (!file_exists("api/db")) {
	mkdir("api/db");
}

// check if database file already exists
if (file_exists($dbFile)) {
	unlink($dbFile);
}

$database = new SQLite3($dbFile);
chmod($dbFile, 0775);
chown($dbFile, "www-data:www-data");
		
$database->exec("CREATE TABLE `invoices` (
				`id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
				`seat` int(1) NOT NULL,
				`status` varchar(255) NOT NULL,
				`paid` int(1) NOT NULL DEFAULT 0,
				`time` varchar(31) NOT NULL
				);");
						
$database->exec("CREATE TABLE `orders` (
				`id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
				`invoiceid` int(11) NOT NULL,
				`productid` varchar(11) NOT NULL,
				`price` decimal(11,2) NOT NULL,
				`time` varchar(31) NOT NULL
				);");
		
$database->exec("CREATE TABLE `products` (
				`id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
				`name` varchar(255) NOT NULL,
				`description` varchar(255) NOT NULL,
				`price` decimal(11,2) NOT NULL,
				`stock` int(11) NOT NULL,
				`available` int(1) NOT NULL DEFAULT 1
				);");			

$database->exec("CREATE TABLE `events` (
				`id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
				`image` varchar(255) NOT NULL,
				`text` varchar(255) NOT NULL,
				`duration` varchar(11) NOT NULL
  				);");

$database->exec("INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`, `available`) VALUES
				(1, 'Drink', 'Heineken 0,33l', '3.40', 100, 1),
				(2, 'Snack', 'Popcorn 100g', '2.50', 100, 1);");
				
$database->close();

echo "DoRiE\n";
echo "---------------------------------\n";					
echo "Database successfully created\n";
echo "---------------------------------\n";	
?>