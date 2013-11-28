function isTouchUI() {
  return !!('ontouchstart' in window)
         || !!('onmsgesturechange' in window);
}

function normalizeHue(hue) {
  if (hue > 359) {
    hue = hue % 360;
  } else if (hue < 0) {
    hue = hue += 360;
  }
  return hue;
}

function setupColorEditors(el) {
  el.getElements('.color-editor').each(function(editor) {
    var handle = editor.getElement('.handle');
    var dragging = false;
    var offset = 0;
    var origin = null;
    var div = $('username-form').getElement('.color');
    if (editor.get('id') == 'inner-color') {
      var style = 'background-color';
    } else {
      var style = 'border-color';
    }
    var hex = div.getStyle(style).match(/#\w{6}/)[0];
    var color = new Color(hex);
    var hue = color.hsb[0];
    var downEvent = isTouchUI() ? 'touchstart' : 'mousedown';
    var upEvent = isTouchUI() ? 'touchend' : 'mouseup';
    var moveEvent = isTouchUI() ? 'touchmove' : 'mousemove';
    
    handle.addEvent(downEvent, function(e) {
      dragging = true;
      origin = e.page.x;
    });
    document.addEvent(upEvent, function(e) {
      if (dragging) {
        dragging = false;
        hue = normalizeHue(hue + offset);
        color.setHue(hue);
        div.setStyle(style, color);
        editor.setStyle('background-color', color.hex);
        editor.getElement('input').value = hue;
      }
    });
    handle.addEvent(moveEvent, function(e) {
      if (dragging) {
        offset = e.page.x - origin;
        var h = normalizeHue(hue + offset);
        color = color.setHue(h);
        div.setStyle(style, color);
        editor.setStyle('background-color', color.hex);
        if (style == 'background-color') {
          var bg = color.setBrightness(33);
          editor.getParent('article').getElement('.container').setStyle('background-color', bg.hex);
        } else {
          var border = color.setBrightness(50);
          $('post-form').setStyle('border-color', border.hex);
          $('post-form').getElement('.buttons').setStyle('background-color', border.hex);
        }
      }
    });
  });
}

function updateAttachForm(e) {
  var position = $('attach-button').getPosition();
  var size = $('attach-button').getSize();
  var isVisible = $('post-form').getParent('form').getSize().y > 0;
  var isDisabled = $('attach-button').hasClass('disabled');
  if (e && e.page &&
      e.page.x > position.x &&
      e.page.x < position.x + size.x &&
      e.page.y > position.y &&
      e.page.y < position.y + size.y &&
      isVisible && !isDisabled) {
    $('upload-form').setStyles({
      left: e.page.x - 180,
      top: e.page.y - 10
    });
    $('attach-button').addClass('hover');
  } else {
    $('upload-form').setStyles({
      left: -1000000,
      top: -1000000
    });
    $('attach-button').removeClass('hover');
  }
}

function setupAttachmentInput() {
  var input = $('upload-form').getElement('input[type=file]');
  if (!input || !$('attach-button')) {
    return;
  }
  if (input.disabled) {
    $('attach-button').destroy();
    input.destroy();
    return;
  }
  if ($('attach-button').hasClass('setup')) {
    return;
  }
  $('attach-button').addClass('setup');
  document.addEvent('mousemove', updateAttachForm);
  input.addEvent('change', function() {
    var maxSize = $('upload-form').getElement('input[name=max_file_size]').value.toInt();
    if (input.files && input.files[0].size &&
        input.files[0].size > maxSize) {
        alert('Sorry, that file is too big to upload. Your file size: ' + getSize(input.files[0].size) + '. Maximum accepted size: ' + getSize(maxSize));
      return;
    }
    $('attach-button').set('html', 'Uploading...');
    $('attach-button').addClass('disabled');
    $('attach-button').disabled = true;
    $('upload-form').submit();
  });  
}

function upload_complete(json) {
  var file = JSON.decode(json);
  new Element('a', {
    href: file.path,
    'class': 'attachment',
    html: file.name,
    target: '_blank'
  }).inject($('post-form').getElement('.container'));
  $('attachment').value = file.id;
  $('attach-button').set('html', 'Attached');
  if ($('post-form').getElement('textarea').value == '') {
    $('post-form').getElement('textarea').value = file.name;
  }
}

function summarizeArticleText(el) {
  el.getElements('article.summary').each(function(article) {
    var text = article.getElement('.text-only');
    if (text) {
      text.removeClass('hidden');
      var orig = text.get('text').trim();
      var length = orig.length;
      if (text.getSize().y <= 48 || (document.body.hasClass('topic') && article.hasClass('topic'))) {
        text.destroy();
        article.removeClass('summary');
        return;
      }
      while (text.getSize().y > 48) {
        length -= 8;
        text.set('html', orig.substr(0, length) + '&nbsp;&hellip;');
      }
      while (text.getSize().y < 72) {
        length += 4;
        text.set('html', orig.substr(0, length) + '&nbsp;&hellip;');
      }
      while (text.getSize().y > 48) {
        length -= 1;
        text.set('html', orig.substr(0, length) + '&nbsp;&hellip;');
      }
      text.addClass('summarized');
    }
  });
  el.getElements('article.post').each(function(el) {
    if (el.hasClass('topic')) {
      return;
    }
    var container = el.getElement('.container');
    container.addEvent('click', function(e) {
      if (!e.target.hasClass('attachment')) {
        window.location = el.getElement('.permalink').get('href');
      }
    });
  });
}

function showPDF(path) {
  PDFJS.getDocument('/' + path).then(function(pdf) {
    pdfSetupPagination(pdf);
    pdfShowPage(pdf, 1);
  });
}

function pdfShowPage(pdf, pg) {
  pdf.getPage(pg).then(function(page) {
    $('pdf-loading').addClass('hidden');
    var frame = $$('.frame')[0];
    var canvas = $('pdf');
    var width = frame.getSize().x - 12;
    var scale = width / (page.view[2] - page.view[0]);
    var height = (page.view[3] - page.view[1]) * scale;
    
    canvas.width = width;
    canvas.height = height;
    var viewport = page.getViewport(scale);
    
    page.render({
      canvasContext: canvas.getContext('2d'),
      viewport: viewport
    });
  });
}

function pdfSetupPagination(pdf) {
  var page = 1;
  var status = new Element('span', {
    id: 'pdf-status',
    html: '<a href="#" class="prev disabled">&larr;</a>' +
          ' <span id="pdf-page">1</span> / ' + pdf.numPages + ' ' +
          '<a href="#" class="next">&rarr;</a>'
  });
  status.inject($('inline-attachment'));
  var next = status.getElement('.next');
  var prev = status.getElement('.prev');
  next.addEvent('click', function(e) {
    e.stop();
    if (page != pdf.numPages) {
      page++;
      $('pdf-page').set('html', page);
      if (page == pdf.numPages) {
        next.addClass('disabled');
      }
      prev.removeClass('disabled');
      pdfShowPage(pdf, page);
    }
  });
  prev.addEvent('click', function(e) {
    e.stop();
    if (page > 1) {
      page--;
      $('pdf-page').set('html', page);
      if (page == 1) {
        prev.addClass('disabled');
      }
      next.removeClass('disabled');
      pdfShowPage(pdf, page);
    }
  });
}

function showTopicForm() {
  $('topic-form').setStyles({
    visibility: 'hidden'
  });
  $('topic-form').addClass('visible');
  var height = $('topic-form').getSize().y;
  $('topic-form').setStyle('height', 0);
  $('topic-form').setStyle('visibility', 'visible');
  $('topic-form').tween('height', height).retrieve('tween').chain(function() {
    setupAttachmentInput();
  });
  $('post-button').set('html', '<span class="icon"></span> CANCEL');
  new Fx.Scroll(window, {
    duration: 500,
    transition: Fx.Transitions.Quart.easeOut,
  }).toElement($('top'));
}

function hideTopicForm() {
  var height = $('topic-form').getSize().y;
  $('topic-form').setStyle('height', height);
  $('topic-form').tween('height', 0);
  $('post-button').set('html', '<span class="icon"></span> POST');
}

window.addEvent('domready', function() {
  $('menu').set('tween', {
    duration: 333,
    transition: Fx.Transitions.Quart.easeOut,
  });
  var menuSlide = new Fx.Slide($('menu'), {
    duration: 750,
    transition: Fx.Transitions.Quart.easeOut,
    onStart: function() {
      if (this.open) {
        $('menu').fade('out');
      }
    },
    onComplete: function() {
      $('menu').fade('show');
    }
  }).hide();
  var windowScroll = new Fx.Scroll(window, {
    duration: 500,
    transition: Fx.Transitions.Quart.easeOut,
  });
  $('menu').removeClass('hidden');
  $('menu-button').addEvent('click', function(e) {
    e.stop();
    menuSlide.toggle();
    windowScroll.start(0, 0);
  });
  
  if ($('pdf')) {
    showPDF($$('.attachment')[0].get('href'));
  }
  
  if ($('topic-form')) {
    $('topic-form').set('tween', {
      duration: 500,
      transition: Fx.Transitions.Quart.easeOut,
      onComplete: function() {
        if ($('topic-form').getSize().y < 1) {
          $('topic-form').removeClass('visible');
        } else {
          $('topic-form').getElement('textarea').focus();
        }
        $('topic-form').setStyle('height', 'auto');
        $('post-button').style.display = 'none';
        $('post-button').offsetHeight;
        $('post-button').style.display = 'block';
      }
    });
    if (location.hash.indexOf('post') != -1) {
      showTopicForm();
    }
    $('post-button').addEvent('click', function(e) {
      e.stop();
      if (!$('topic-form').hasClass('visible')) {
        showTopicForm();
      } else {
        hideTopicForm();
      }
    });
    if ($('edit-options')) {
      $('edit-options').addEvent('click', function(e) {
        e.stop();
        $('options-form').removeClass('hidden');
        $('edit-options').addClass('hidden');
        updateAttachForm();
      });
    }
  }
  
  if ($('post-form')) {
    var textarea = $('post-form').getElement('textarea');
    textarea.addEvent('keyup', function() {
      if (textarea.getScrollSize().y != textarea.getSize().y) {
        textarea.setStyle('height', textarea.getScrollSize().y);
      }
    });
    $('post-form').getParent('form').addEvent('submit', function(e) {
      if (textarea.value == '') {
        e.stop();
      }
    });
    var container = $('post-form').getElement('select[name=container]');
    if (container) {
      var length = container.options.length;
      container.addEvent('change', function() {
        if (container.selectedIndex == container.options.length - 1) {
          var name = prompt('Contaner name?');
          if (!name) {
            container.selectedIndex = 0;
          } else {
            container.options[length - 1] = new Option(name, name);
            container.options[length] = new Option('[New container...]', 'new');
            container.selectedIndex = length - 1;
          }
        }
      });
    }
  }
  
  if ($('edit-username')) {
    $('edit-username').addEvent('click', function(e) {
      e.stop();
      $('post-form').getElement('.author').addClass('hidden');
      $('username-form').removeClass('hidden');
      updateAttachForm();
    });
  }
  
  if ($('edit-colors')) {
    $('edit-colors').addEvent('click', function(e) {
      e.stop();
      $('edit-colors').addClass('hidden');
      $('color-form').removeClass('hidden');
      updateAttachForm();
    });
  }
  
  $$('article, .file, body').each(function(article) {
    var user = null;
    if (article.get('class')) {
      user = article.get('class').match(/user_\w+/);
    }
    if (user) {
      userCssId = user[0];
      if (!$(userCssId)) {
        var colors = article.get('data-colors').split(',');
        var vars = {
          selector: '.' + user[0],
          color1: new Color($HSB(colors[0], 80, 100)).hex,
          color2: new Color($HSB(colors[1], 80, 100)).hex,
          color3: new Color($HSB(colors[0], 80, 33)).hex,
          color4: new Color($HSB(colors[1], 80, 50)).hex,
          color5: new Color($HSB(colors[1], 50, 100)).hex
        };
        var style = new Element('style', {
          id: userCssId,
          html: ("{selector} .id .color { background: {color1}; border-color: {color2}; }\n" +
                 "{selector} .author a:hover { color: {color5}; }\n" +
                 "{selector} #username-form .color { background: {color1}; border-color: {color2}; }\n" +
                 "{selector} #inner-color { background: {color1}; }\n" +
                 "{selector} #outer-color { background: {color2}; }\n" +
                 "{selector} .replies-arrow { background-color: {color2}; }\n" +
                 "{selector} .replies-posts { background-color: {color1}; border-color: {color2}; }\n" +
                 "{selector} .reply-button { border-color: {color2}; }\n" +
                 "{selector} .reply-button:hover { background-color: {color4}; }\n" +
                 "{selector} .container:hover { background-color: {color3}; }\n" +
                 "article{selector}.topic .container { background-color: {color3}; }\n" +
                 "#post-form{selector} .container { background-color: {color3}; }\n" +
                 "#post-form{selector} { border-bottom: 3px solid {color4}; }\n" +
                 "#post-form{selector} .buttons { background-color: {color4}; }\n" +
                 "body{selector} h2.user { border-color: {color4}; background-color: {color4}; }\n" +
                 "{selector} .attachment:hover { background-color: {color4}; }\n" +
                 "body{selector} #content a { color: {color2}; }\n" +
                 "body{selector} #content a:hover { color: {color5}; }\n").substitute(vars)
        }).inject(document.body);
      }
    }
    if (article.getElement('.color-editor')) {
      setupColorEditors(article);
    }
    if ($('reply')) {
      setupAttachmentInput();
    }
  });
  
  $$('.announcement').each(function(el) {
    el.getElements('.close').each(function(link) {
      link.addEvent('click', function(e) {
        e.stop();
        var id = el.get('id').match(/(.+)_announcement/)[1];
        var hidden = Cookie.read('hidden_announcements') || '';
        hidden = hidden.split(',');
        hidden.push(id);
        hidden = hidden.join(',');
        Cookie.write('hidden_announcements', hidden, {
          duration: 365
        });
        el.destroy();
        if ($('announcements').getElements('.announcement').length == 0) {
          $('announcements').destroy();
        }
      });
    });
    el.getElements('.more').addEvent('click', function(e) {
      e.stop();
      el.removeClass('minimized');
    });
  });
  
  function resize() {
    if (window.getSize().x < 590) {
      $$('#article img').each(function(img) {
        if (img.get('width') == '520') {
          var ratio = parseInt(img.get('width')) / parseInt(img.get('height'));
          img.setStyle('width', window.getSize().x);
          img.setStyle('height', Math.round(window.getSize().x / ratio));
          img.setStyle('margin-left', -15);
        }
      });
    } else {
      $$('#article img').each(function(img) {
        if (img.get('width') == '520') {
          var ratio = parseInt(img.get('width')) / parseInt(img.get('height'));
          img.setStyle('width', 520);
          img.setStyle('height', Math.round(520 / ratio));
          img.setStyle('margin-left', 0);
        }
      });
    }
  }
  window.addEvent('resize', resize);
  resize();
  
  if ($('backup_form')) {
    $('backup_form').addEvent('submit', function(e) {
      var file = $('backup_form').getElement('input[name=file]');
      if (file.value == '') {
        e.stop();
        var button = $('backup_form').getElement('.button');
        button.disabled = true;
        button.addClass('disabled');
        button.value = 'Loading...';
        new Request({
          url: '/admin/backup_download',
          onComplete: function(response) {
            var response = JSON.parse(response);
            if (response.status == 'ok' && response.file) {
              file.value = response.file;
              $('backup_form').submit();
            } else if (response.status == 'error' && response.output) {
              alert('Error: ' + response.output);
            } else {
              alert('Oops, something went wrong.');
            }
          }
        }).post();
      }
    });
  }
  
  if ($('update_ssid')) {
    var form = $('update_ssid').getElement('form');
    form.addEvent('submit', function(e) {
      e.stop();
      var ssid = $('ssid').value;
      new Request({
        url: '/admin/update_ssid'
      }).post({
        ssid: ssid
      });
      alert('Your wifi network name has been updated. You need to reconnect to the new wifi network called “' + $('ssid').value + '”.');
    });
  }
  
  $$('.delete').each(function(link) {
    link.addEvent('click', function(e) {
      e.stop();
      if (confirm('Are sure you want to delete that?')) {
        new Request({
          url: '/admin/delete_post',
          onComplete: function() {
            link.getParent('.post').destroy();
          }
        }).post({
          id: link.get('data-id')
        });
      }
    });
  });
  
  var serverTime = $(document.body).get('data-servertime');
  if (serverTime) {
    serverTime = new Date(serverTime.toInt() * 1000);
    var clientTime = Math.round(new Date().getTime() / 1000);
    if (serverTime.getFullYear() < 2012) {
      new Request({
        url: 'api/set_time'
      }).post({
        time: clientTime
      });
    }
  }
  
});

function backup_complete() {
  var file = $('backup_form').getElement('input[name=file]');
  var button = $('backup_form').getElement('.button');
  file.value = '';
  button.disabled = null;
  button.removeClass('disabled');
  button.value = 'Download backup';
}

window.addEvent('load', function() {
  summarizeArticleText($('page'));
});
