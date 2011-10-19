
local task = forum.request.post.task

content_type('text/html')
end_headers()

include("html/header.html", {
  class = 'topic',
  username = username_html()
})

local id = tonumber(forum.request.get.id) or tonumber(forum.request.post.topic)
local replies = get_posts("data/replies/" .. id)
local author = sanitize(forum.request.post.author or get_cookie('author', 'Anonymous'))

show_post("data/topics/" .. id .. ".json")

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

include("html/reply_form.html", {
  topic = id,
  author = author,
  content = sanitize(forum.request.post.content or '')
})
include("html/footer.html")
