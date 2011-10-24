content_type('text/html')
end_headers()

include("html/header.html", {
  class = 'home',
  title = forum.forum_title,
  username = username_html()
})

local offset = tonumber(forum.request.get.offset) or 0
local count = 10
local posts = get_posts("data/topics")
local total = table.maxn(posts)
local shown = 0

select_posts(posts, offset, count, true, function(file)
  shown = shown + 1
  show_post("data/topics/" .. file)
end)

if total > offset + count then
  include("html/pagination.html", {
    offset = offset + count,
    from = offset + 1,
    to = offset + shown,
    total = total
  })
else
  include("html/pagination_end.html", {
    from = offset + 1,
    to = offset + shown,
    total = total
  })
end

include("html/footer.html")
