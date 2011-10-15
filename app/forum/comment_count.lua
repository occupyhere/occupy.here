local id = normalize_id(tonumber(forum.request.get.id))
local posts = get_posts("data/replies/" .. id)
local num = table.maxn(posts)
local s = "s"
if (num == 1) then
  s = ""
end
content_type('text/plain')
end_headers()
io.write(num .. " comment" .. s)
