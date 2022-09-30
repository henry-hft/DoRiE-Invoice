# DoRiE-Invoice

Apache 2<br>
PHP 8.1<br>
SQLite

## Install Apache + PHP

`sudo apt update`
  
`sudo apt install apache2`

`sudo apt install ca-certificates apt-transport-https lsb-release gnupg curl nano unzip git`

`sudo apt install software-properties-common -y`

`sudo add-apt-repository ppa:ondrej/php`

`sudo apt update`

`sudo apt install php8.1 php8.1-cli php8.1-common php8.1-curl php8.1-gd php8.1-intl php8.1-mbstring php8.1-opcache php8.1-readline php8.1-xml php8.1-xsl php8.1-zip php8.1-bz2 libapache2-mod-php8.1 php8.1-sqlite3`

`sudo apt install sqlite3 sqlitebrowser`

`sudo cd /var/www/html`

`sudo git clone https://github.com/henry-hft/DoRiE-Invoice`

`sudo mv DoRiE-Invoice/* .`

`sudo php setup.php`

`sudo service apache2 restart`


## Configuration

nano api/config/core.php
<br>
Ip address or domain of the server:
<br>
`$baseUrl = "http://127.0.0.1";`

## API Documentation

## Endpoints

- order.php - Order a product
- request.php - Get an invoice
- reset.php - Close all active invoices

## Examples

`http://localhost/api/order.php?seat=1&product=Drink`
<br>
`http://localhost/api/order.php?seat=1product=2`
<br>
`http://localhost/api/request.php?seat=1`
<br>
`http://localhost/api/request.php?seat=2`
<br>
`http://localhost/api/reset.php`
