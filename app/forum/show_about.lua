content_type('text/html')
end_headers()

include("html/header.html", {
  class = 'about',
  title = forum.forum_title,
  description = forum.forum_description,
  username = username_html()
})

include("html/about.html")

include("html/footer.html")
