# occupy.here

## Peer-to-peer social software for the Occupy movement

[Occupy.here](http://occupyhere.org/) is a software project built in support of the [Occupy Wall Street](http://nycga.net/) protest in New York City and other occupations beyond Zuccotti Park. The project was formerly called ows.offline, referring to its capacity to run on locally-hosted hardware without an upstream Internet connection. With occupy.here, users can anonymously post messages and files to a host router running [OpenWrt Linux](https://openwrt.org/). A syncing mechanism allows remote instances of the software to exchange content using a kind of cross-pollination enabled by the movement of users.

The project is still in an early phase of development and welcomes new contributers.

### General installation guide

1. Configure your webserver to serve from the `public` directory
2. Change file permissions on the `data` and `public/uploads` directories so the webserver user can write new files
3. Create a new `config.php` based on `config-example.php`, update the settings to your needs

*More documentation coming soon!*
