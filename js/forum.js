window.addEvent('domready', function() {
  var $ = document.id;
  if ($(document.body).hasClass('home')) {
    var queue = [];
    $$('.comments a').each(function(link) {
      queue.push({
        link: link,
        url: link.get('href').replace('topic', 'comments')
      });
    });
    getComments(queue);
  }
  if ($$('input[name=author]').length > 0) {
    var input = $$('input[name=author]')[0];
    input.addEvent('blur', function() {
      Cookie.write('author', input.value, {
        duration: 365
      });
    });
  }
  if ($$('form').length > 0) {
    $$('form')[0].addEvent('submit', function(e) {
      if ($$('input[name=author]')[0].value == '') {
        $$('input[name=author]')[0].value = 'Anonymous';
      }
      if ($$('textarea')[0].value == '') {
        new Event(e).stop();
      }
    });
  }
  if ($('first_comment')) {
    $('first_comment').setStyle('visibility', 'visible');
    $('first_comment').setStyle('position', 'static');
    var slide = new Fx.Slide($('first_comment')).hide();
    $('add_first_comment').addEvent('click', function(e) {
      new Event(e).stop();
      slide.toggle();
      if (!slide.open) {
        $('add_first_comment').set('html', 'hide first comment');
      } else {
        $('add_first_comment').set('html', 'show first comment');
      }
    });
    if ($$('textarea[name=first_comment]')[0].value != '') {
      slide.show();
      $('add_first_comment').set('html', 'hide first comment');
    }
  }
  var now = new Date();
  if ($('timestamp')) {
    $('timestamp').value = now.getTime();
    $('time').value = now.format('%I:%M %p');
    $('date').value = now.format('%b %e, %Y');
  }
});

function getComments(queue) {
  if (queue.length > 0) {
    var target = queue.shift();
    new Request({
      url: target.url,
      method: 'get',
      onSuccess: function(comments) {
        target.link.set('html', comments);
      }
    }).send();
    return getComments(queue);
  }
}
