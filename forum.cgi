#!/usr/bin/lua

----------------------------
-- Begin of configuration --

-- For future use
location_name = 'nyc'
location_latlng = '40.709328,-74.011245'

-- If your CGI file is located in a different directory, use forum_base to
-- guide it back to the base directory.
-- For OpenWRT routers, use '/www'
forum_base = '../openwrt-forum'

-- Set public_root to the path where the forum lives, with a trailing slash.
-- For OpenWRT routers, use '/'
public_root = '/openwrt-forum/'

-- End of configuration --
--------------------------


require "lfs"
if forum_base ~= nil then
  lfs.chdir(forum_base)
end

package.path = package.path .. ";./?.lua;./app/?.lua"

local forum = require "forum"
forum.main()
