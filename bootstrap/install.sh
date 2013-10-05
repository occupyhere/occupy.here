#!/bin/sh

MAC_ADDRESS=$(/sbin/ifconfig | grep 'eth0' | tr -s ' ' | cut -d ' ' -f5 | sed "s/://g")

echo
echo "Set the root password (enables ssh access)"
echo "------------------------------------------"
passwd

echo
echo "Installing packages (1/5)"
echo "-------------------------"
opkg -dest opt install /usb/occupy.here/bootstrap/step1/*

echo
echo "Installing packages (2/5)"
echo "-------------------------"
opkg -dest opt install /usb/occupy.here/bootstrap/step2/*

echo
echo "Installing packages (3/5)"
echo "-------------------------"
opkg -dest opt install /usb/occupy.here/bootstrap/step3/*

echo
echo "Installing packages (4/5)"
echo "-------------------------"
opkg -dest opt install /usb/occupy.here/bootstrap/step4/*

echo
echo "Installing packages (5/5)"
echo "-------------------------"
opkg -dest opt install /usb/occupy.here/bootstrap/step5/*

echo
echo "Finishing up"
echo "------------"
echo "Setting up symlinks..."
ln -s /opt/lib/libpthread-0.9.33.2.so /lib/libpthread.so.0
ln -s /opt/lib/librt-0.9.33.2.so /lib/librt.so.0
ln -s /opt/usr/lib/libcrypto.so.1.0.0 /usr/lib/libcrypto.so.1.0.0
ln -s /opt/usr/lib/libncurses.so.5.7 /usr/lib/libcurses.so
ln -s /opt/usr/lib/libform.so.5.7 /usr/lib/libform.so.5
ln -s /opt/usr/lib/libform.so.5.7 /usr/lib/libform.so
ln -s /opt/usr/lib/libjpeg.so.62.0.0 /usr/lib/libjpeg.so.62
ln -s /opt/usr/lib/libjpeg.so.62.0.0 /usr/lib/libjpeg.so
ln -s /opt/usr/lib/libmenu.so.5.7 /usr/lib/libmenu.so.5
ln -s /opt/usr/lib/libmenu.so.5.7 /usr/lib/libmenu.so
ln -s /opt/usr/lib/libncurses.so.5.7 /usr/lib/libncurses.so.5
ln -s /opt/usr/lib/libncurses.so.5.7 /usr/lib/libncurses.so
ln -s /opt/usr/lib/libpanel.so.5.7 /usr/lib/libpanel.so.5
ln -s /opt/usr/lib/libpanel.so.5.7 /usr/lib/libpanel.so
ln -s /opt/usr/lib/libpcre.so.0.0.1 /usr/lib/libpcre.so.0
ln -s /opt/usr/lib/libpcreposix.so.0.0.0 /usr/lib/libpcreposix.so.0
ln -s /opt/usr/lib/libreadline.so.5.2 /usr/lib/libreadline.so.5
ln -s /opt/usr/lib/libreadline.so.5.2 /usr/lib/libreadline.so
ln -s /opt/usr/lib/libsqlite3.so.0.8.6 /usr/lib/libsqlite3.so.0
ln -s /opt/usr/lib/libssl.so.1.0.0 /usr/lib/libssl.so.1.0.0
ln -s /opt/usr/lib/libxml2.so.2.7.8 /usr/lib/libxml2.so.2
ln -s /opt/usr/lib/libz.so.1.2.7 /usr/lib/libz.so.1
ln -s /opt/usr/lib/libz.so.1.2.7 /usr/lib/libz.so
ln -s /opt/usr/lib/lighttpd /usr/lib/lighttpd
ln -s /opt/usr/lib/opkg /usr/lib/opkg
ln -s /opt/usr/lib/php /usr/lib/php
ln -s /opt/usr/bin/php-cgi /usr/bin/php-fcgi
ln -s /opt/usr/bin/php-cli /usr/bin/php
ln -s /opt/usr/bin/sqlite3 /usr/bin/sqlite3
ln -s /opt/usr/bin/jpegtran /usr/bin/jpegtran
ln -s /opt/usr/sbin/lighttpd /usr/sbin/lighttpd
ln -s /opt/usr/share/zoneinfo /usr/share/zoneinfo
ln -s /opt/etc/lighttpd /etc/lighttpd
ln -s /opt/etc/init.d/lighttpd /etc/init.d/lighttpd
ln -s /opt/etc/init.d/lighttpd /etc/rc.d/S90lighttpd
ln -s /opt/etc/init.d/php5-fastcgi /etc/init.d/php5-fastcgi

echo "Copying config files..."
cp /usb/occupy.here/bootstrap/step6/config.php /usb/occupy.here/config.php
cp /usb/occupy.here/bootstrap/step6/php.ini /etc/php.ini
cp /usb/occupy.here/bootstrap/step6/lighttpd.conf /etc/lighttpd/lighttpd.conf
cp /usb/occupy.here/bootstrap/step6/dnsmasq.conf /etc/dnsmasq.conf

echo
echo "Generating GnuPG keys"
echo "---------------------"
cat >/usb/occupy.here/data/gpg.conf <<EOF
Key-Type: rsa
Key-Length: 2048
Name-Real: occupy.here
Name-Comment: $MAC_ADDRESS 
Name-Email: $MAC_ADDRESS@occupyhere.org
Expire-Date: 0
%pubring /usb/occupy.here/data/gpg.pub
%secring /usb/occupy.here/data/gpg.sec
%commit
EOF
/opt/usr/bin/gpg --batch --gen-key /usb/occupy.here/data/gpg.conf

echo "Starting lighttpd..."
/etc/init.d/lighttpd start

echo "Restarting dnsmasq..."
/etc/init.d/dnsmasq restart

echo "Starting wifi..."
/usr/bin/php -f /usb/occupy.here/bootstrap/step7/wireless.php

echo
echo "+----------------------------------------------------------------------------+"
echo "|                                                                            |"
echo "|  1. Join the wifi network 'OCCUPY.HERE / $MAC_ADDRESS'                     |"
echo "|  2. Open a browser and go to the URL: http://occupy.here/                  |"
echo "|                                                                            |"
echo "|  Note: in order to login to ssh using public key encryption, copy your     |"
echo "|        public key to /etc/config/dropbear/authorized_keys                  |"
echo "|                                                                            |"
echo "+----------------------------------------------------------------------------+"
echo

