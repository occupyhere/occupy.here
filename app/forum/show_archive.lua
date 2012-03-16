content_type('text/html')
end_headers()

show_header({
  class = 'archive'
})

io.write('<div id="articles">')
files = get_media(archive_base .. "/videos", "mp4")
for i,file in ipairs(files) do
  local title = string.sub(file, 14, -5)
  local date = split(string.sub(file, 0, 10), '-')
  local time = os.time({
    year = tonumber(date[1]),
    month = tonumber(date[2]),
    day = tonumber(date[3])
  })
  local date_formatted = os.date('%b %d, %Y', time)
  local file_encoded = url_encode(file)
  file_encoded = string.gsub(file_encoded, '&', '&amp;')
  print('<article><a href="?x=video&file=' .. file_encoded .. '"><span class="title">' .. title .. '</span><span class="date">' .. date_formatted .. "</span></a></article>")
end
io.write('</div>')

include("html/footer.html")
