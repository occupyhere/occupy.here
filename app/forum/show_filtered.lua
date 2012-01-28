content_type('text/html')
end_headers()

show_header({
  class = 'forum filtered loading'
})

io.write('<h2 id="filter">loading...</h2>')
io.write('<div id="posts"></div>');

include("html/pagination_end.html", {
  from = '',
  to = '',
  total = ''
})
  
include("html/footer.html")
