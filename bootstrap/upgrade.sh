#!/bin/sh

echo
echo "Upgrading firmware"
echo "------------------"

echo "Copying sysupgrade firmware image to /tmp..."
cp /mnt/usb/occupy.here/firmware/tl-wr703n-sysupgrade.bin /tmp/

mtd write /tmp/tl-wr703n-sysupgrade.bin firmware

echo
echo "+----------------------------------------------------------------------------+"
echo "|                                                                            |"
echo "|  Your firmware has been upgraded and the router is now rebooting. There    |"
echo "|  still a few more steps to take before you can start using it.             |"
echo "|                                                                            |"
echo "|  1. Wait for the current (soft) reboot to finish (blue light solidly lit)  |"
echo "|  2. Unplug the power and plug it back in (hard reboot)                     |"
echo "|  3. Wait until the router's blue light stays solidly lit                   |"
echo "|  4. Turn off your computer's wifi, and make sure your computer is          |"
echo "|     connected via Ethernet                                                 |"
echo "|  5. Start a new command line session to bootstrap occupy.here:             |"
echo "|                                                                            |"
echo "|     telnet 192.168.1.1                                                     |"
echo "|     passwd                                                                 |"
echo "|     cd /mnt/usb/occupy.here                                                |"
echo "|     ./bootstrap/install.sh                                                 |"
echo "|                                                                            |"
echo "+----------------------------------------------------------------------------+"
echo
echo "Rebooting..."
reboot
