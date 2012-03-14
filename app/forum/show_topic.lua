
local task = forum.request.post.task

content_type('text/html')
end_headers()

show_header({
  class = 'forum topic'
})

local id = validate_id(forum.request.get.id) or validate_id(forum.request.post.topic_id)
local replies = get_posts("data/forum/replies/" .. id)
local author = sanitize(forum.request.post.author or get_cookie('author', 'Anonymous'))

io.write('<div id="articles">')
show_post("data/forum/" .. id .. ".json")

if table.maxn(replies) > 0 or task == 'preview' then
  io.write('<section id="replies">')
  select_posts(replies, 0, 99999, false, function(file)
    show_post("data/replies/" .. id .. "/" .. file)
  end)
  if (task == 'preview') then
    io.write('<h2 id="preview">reply preview</h2>')
    io.write('<div class="preview">')
    io.write(reply_html(forum.preview_post))
    io.write('</div>')
  end
  io.write('</section>')
end
io.write('</div>')

include("html/reply_form.html", {
  topic_id = id,
  author = author,
  content = sanitize(forum.request.post.content or '')
})
include("html/footer.html")
