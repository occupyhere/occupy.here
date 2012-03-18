local task = forum.request.post.task
local post = {
  id = validate_id(forum.request.post.id),
  topic_id = validate_id(forum.request.post.topic_id),
  content = forum.request.post.content or '',
  author = forum.request.post.author or get_cookie('author', 'Anonymous'),
  location = forum.location_name,
  latlng = forum.location_latlng
}
 
if (task == 'preview') then
  post.id = 'preview'
  forum.preview_post = post
  require "forum/show_topic"
else
  local dir = os.date('%Y-%m-%d', math.floor(tonumber(post.topic_id) / 1000)) .. '-' .. post.topic_id
  local filename = "data/forum/" .. dir .. "/" .. post.id .. ".json"
  if file_exists(filename) ~= true and post.content ~= nil and post.content ~= '' then
    local f = assert(io.open(filename, "w"))
    local json = json.encode(post)
    f:write(json)
    f:close()
  end
  filename = "data/forum/" .. dir .. "/" .. post.topic_id .. ".json"
  local topic = get_data(filename)
  local thread = get_thread("data/forum/" .. dir)
  topic.comment_count = table.maxn(thread) - 1
  local f = assert(io.open(filename, "w"))
  local json = json.encode(topic)
  f:write(json)
  f:close()
  redirect(forum.cgi .. "?x=topic&id=" .. post.topic_id .. "#reply-" .. post.id)
end

