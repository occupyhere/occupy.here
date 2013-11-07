# occupy.here

## A tiny, self-contained darknet

[Occupy.here](http://occupyhere.org/) is a software project built in support of the [Occupy Wall Street](http://nycga.net/) protest in New York City and other occupations beyond Zuccotti Park. The software is designed to run on locally-hosted hardware without an upstream Internet connection. With occupy.here, users can anonymously share messages and files. The software should run on any web server capable of parsing PHP, but it is intended to run on a particular environment; a wifi router running [OpenWrt Linux](https://openwrt.org/).

### Basic installation guide

If you'd like to try out the software without using the full OpenWrt stack, you should be able to get running with the following commands:

1. Configure your webserver to serve from the `public` directory
2. Change file permissions on the `data` and `public/uploads` directories so the webserver user can write new files
3. Create a new `config.php` based on `config-example.php`, update the settings to your needs

### Full wifi router installation
 
If you would like to build an occupy.here router, follow the [installation guide](https://github.com/occupyhere/occupy.here/wiki/How-to-build-Occupy.here), also available in the included files as INSTALL.md.
