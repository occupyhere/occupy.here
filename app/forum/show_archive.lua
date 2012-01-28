content_type('text/html')
end_headers()

include("html/header.html", {
  class = 'archive',
  title = forum.forum_title,
  description = forum.forum_description,
  username = username_html()
})

print "<ul>"
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
  print('<li><a href="' .. forum.archive_root .. 'videos/' .. file .. '" target="_blank">' .. title .. '</a><span class="date">' .. date_formatted .. "</span></li>")
end
print "</ul>"

include("html/footer.html")
