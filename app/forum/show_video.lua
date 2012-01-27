content_type('video/mp4')
end_headers()

local f = assert(io.open("archive/videos/" .. forum.get.file, "r"))
local block = 4096
while true do
  local bytes = f:read(block)
  if not bytes then break end
  io.write(bytes)
end
