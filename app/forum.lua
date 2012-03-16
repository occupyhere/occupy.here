module("forum", package.seeall)

require "lfs"
require "json/json"
require "forum/utils"

if base_path ~= nil then
  lfs.chdir(base_path)   
end

public_root = public_root or "/"
if forum_offline then
  offline_class = 'offline'
else
  offline_class = ''
end
cgi = os.getenv("SCRIPT_NAME")

if file_exists(archive_base) then
  archive_class = 'show-archive'
else
  archive_class = 'hide-archive'
end

request = {
  post = {},
  get = parse_qs(os.getenv("QUERY_STRING"))
}

-- read POST data from STDIN
-- only read if there is any data,
-- otherwise the script hangs on some servers
if tonumber(os.getenv("CONTENT_LENGTH")) ~= nil then
  request.post = parse_qs(io.read("*all"))
end

function main()
  if request.get.x == "username" then
    require "forum/username"
  elseif request.get.x == "post" then
    require "forum/save_topic"
  elseif request.get.x == "topic" then
    require "forum/show_topic"
  elseif request.get.x == "reply" then
    require "forum/save_reply"
  elseif request.get.x == "sync_data" then
    require "forum/sync_data"
  elseif request.get.x == "filter" then
    require "forum/show_filtered"
  elseif request.get.x == "archive" then
    require "forum/show_archive"
  elseif request.get.x == "video" then
    require "forum/show_video"
  elseif request.get.x == "forum" then
    if get_cookie('author', '') == '' then
      require "forum/username"
    else
      require "forum/show_index"
    end
  else
    require "forum/show_about"
  end
end
