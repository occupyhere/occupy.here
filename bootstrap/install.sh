#!/bin/sh

MAC_ADDRESS=$(/sbin/ifconfig | grep 'eth0' | tr -s ' ' | cut -d ' ' -f5 | sed "s/://g")

if [ ! -e /usb/occupy.here ] &&
   [ -e /usb/occupy.here-r7 ]
then
  echo "Renamed /usb/occupy.here-r7 to /usb/occupy.here"
  mv /usb/occupy.here-r7 /usb/occupy.here
fi

if [ ! -e /var/.password_set ]
then
  echo
  echo "Set the root password (enables ssh access)"
  echo "------------------------------------------"
  echo "Note: if you see a warning about weak passwords, it is ok to ignore for now."
  echo "      Weak passwords are not recommended, but will be accepted. You can use"
  echo "      the passwd command to choose a stronger password later. You will need"
  echo "      to remember this password for logging in as an admin user later."
  echo
  passwd
  touch /var/.password_set
fi

if ! df | grep " /usb" > /dev/null
then
  mount /dev/sda1 /usb
fi

if ! df | grep " /opt" > /dev/null
then
  if [ ! -e /usb/occupy.here/data/opt.img ]
  then
    echo
    echo "Creating opt.img disk image"
    echo "---------------------------"
    echo "This will probably take a few minutes..."
    dd if=/dev/zero of=/usb/occupy.here/data/opt.img bs=4096 count=32768
    mkfs.ext4 -F /usb/occupy.here/data/opt.img
  fi
  mount /usb/occupy.here/data/opt.img /opt
  if [ ! -e /opt/lost+found ]
  then
    echo "Error: could not mount opt.img"
    echo "  Try deleting /usb/occupy.here/data/opt.img and run install.sh again"
    exit 1
  fi
fi

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
echo "Configuring things"
echo "------------------"

symlink()
{
  if [ ! -h "$2" ]
  then
    ln -s "$1" "$2"
  fi
}

echo "Setting up symlinks..."

symlink /opt/lib/libpthread-0.9.33.2.so /lib/libpthread.so.0
symlink /opt/lib/librt-0.9.33.2.so /lib/librt.so.0
symlink /opt/usr/lib/libcrypto.so.1.0.0 /usr/lib/libcrypto.so.1.0.0
symlink /opt/usr/lib/libncurses.so.5.7 /usr/lib/libcurses.so
symlink /opt/usr/lib/libform.so.5.7 /usr/lib/libform.so.5
symlink /opt/usr/lib/libform.so.5.7 /usr/lib/libform.so
symlink /opt/usr/lib/libintl.so.8.1.1 /usr/lib/libintl.so.8.1.1
symlink /opt/usr/lib/libintl.so.8.1.1 /usr/lib/libintl.so.8.1
symlink /opt/usr/lib/libintl.so.8.1.1 /usr/lib/libintl.so.8
symlink /opt/usr/lib/libintl.so.8.1.1 /usr/lib/libintl.so
symlink /opt/usr/lib/libjpeg.so.62.0.0 /usr/lib/libjpeg.so.62
symlink /opt/usr/lib/libjpeg.so.62.0.0 /usr/lib/libjpeg.so
symlink /opt/usr/lib/libmenu.so.5.7 /usr/lib/libmenu.so.5
symlink /opt/usr/lib/libmenu.so.5.7 /usr/lib/libmenu.so
symlink /opt/usr/lib/libncurses.so.5.7 /usr/lib/libncurses.so.5
symlink /opt/usr/lib/libncurses.so.5.7 /usr/lib/libncurses.so
symlink /opt/usr/lib/libpanel.so.5.7 /usr/lib/libpanel.so.5
symlink /opt/usr/lib/libpanel.so.5.7 /usr/lib/libpanel.so
symlink /opt/usr/lib/libpcre.so.0.0.1 /usr/lib/libpcre.so.0
symlink /opt/usr/lib/libpcreposix.so.0.0.0 /usr/lib/libpcreposix.so.0
symlink /opt/usr/lib/libreadline.so.5.2 /usr/lib/libreadline.so.5
symlink /opt/usr/lib/libreadline.so.5.2 /usr/lib/libreadline.so
symlink /opt/usr/lib/libsqlite3.so.0.8.6 /usr/lib/libsqlite3.so.0
symlink /opt/usr/lib/libssl.so.1.0.0 /usr/lib/libssl.so.1.0.0
symlink /opt/usr/lib/libxml2.so.2.7.8 /usr/lib/libxml2.so.2
symlink /opt/usr/lib/libz.so.1.2.7 /usr/lib/libz.so.1
symlink /opt/usr/lib/libz.so.1.2.7 /usr/lib/libz.so
symlink /opt/usr/lib/lighttpd /usr/lib/lighttpd
symlink /opt/usr/lib/opkg /usr/lib/opkg
symlink /opt/usr/lib/php /usr/lib/php
symlink /opt/usr/bin/php-cgi /usr/bin/php-fcgi
symlink /opt/usr/bin/php-cli /usr/bin/php
symlink /opt/usr/bin/sqlite3 /usr/bin/sqlite3
symlink /opt/usr/bin/jpegtran /usr/bin/jpegtran
symlink /opt/usr/sbin/lighttpd /usr/sbin/lighttpd
symlink /opt/usr/share/zoneinfo /usr/share/zoneinfo
symlink /opt/etc/lighttpd /etc/lighttpd
symlink /opt/etc/init.d/lighttpd /etc/init.d/lighttpd
symlink /opt/etc/init.d/lighttpd /etc/rc.d/S90lighttpd
symlink /opt/etc/init.d/php5-fastcgi /etc/init.d/php5-fastcgi

echo "Copying config files..."

if [ -e /etc/lighttpd/lighttpd.conf ]
then
  mv /etc/lighttpd/lighttpd.conf /etc/lighttpd/lighttpd.conf.bak
fi

cp /usb/occupy.here/bootstrap/step6/config.php /usb/occupy.here/config.php
cp /usb/occupy.here/bootstrap/step6/php.ini /etc/php.ini
cp /usb/occupy.here/bootstrap/step6/lighttpd.conf /etc/lighttpd/lighttpd.conf
cp /usb/occupy.here/bootstrap/step6/dnsmasq.conf /etc/dnsmasq.conf
cp /usb/occupy.here/bootstrap/step6/banner /etc/banner

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

echo
echo "Finishing up"
echo "------------"
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
echo "|  3. Visit http://occupy.here/admin to access admin-only functionality      |"
echo "|     (also available from the menu), login with user 'root' and the         |"
echo "|     password you typed in earlier.                                         |"
echo "|                                                                            |"
echo "+----------------------------------------------------------------------------+"
echo
