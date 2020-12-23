# factoring
APP:

- git clone https://github.com/aoprea90/factoring.git
- docker-compose up -d (case of error run sudo curl -L "https://github.com/docker/compose/releases/download/1.24.1/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose^C)
- docker-compose exec -e COLUMNS="`tput cols`" -e LINES="`tput lines`" factoring_app bash

Install composer

- php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
- php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
- php composer-setup.php
- php -r "unlink('composer-setup.php');"
- mv composer.phar /usr/local/bin/composer


- create env.local on project root
##DB CREDENTIALS
DATABASE_URL=mysql://root:123@mysql.local:3306/factoring?serverVersion=5.7

- composer install

- cd /etc/apache2/sites-available/
- cp /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/symfony.dev.conf
- overwrite symfony.dev.conf with:

 

        <VirtualHost *:80>
        # The ServerName directive sets the request scheme, hostname and port that
        # the server uses to identify itself. This is used when creating
        # redirection URLs. In the context of virtual hosts, the ServerName
        # specifies what hostname must appear in the request's Host: header to
        # match this virtual host. For the default virtual host (this file) this
        # value is not decisive as it is used as a last resort host regardless.
        # However, you must set it for any further virtual host explicitly.
        ServerName factoring.local
        ServerAdmin webmaster@localhost
        ServerAlias www.symfony.dev
        DocumentRoot /var/www/html/public
	DirectoryIndex index.php

        <Directory "/var/www/html/public">
                AllowOverride All
                Order Allow,Deny
                Allow from All

                FallbackResource /index.php
        </Directory>

        #<IfModule mod_rewrite.c>
        #        Options -MultiViews
        #        RewriteEngine On
        #        RewriteCond %{REQUEST_FILENAME} !-f
        #        RewriteRule ^(.*)$ index.php [QSA,L]
        #</IfModule>

        # Available loglevels: trace8, ..., trace1, debug, info, notice, warn,
        # error, crit, alert, emerg.
        # It is also possible to configure the loglevel for particular
        # modules, e.g.
        #LogLevel info ssl:warn

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined

        # For most configuration files from conf-available/, which are
        # enabled or disabled at a global level, it is possible to
        # include a line for only one particular virtual host. For example the
        # following line enables the CGI configuration for this host only
        # after it has been globally disabled with "a2disconf".
        #Include conf-available/serve-cgi-bin.conf
</VirtualHost>


- a2dissite 000-default.conf
- a2ensite symfony.dev.conf
- service apache2 reload


DB:

- docker-compose exec -e COLUMNS="`tput cols`" -e LINES="`tput lines`" factoring_db bash
- RUN apt-get update
- RUN apt-get install nano
- nano /etc/mysql/my.cnf
- ADD: 
  [mysqld]
	sql_mode = ''
- service mysql restart


APP:

- docker-compose exec -e COLUMNS="`tput cols`" -e LINES="`tput lines`" factoring_app bash
- cd /var/www/html
- bin/console doctrine:migrations:migrate
- bin/console doctrine:fixtures:load
- acces localhost:8001/invoices


