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
      dragging = false;
      hue = normalizeHue(hue + offset);
      color.setHue(hue);
      div.setStyle(style, color);
      editor.setStyle('background-color', color.hex);
    });
    handle.addEvent(moveEvent, function(e) {
      if (dragging) {
        offset = e.page.x - origin;
        var h = normalizeHue(hue + offset);
        color = color.setHue(h);
        div.setStyle(style, color);
        editor.setStyle('background-color', color.hex);
      }
    });
  });
}

function summarizeArticleText(el) {
  el.getElements('article.summary').each(function(article) {
    var text = article.getElement('.text-only');
    if (text) {
      text.fade('hide');
      text.removeClass('hidden');
      var orig = text.get('text').trim();
      var length = orig.length;
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
      text.fade('in');
      text.addClass('summarized');
    }
    var container = article.getElement('.container');
    container.addEvent('click', function() {
      if (article.hasClass('summary')) {
        article.removeClass('summary');
      }
    });
  });
}

window.addEvent('domready', function() {
  
  $('post-button').addEvent('click', function(e) {
    e.stop();
    $('post-form').set('tween', {
      duration: 500,
      transition: Fx.Transitions.Quart.easeOut,
      onComplete: function() {
        if ($('post-form').getSize().y < 1) {
          $('post-form').removeClass('visible');
        }
        $('post-form').setStyle('height', 'auto');
        $('post-button').style.display = 'none';
        $('post-button').offsetHeight;
        $('post-button').style.display = 'block';
      }
    });
    if (!$('post-form').hasClass('visible')) {
      $('post-form').setStyles({
        visibility: 'hidden'
      });
      $('post-form').addClass('visible');
      var height = $('post-form').getSize().y;
      $('post-form').setStyle('height', 0);
      $('post-form').setStyle('visibility', 'visible');
      $('post-form').tween('height', height);
      $('post-button').set('html', '<span class="icon"></span> CANCEL');
    } else {
      var height = $('post-form').getSize().y;
      $('post-form').setStyle('height', height);
      $('post-form').tween('height', 0);
      $('post-button').set('html', '<span class="icon"></span> POST');
    }
  });
  
  var textarea = $('post-form').getElement('textarea');
  textarea.addEvent('keyup', function() {
    if (textarea.getScrollSize().y != textarea.getSize().y) {
      textarea.setStyle('height', textarea.getScrollSize().y);
    }
  });
  
  $('edit-username').addEvent('click', function(e) {
    e.stop();
    $('post-form').getElement('.author').addClass('hidden');
    $('username-form').removeClass('hidden');
  });
  
  $('edit-colors').addEvent('click', function(e) {
    e.stop();
    $('edit-colors').addClass('hidden');
    $('color-form').removeClass('hidden');
  });
  
  $$('article').each(function(article) {
    var user = article.get('class').match(/user\w+/);
    if (user) {
      userCssId = 'css-' + user[0];
      if (!$(userCssId)) {
        var vars = {
          selector: '.' + user[0],
          color1: article.get('data-color1'),
          color2: article.get('data-color2'),
          color3: new Color(article.get('data-color1')).setBrightness(33).hex,
          color4: new Color(article.get('data-color2')).setBrightness(50).hex
        };
        var style = new Element('style', {
          id: userCssId,
          html: ("{selector} .author .id .color { background: {color1}; border-color: {color2}; }\n" +
                 "{selector} #username-form .color { background: {color1}; border-color: {color2}; }\n" +
                 "{selector} #inner-color { background: {color1}; }\n" +
                 "{selector} #outer-color { background: {color2}; }\n" +
                 "{selector} .replies-arrow { background-color: {color2}; }\n" +
                 "{selector} .replies-posts { background-color: {color1}; border-color: {color2}; }\n" +
                 "{selector} .reply-button { border-color: {color2}; }\n" +
                 "{selector} .reply-button:hover { background-color: {color4}; }\n" +
                 "{selector}.summary .container:hover { background-color: {color3}; }\n").substitute(vars)
        }).inject(document.body);
      }
    }
    if (article.getElement('.color-editor')) {
      setupColorEditors(article);
    }
  });
  
});

window.addEvent('load', function() {
  summarizeArticleText($('page'));
});
