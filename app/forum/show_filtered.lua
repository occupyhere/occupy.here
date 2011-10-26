content_type('text/html')
end_headers()

include("html/header.html", {
  class = 'filtered loading',
  title = forum.forum_title,
  description = forum.forum_description,
  username = username_html()
})

io.write('<h2 id="filter">loading...</h2>')
io.write('<div id="posts"></div>');

include("html/pagination_end.html", {
  from = '',
  to = '',
  total = ''
})
  
include("html/footer.html")
