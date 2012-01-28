content_type('text/html')
end_headers()

show_header({
  class = 'archive'
})

io.write('<div id="articles">')
files = get_media("archive/videos", "mp4")
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
  print('<article><a href="?x=video&file=' .. file .. '"><span class="title">' .. title .. '</span><span class="date">' .. date_formatted .. "</span></a></article>")
end
io.write('</div>')

include("html/footer.html")
