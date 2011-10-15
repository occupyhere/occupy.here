local task = forum.request.post.task
local topic = tonumber(forum.request.post.topic) or 0
local timestamp = tonumber(forum.request.post.timestamp) or 0
local post = {
  topic = topic,
  timestamp = timestamp,
  content = forum.request.post.content or '',
  author = forum.request.post.author or get_cookie('author', 'Anonymous'),
  date = forum.request.post.date or os.date("%a %b %d, %Y"),
  time = forum.request.post.time or os.date("%I:%M %p")
}
 
if (task == 'preview') then
  post.id = 'reply'
  post.reply_id = 'preview'
  forum.preview_post = post
  require "forum/show_topic"
else
  if (post.content ~= nil and post.content ~= '') then
    local f = assert(io.open("data/replies/" .. post.topic .. "/" .. post.timestamp .. ".json", "w"))
    local json = json.encode(post)
    f:write(json)
    f:close()
  end
  redirect(forum.cgi .. "?x=topic&id=" .. post.topic .. "#reply-" .. post.timestamp)
end

