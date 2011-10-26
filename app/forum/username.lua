
local task = forum.request.post.task
if task == 'save' then
  set_cookie('author', forum.request.post.author)
  redirect(forum.cgi)
else
  content_type('text/html')
  end_headers()
  include("html/header.html", {
    class = 'username',
    title = forum.forum_title,
    description = forum.forum_description,
    username = username_html()
  })
  include('html/username_form.html', {
    author = get_cookie('author', 'Anonymous')
  })
  include("html/footer.html")
end
