
function include(filename, vars)
  t = template(filename, vars)
  io.write(t)
end

function template(filename, vars)
  vars = vars or {}
  vars.public_root = forum.public_root
  local f = assert(io.open(filename, "r"))
  local t = f:read("*all")
  f:close()
  -- The following is based on a technique used by MooTools's String.substitute
  -- template function
  t = string.gsub(t, "\{([^{}]+)\}", function(name)
    if vars[name] ~= nil then
      return vars[name]
    else
      return ""
    end
  end)
  return t
end

function show_post(filename)
  local f = assert(io.open(filename, "r"))
  local t = f:read("*all")
  local vars = json.decode(t)
  if string.find(filename, "replies", 0, true) then
    io.write(reply_html(vars))
  else
    io.write(topic_html(vars))
  end
end

function select_posts(posts, offset, number, reverse, callback)
  
  -- I bet this could be implemented better...
  
  -- Reverse all the posts (if necessary)
  if reverse then
    table.sort(posts, function(a, b)
      return (b < a)
    end)
  end
  
  -- Iterate over each post and grab the ones that fall in the desired range
  local filtered = {}
  for i,n in ipairs(posts) do
    if offset > 0 then
      offset = offset - 1
    elseif number == 0 then
      break
    else
      table.insert(filtered, n)
      number = number - 1
    end
  end
  
  -- Pass each filename to the callback function
  for i,n in ipairs(filtered) do
    callback(n)
  end
end

function get_posts(dir)
  -- Grab all the JSON filenames in a given directory
  local posts = {}
  local n = 0
  local id
  for file in lfs.dir(dir) do
    if string.find(file, ".json", 0, true) then
      table.insert(posts, file)
    end
  end
  return posts
end

function normalize_id(id)
  -- this is from when I was using 00001 type file naming, now it's vestigial
  return id
end

function topic_html(post)
  post.id_attr = 'post-' .. post.timestamp
  post = sanitize_post(post)
  return template("html/post.html", post)
end

function reply_html(post)
  post.id_attr = 'reply-' .. post.timestamp
  post = sanitize_post(post)
  return template("html/post.html", post)
end

function sanitize_post(post)
  local html = sanitize(post.content)
  html = string.gsub(html, "\n", "<br />")
  post.content = html
  post.author = sanitize(post.author)
  return post
end

-- From Lua Web Server API (WSAPI)
-- http://keplerproject.github.com/wsapi/
function parse_qs(qs, tab, overwrite)
  tab = tab or {}
  if type(qs) == "string" then
    for key, val in string.gmatch(qs, "([^&=]+)=([^&=]*)&?") do
      insert_field(tab, url_decode(key), url_decode(val), overwrite)
    end
  elseif qs then
    error("Request error: invalid query string")
  end
  return tab
end


-- From Lua Web Server API (WSAPI)
-- http://keplerproject.github.com/wsapi/
function insert_field(tab, name, value, overwrite)
  if overwrite or not tab[name] then
    tab[name] = value
  else
    local t = type (tab[name])
    if t == "table" then
      table.insert (tab[name], value)
    else
      tab[name] = { tab[name], value }
    end
  end
end

-- From Lua Web Server API (WSAPI)
-- http://keplerproject.github.com/wsapi/
function url_decode(str)
  if not str then return nil end
  str = string.gsub (str, "+", " ")
  str = string.gsub (str, "%%(%x%x)", function(h) return string.char(tonumber(h,16)) end)
  str = string.gsub (str, "\r\n", "\n")
  return str
end

-- From Lua Web Server API (WSAPI)
-- http://keplerproject.github.com/wsapi/
function url_encode(str)
  if not str then return nil end
  str = string.gsub (str, "\n", "\r\n")
  str = string.gsub (str, "([^%w ])",
        function (c) return string.format ("%%%02X", string.byte(c)) end)
  str = string.gsub (str, " ", "+")
  return str
end

-- From Lua Web Server API (WSAPI)
-- http://keplerproject.github.com/wsapi/
function sanitize(text)
  return htmlspecialchars(text)
--  return text:gsub(">", "&gt;"):gsub("<", "&lt;")
end

-- More complete sanitizer, similar to the PHP function
function htmlspecialchars(text)
  text = text:gsub("&", "&amp;")
  text = text:gsub("\"", "&quot;")
  text = text:gsub("'", "&039;")
  text = text:gsub("<", "&lt;")
  text = text:gsub(">", "&gt;")
  return text
end

function content_type(value)
  io.write("Content-Type: " .. value .. "\r\n")
end

function redirect(url)
  -- Using a dumb redirect because OpenWRTs uhttpd server only seems capable of
  -- serving HTTP status 200
  -- start_headers("302 Found")
  -- io.write("Location: " .. forum_cgi_base .. url .. "\r\n")
  content_type("text/html")
  end_headers()
  io.write('<html><head><meta http-equiv="refresh" content="0; URL=' .. url .. '" /></head><body></body></html>')
end

function set_cookie(key, value)
  local one_year_hence = os.time() + 60 * 60 * 24 * 365
  local expires = os.date("%a, %d %b %Y %H:%M:%S EST", one_year_hence)
  io.write("Set-Cookie: " .. url_encode(key) .. "=" .. url_encode(value) .. "; Expires=" .. expires .. "\r\n")
end

function get_cookie(key, default_value)
  local header = os.getenv('HTTP_COOKIE') or ''
  local cookies = string.gsub(";" .. header .. ";", "%s*;%s*", ";")
  local pattern = ";" .. key .. "=(.-);"
  local cookie = string.match(cookies, pattern)
  return url_decode(cookie) or default_value
end

function start_headers(status)
  io.write("HTTP/1.1 " .. status .. "\r\n")
end

function end_headers()
  io.write("\r\n")
end
