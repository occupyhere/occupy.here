local task = forum.request.post.task
local timestamp = tonumber(forum.request.post.timestamp) or 0
local post = {
  timestamp = timestamp,
  content = forum.request.post.content or '',
  author = forum.request.post.author or get_cookie('author', 'Anonymous'),
  date = forum.request.post.date or os.date("%a %b %d, %Y"),
  time = forum.request.post.time or os.date("%I:%M %p")
}

if task == "preview" or (post.content == '') then
  content_type('text/html')
  end_headers()
  include("html/header.html", {
    class = 'topic'
  })
  if task == "preview" then
    post.id = 'preview'
    io.write('<h2 id="preview">post preview</h2>')
    io.write('<div class="preview">')
    io.write(topic_html(post))
    if (forum.request.post.first_comment ~= '') then
      local reply = {
        topic = 'reply',
        timestamp = 'preview',
        content = forum.request.post.first_comment or '',
        author = forum.request.post.author or get_cookie('author', 'Anonymous'),
        date = os.date("%a %b %d, %Y"),
        time = os.date("%I:%M %p")
      }
      io.write('<section id="replies">' .. reply_html(reply) .. '</section>')
    end
    io.write('</div>')
  end
  include("html/topic_form.html", {
    author = sanitize(post.author),
    content = sanitize(post.content),
    first_comment = sanitize(forum.request.post.first_comment or '')
  })
  include("html/footer.html")
else
  local f = assert(io.open("data/topics/" .. post.timestamp .. ".json", "w"))
  
  f:write(json.encode(post))
  f:close()
  lfs.mkdir("data/replies/" .. post.timestamp)
  set_cookie('author', post.author)
  
  local first_comment = forum.request.post.first_comment
  if (first_comment ~= nil and first_comment ~= "") then
    f = assert(io.open("data/replies/" .. post.timestamp .. "/" .. post.timestamp .. ".json", "w"))
    local reply = {
      timestamp = post.timestamp,
      content = first_comment,
      author = post.author,
      date = post.date,
      time = post.time
    }
    f:write(json.encode(reply))
    f:close()
  end
  
  redirect(forum.cgi)
end
