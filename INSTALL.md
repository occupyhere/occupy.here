### Technology overview

Occupy.here will run on [any wifi router supported by OpenWrt](http://wiki.openwrt.org/toh), but the custom firmware and this documentation are currently limited to the TP-Link [TL-WR703N](http://wiki.openwrt.org/toh/tp-link/tl-wr703n) or [TL-WR3020](http://wiki.openwrt.org/toh/tp-link/tl-mr3020). These routers are small, portable, and can run off a 5V USB connection. Note that the default firmware for the TL-703N is in Chinese only, and may prove slightly harder to purchase, but it is the smallest and best router for Occupy.here. Even if you aren’t proficient reading Chinese, installing the firmware should be straightforward. The English-language [TP-Link TL-WR3020](http://wiki.openwrt.org/toh/tp-link/tl-mr3020) is almost exactly the same, except slightly larger.

The Occupy.here website software is written in PHP and stores its content in a SQLite database. Other supporting services include Lighttpd and Dnsmasq, which are configured to simulate a captive portal (i.e., all requests redirect back to http://occupy.here/). There is ongoing research into future support for PGP encrypted messages and email-based syncing between individual routers, but this has not yet been implemented.

### Hardware requirements

* A wifi router [supported by OpenWrt](http://wiki.openwrt.org/toh). Recommended: [TP-Link TL-WR703N](http://wiki.openwrt.org/toh/tp-link/tl-wr703n) ([buy on Amazon](http://www.amazon.com/gp/product/B005VEJ3GM/))
* A USB stick, anything over 1GB should work. Recommended: [Sandisk Cruzer Fit 8GB](http://www.sandisk.com/products/usb/drives/cruzer-fit/) ([buy on Amazon](http://www.amazon.com/SanDisk-Cruzer-SDCZ33-008G-B35-Flash-Drive/dp/B005FYNSUA))
* A computer (with an Ethernet port)
* An ethernet cable
* Optional: portable battery ([buy on Amazon](http://www.amazon.com/dp/B005IHDVLU/))

### Preparing the USB stick

Your USB stick should be formatted as FAT 32, with a single partition. This is the default on most USB sticks, so you shouldn't have to do anything special.

1. Download occupy.here-r1.zip and unzip the contents. You should see a folder called **occupy.here**.
2. Plug in your USB stick and **copy the occupy.here folder** onto it. Keep the original folder on your computer, you'll need it again in a minute.
3. Eject the USB stick and **plug it into your wifi router**.

### Preparing the wifi router

1. Turn off your computer's wifi and connect it via **Ethernet cable** to the wifi router.
2. **Power up the wifi router** by connecting the micro USB connector (next to the Ethernet) to a power source. When the blue light stops flashing, and stays solidly lit, you should be ready to continue.
3. Load http://192.168.1.1/ in a browser to access the default web admin interface, username **admin** password **admin**. (On the TL-MR3020 router, the URL is http://192.168.0.254/)
4. You should see a web interface in Mandarin Chinese. Don’t panic, this will be easy! Click the **last option** in the left-hand menu, then the **third sub-option** below that. (On the TL-MR3020 router, go to System Tools &rarr; Firmware Upgrade.)
5. You should see a thin rectangular box with a button next to it. That is the **file selection box**. Click on it to browse for the firmware to flash onto the router.
6. Browse to the **occupy.here** folder on your computer, then look inside the **firmware** folder. Choose the file **tl-wr703n-factory.bin**. (On the TL-MR3020 router, use tl-mr3020-factory.bin.)
7. Click the **lavendar button** below and to the left of the file selection box. Confirm by clicking **OK** and wait for the firmware to flash onto the router.
8. The router will automatically reboot and the default admin interface will attempt to reload (and fail), leaving you on a browser error page.

### Bootstrapping Occupy.here

At this point you should have an OpenWrt router, expanded with additional storage from the USB stick. When the router first powers on, it will start blinking its blue light as it starts up. Once the light goes solid, you should be ready to continue.

1. Connect your computer to the router via **Ethernet cable** and configure your computer so that it uses the IP address **192.168.1.2**. In OS X, go to **System Preferences** &rarr; **Network** &rarr; **Ethernet** (or Thunderbolt Ethernet). Choose **Manually** from the drop-down, then enter IP address **192.168.1.2**, Subnet Mask **255.255.255.0**, Router **192.168.1.1**.
2. Open a **command line terminal**. On OS X, open **Applications** &rarr; **Utilities** &rarr; **Terminal.app**.
3. Type `telnet 192.168.1.1` and press enter. You should see the "OCCUPY.HERE" ASCII banner. If your router *just* rebooted, you may need to wait a moment before it's ready for you to connect.
4. Type `df -h`. You should see a list of mounted filesystems, including those mounted to `/usb` and `/opt`. If you don't see those, but instead you find `/tmp/root/usb` and `/tmp/root/opt`, you should restart your router for the filesystems to get recognized properly, then telnet back in.
5. Type `cd /usb/occupy.here/bootstrap`.
6. Type `./install.sh` to start the bootstrap process.
7. You will be prompted to set the root password on your router, and then confirm it by typing it again. After you've set the password, the router will reject telnet connections and start allowing connections via ssh. The rest of the bootstrapping process will take a few minutes install packages and configure the software.
8. Once the script finishes, your telnet session will be terminated and you should have a new wifi network available called **OCCUPY.HERE / XXXXXXXXXXXX** (the last part is set according to your router's MAC address).

### Using Occupy.here

After the router is bootstrapped, the Ethernet port stops allowing telnet and ssh sessions and starts allowing the router to connect to an upstream Internet connection. It does not require an Internet connection, but if one is available it will use it.

1. Join the wifi network **OCCUPY.HERE / XXXXXXXXXXXX**.
2. Open a browser and go to the URL: http://occupy.here/
3. To connect via ssh, use the IP address 10.0.47.1. Login as user **root** and use the password you entered at the beginning of the bootstrap process. If you'd like to login to ssh using public key encryption, copy your public key to /etc/config/dropbear/authorized_keys.

If your Occupy.here router is connected to an Internet connection via Ethernet, you should be able to use its wifi connection instead of your usual wifi network. However, the website http://occupy.here/ will *only* be available to those on Occupy.here. Be aware that this network is open, without any password protection.
