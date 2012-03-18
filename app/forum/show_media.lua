content_type('text/html')
end_headers()

local id = forum.request.get.id
local date = os.date('%Y-%m-%d', math.floor(id / 1000))
local file = archive_base .. date .. '-' .. id .. '/' .. id .. '.json'
local media = get_media_detail(file)

show_header({
  class = media.type .. ' archive'
})

if media.type == "video" then
  media.src = forum.archive_root .. date .. '-' .. id .. '/' .. id .. '.mp4'
  media.width = 620
  media.height = 465
end

include("html/" .. media.type .. ".html", media)

include("html/footer.html")
