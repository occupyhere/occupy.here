content_type('text/html')
end_headers()

show_header({
  class = 'archive'
})

io.write('<div id="articles">')
files = get_media_archive(archive_base)
for i,file in ipairs(files) do
  local media = get_media_detail(file)
  io.write(archive_html(media))
end
io.write('</div>')

include("html/footer.html")
