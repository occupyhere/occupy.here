content_type('text/html')
end_headers()

show_header({
  class = 'about'
})

include("html/about.html")

include("html/footer.html")
