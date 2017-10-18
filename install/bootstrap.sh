#!/usr/bin/env bash

# Use single quotes instead of double quotes to make it work with special-character passwords
MYSQL_PASSWORD='98ubfhru4jgu89dS'
PROJECT_PATH='/var/www/html'

# update / upgrade
sudo apt-get update
sudo apt-get upgrade -y

# install apache 2.5 and php 7.0
sudo apt-get install -y apache2
sudo apt-get install -y php libapache2-mod-php php-mcrypt php-mysql php-intl python-software-properties php-xml php-zip php-bcmath php-curl

# phpunit
wget https://phar.phpunit.de/phpunit-5.7.phar
sudo chmod +x phpunit-5.7.phar
sudo mv phpunit-5.7.phar /usr/bin/phpunit
sudo cp ${PROJECT_PATH}/phpunit.xml.dist ${PROJECT_PATH}/phpunit.xml

# xdebug
sudo apt-get install -y php-xdebug

# install mysql and give password to installer
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password password $MYSQL_PASSWORD"
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $MYSQL_PASSWORD"
sudo apt-get install -y mysql-server php-mysql

# install phpmyadmin and give password(s) to installer
# for simplicity I'm using the same password for mysql and phpmyadmin
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/dbconfig-install boolean true"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/app-password-confirm password $MYSQL_PASSWORD"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/admin-pass password $MYSQL_PASSWORD"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/app-pass password $MYSQL_PASSWORD"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2"
sudo apt-get install -y phpmyadmin

# setup hosts file
VHOST=$(cat <<EOF
<VirtualHost *:80>
    DocumentRoot "/var/www/html/web"
    <Directory "/var/www/html/web">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
EOF
)
echo "${VHOST}" > /etc/apache2/sites-available/000-default.conf

# enable mod_rewrite
sudo a2enmod rewrite

# restart apache
sudo service apache2 restart

# install git
sudo apt-get install -y git

# enabling php7.0 in apache2
#sudo a2dismod php5
#sudo a2enmod php7.0
#sudo service apache2 restart

# install Composer
curl -s https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# install yaml
sudo apt-get install -y php7.0-dev libyaml-dev
sudo apt-get install -y php7.0-sqlite3
#sudo pecl install yamL
#sudo sh -c "echo 'extension=yaml.so' >> /etc/php5/mods-available/yaml.ini"
#sudo php7enmod yaml
# ssh
sudo cp ${PROJECT_PATH}/install/.ssh/config ~/.ssh/config
sudo cp ${PROJECT_PATH}/install/.ssh/id_rsa ~/.ssh/id_rsa
sudo cp ${PROJECT_PATH}/install/.ssh/id_rsa.pub ~/.ssh/id_rsa.pub

# composer
cd ${PROJECT_PATH}
composer install

cd ${PROJECT_PATH}
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
php bin/console doctrine:database:create --env=test
php bin/console doctrine:schema:update --force --env=test


################### PART ONLY ON VAGRANT ###################

# Apache2 envvars change
sudo cp ${PROJECT_PATH}/install/envvars /etc/apache2/envvars
sudo service apache2 restart
sudo chown ubuntu:ubuntu /var/lock/apache2