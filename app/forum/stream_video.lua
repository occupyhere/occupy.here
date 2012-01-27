
local filename = "archive/videos/" .. forum.request.get.file

content_type('video/mp4')
io.write("Content-Length: " .. lfs.attributes(filename, 'size') .. "\r\n")
io.write("Accept-Ranges: bytes\r\n")
end_headers()

--content_type('text/html')
--end_headers()

--io.write(filename)


--io.write("archive/videos/" .. forum.get.file)

local input = assert(io.open(filename, "rb"))
local block = 1024
while true do
  local bytes = input:read(block)
  if not bytes then break end
  io.write(bytes)
end
