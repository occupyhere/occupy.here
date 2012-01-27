content_type('text/html')
end_headers()

include("html/header.html", {
  class = 'archive',
  title = forum.forum_title,
  description = forum.forum_description,
  username = username_html()
})

include("html/video.html", {
  file = forum.request.get.file
})

include("html/footer.html")
