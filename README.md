# OCCUPY.HERE

## Peer-to-peer forum software for the Occupy movement

Occupy.here is a project to support the [Occupy Wall Street][ows] protest in New York City, as well as other occupations beyond Zuccotti Park. The project was formerly called ows.offline, referring to the possibility of running the software on locally-hosted hardware without an upstream Internet connection. The forum is open source software that can be installed on any wifi router capable of running OpenWRT. A simple syncing mechanism allows remote instances of the forum software to share content using a kind of cross-pollination enabled by the movement of their users.

## How to install

This forum software was originally designed for the [Linksys WRT54GL][wrt54g] router running [OpenWRT][owrt] Linux and [uhttpd]. The latest builds are mainly being tested with another router, the [Netgear N600 WNDR3700v2][n600] which is the current recommended hardware. The software has also been tested on Mac OS X (with Apache), Debian/Ubuntu (with [thttpd]) and should run fine on any CGI capable webserver with Lua and LuaFileSystem installed.

## Installing dependencies on Debian/Ubuntu

1. Get a root shell: `sudo -i`
2. Install [Lua][lua] and [Luarocks][lrock]: `apt-get install lua luarocks`
3. Install [LuaFileSystem][lfs]: `luarocks install lfs`

## Installing on any platform

1. Install dependencies: [Lua][lua] + [LuaFileSystem][lfs]
2. Unpack the zip file in a public web folder
3. Copy `forum.cgi.example` to `forum.cgi` and move it to your webserver's CGI
   directory (e.g., `/www/cgi-bin`)
4. Modify the configuration settings in `forum.cgi`:
    * Set your location name and coordinates (for p2p functionality)
    * Point `base_path` to the forum's directory in the filesystem
    * Point `public_root` to the forum's URL as seen from the "web"
6. Adjust file permissions if necessary (not necessary by default in OpenWRT):
    * Base and subdirectories accessible (executable) and readable by
      the httpd user
    * `data` directory writable by the httpd user (www-data or www)
    * Some webservers don't like executable static files (css, javascript)
7. Try it out in a browser by loading up the CGI script! The address should look
   like `http://192.168.1.1/cgi-bin/forum.cgi`

## Installing on OpenWRT

*These instructions currently assume that you're experienced with basic Unix
tools and have a sense of adventure. It is possible to brick your router if
you're not careful with the settings, so please be cautious.*

1. If you're running the default vendor-provided OS, you'll first need to [flash
   your router][flash] with the [OpenWRT system image][squashfs]
2. Download and scp the [LuaFileSystem package][lfsipk] to the router
   (scp the file to `/tmp` on your router)
3. Install the package using: `opkg install /tmp/luafilesystem_1.5.0-1_brcm-2.4.ipk`
4. Use scp to put all the forum's files in `/www` on the router
5. Copy `forum.cgi.example` to `/www/cgi-bin/forum.cgi`
6. Modify the configuration settings in `forum.cgi`:
    * Set your location name and coordinates
    * Update `base_path` to `'/www/'`
    * Update `public_root` to `'/'`
7. Try it out in a browser! (something like `http://192.168.1.1/cgi-bin/forum.cgi`)

## Tuning OpenWRT

There are a couple things you can do to modify your router to make it to help
guide users to the right place.

### Set up a wildcard DNS entry

It's a good idea to resolve all domains to `192.168.1.1`. This will make the router
behave as a kind of captive portal.

  * Edit `/etc/dnsmasq.conf` and add the line `address=/#/192.168.1.1`
  * Restart the DNS daemon with `/etc/init.d/dnsmasq restart`

### Change the base redirect

  * Edit `/www/index.html` to redirect to the forum URL
  * This URL is ideal for syncing purposes: `http://occupy.here/cgi-bin/forum.cgi`

### Change the error page configuration

  * Edit `/etc/config/uhttpd` and add the following configuration:  
    `option error_page    /`
  * Restart the webserver with `/etc/init.d/uhttpd restart`

[ows]: http://occupywallst.org/
[hm]: http://www.thenation.com/blog/163767/we-are-all-human-microphones-now
[wrt54g]: http://en.wikipedia.org/wiki/Linksys_WRT54G_series
[n600]: http://wiki.openwrt.org/toh/netgear/wndr3700
[owrt]: https://openwrt.org/
[hb]: http://en.wikipedia.org/wiki/Shebang_%28Unix%29
[lua]: http://lua.org/
[lfs]: http://keplerproject.github.com/luafilesystem/
[lrock]: http://luarocks.org/
[flash]: http://wiki.openwrt.org/doc/howto/generic.flashing
[squashfs]: http://downloads.openwrt.org/backfire/10.03/brcm-2.4/openwrt-wrt54g-squashfs.bin
[lfsipk]: http://downloads.openwrt.org/backfire/10.03/brcm-2.4/packages/luafilesystem_1.5.0-1_brcm-2.4.ipk
[uhttpd]: http://code.google.com/p/uhttpd/
[thttpd]: http://acme.com/software/thhtpd/
