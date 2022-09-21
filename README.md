# DoRiE-Invoice

Apache 2<br>
PHP 8.1<br>
SQLite

## Install Apache + PHP

`apt update`
  
`apt install apache2`

`apt install ca-certificates apt-transport-https lsb-release gnupg curl nano unzip git`

`curl -fsSL https://packages.sury.org/php/apt.gpg -o /usr/share/keyrings/php-archive-keyring.gpg`

`echo "deb [signed-by=/usr/share/keyrings/php-archive-keyring.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list`

`apt update`

`apt install php8.1 php8.1-cli php8.1-common php8.1-curl php8.1-gd php8.1-intl php8.1-mbstring php8.1-opcache php8.1-readline php8.1-xml php8.1-xsl php8.1-zip php8.1-bz2 libapache2-mod-php8.1 php8.1-sqlite3`

`apt install sqlite3 sqlitebrowser`

`cd /var/www/html`

`git clone https://github.com/henry-hft/DoRiE-Invoice`

`mv DoRiE-Invoice/* .`

`php setup.php`

`service apache2 restart`


## Configuration

nano api/config/core.php
<br>
Ip address or domain of the server:
<br>
`$baseUrl = "http://127.0.0.1";`
