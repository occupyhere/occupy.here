local task = forum.request.post.task
local post = {
  id = validate_id(forum.request.post.id),
  topic_id = validate_id(forum.request.post.topic_id),
  content = forum.request.post.content or '',
  author = forum.request.post.author or get_cookie('author', 'Anonymous'),
  date = forum.request.post.date or os.date("%a %b %d, %Y"),
  time = forum.request.post.time or os.date("%I:%M %p"),
  location = forum.location_name,
  latlng = forum.location_latlng
}
 
if (task == 'preview') then
  post.id = 'preview'
  forum.preview_post = post
  require "forum/show_topic"
else
  local filename = "data/replies/" .. post.topic_id .. "/" .. post.id .. ".json"
  if file_exists(filename) ~= true and post.content ~= nil and post.content ~= '' then
    local f = assert(io.open(filename, "w"))
    local json = json.encode(post)
    f:write(json)
    f:close()
  end
  redirect(forum.cgi .. "?x=topic&id=" .. post.topic_id .. "#reply-" .. post.id)
end

