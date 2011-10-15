module("forum", package.seeall)

require "config"
require "lfs"
require "json/json"
require "forum/utils"

public_root = public_root or "/"
cgi = os.getenv("SCRIPT_NAME")

if forum_base ~= nil then
  lfs.chdir(forum_base)
end

request = {
  post = parse_qs(io.read("*all")), -- POST data comes from STDIN
  get = parse_qs(os.getenv("QUERY_STRING"))
}

function main()
  if request.get.x == "post" then
    require "forum/save_post"
  elseif request.get.x == "topic" then
    require "forum/show_topic"
  elseif request.get.x == "reply" then
    require "forum/save_reply"
  elseif request.get.x == "comments" then
    require "forum/comment_count"
  else
    require "forum/show_index"
  end
end
