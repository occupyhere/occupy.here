# How to upgrade Occupy.here

These instructions assume you already have Occupy.here running on a wifi router.

1. Download the [latest revision](http://downloads.occupyhere.org/occupy.here-latest.zip) of Occupy.here.
2. Power your wifi router off, unplug the memory stick, and plug it into your computer.
3. Rename the 'occupy.here' folder on the USB memory stick to 'occupy.here.old'.
4. Unzip the latest revision you downloaded in step 1, and copy the folder it contains onto the USB memory stick.
5. Rename the folder you just copied onto the memory stick to be `occupy.here` if it isn't already.
6. Copy `occupy.here.old/config.php` to `occupy.here/config.php`
7. Delete `occupy.here/data` and move `occupy.here.old/data` to take its place.
8. Delete `occupy.here/public/uploads` and move `occupy.here.old/public/uploads` to take its place.
9. Eject the USB memory stick and plug it into the wifi router
10. Start up the wifi router.
