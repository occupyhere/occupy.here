content_type('text/plain')
end_headers()

local topic_contents = ""
local reply_contents = ""

local known_topics = (forum.request.post.known_topics or '') .. ','
local known_replies = (forum.request.post.known_replies or '') .. ','

local new_topics = (forum.request.post.new_topics or '')
local new_replies = (forum.request.post.new_replies or '')

if new_topics ~= '' or new_replies ~= '' then
  new_topics = json.decode(new_topics)
  for i,topic in ipairs(new_topics) do
    local filename = "data/topics/" .. topic.id .. ".json"
    if file_exists(filename) ~= true then
      local f = assert(io.open(filename, "w"))
      f:write(json.encode(topic))
      f:close()
      if (file_exists("data/replies/" .. topic.id) ~= true) then
        lfs.mkdir("data/replies/" .. topic.id)
      end
    end
  end
  new_replies = json.decode(new_replies)
  for i,reply in ipairs(new_replies) do
    local filename = "data/replies/" .. reply.topic_id .. "/" .. reply.id .. ".json"
    if file_exists(filename) ~= true then
      local f = assert(io.open(filename, "w"))
      f:write(json.encode(reply))
      f:close()
    end
  end
else
  local topics = get_posts("data/topics")
  for i,n in ipairs(topics) do
    local filename = "data/topics/" .. n
    local topic_id = string.sub(n, 0, -6)
    if (known_topics:find(topic_id .. ',') == nil) then
      local f = assert(io.open(filename, "r"))
      local topic = f:read("*all")
      f:close()
      topic_contents = topic_contents .. '"' .. topic_id .. '":' .. topic .. ","
    else
      known_topics = string.gsub(known_topics, topic_id .. ",", "")
    end
    local replies = get_posts("data/replies/" .. topic_id)
    for j,m in ipairs(replies) do
      local filename = "data/replies/" .. topic_id .. "/" .. m
      local reply_id = string.sub(m, 0, -6)
      if (known_replies:find(reply_id .. ',') == nil) then
        local f = assert(io.open(filename, "r"))
        local reply = f:read("*all")
        f:close()
        reply_contents = reply_contents .. reply_id .. ':' .. reply .. ","
      else
        known_replies = string.gsub(known_replies, reply_id .. ",", "")
      end
    end
  end
  
  if string.len(topic_contents) > 0 then
    topic_contents = string.sub(topic_contents, 0, -2)
  end
  
  if string.len(reply_contents) > 0 then
    reply_contents = string.sub(reply_contents, 0, -2)
  end
  
  if string.len(known_topics) > 0 then
    known_topics = string.sub(known_topics, 0, -2)
  end
  
  if string.len(known_replies) > 0 then
    known_replies = string.sub(known_replies, 0, -2)
  end
  
  io.write('{"topics":{' .. topic_contents .. '},"replies":{' .. reply_contents .. '},')
  io.write('"send_topics":[' .. known_topics .. '],"send_replies":[' .. known_replies .. ']}')
  
end
