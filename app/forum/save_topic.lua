local task = forum.request.post.task
local post = {
  id = validate_id(forum.request.post.id),
  content = forum.request.post.content or '',
  author = forum.request.post.author or get_cookie('author', 'Anonymous'),
  date = forum.request.post.date or os.date("%a %b %d, %Y"),
  time = forum.request.post.time or os.date("%I:%M %p"),
  location = forum.location_name,
  latlng = forum.location_latlng
}

if task == "preview" or (post.content == '') then
  content_type('text/html')
  end_headers()
  include("html/header.html", {
    class = 'forum topic',
    title = forum.forum_title,
    description = forum.forum_description,
    username = username_html()
  })
  if task == "preview" then
    post.id = 'preview'
    io.write('<h2 id="preview">post preview</h2>')
    io.write('<div class="preview">')
    io.write(topic_html(post))
    if (forum.request.post.first_comment ~= '') then
      local reply = {
        id = 'preview',
        topic_id = 'reply',
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
  local filename = "data/topics/" .. post.id .. ".json"
  if file_exists(filename) ~= true then
    local f = assert(io.open(filename, "w"))
    f:write(json.encode(post))
    f:close()
    lfs.mkdir("data/replies/" .. post.id)
    set_cookie('author', post.author)
  end
  
  local first_comment = forum.request.post.first_comment
  filename = "data/replies/" .. post.id .. "/" .. post.id .. ".json"
  if file_exists(filename) ~= true and first_comment ~= nil and first_comment ~= "" then
    f = assert(io.open(filename, "w"))
    local reply = {
      id = post.id,
      topic_id = post.id,
      content = first_comment,
      author = post.author,
      date = post.date,
      time = post.time,
      location = forum.location_name,
      latlng = forum.location_latlng
    }
    f:write(json.encode(reply))
    f:close()
  end
  
  redirect(forum.cgi .. "?x=forum")
end
