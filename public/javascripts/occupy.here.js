var data;

$(window).addEvent('domready', function() {
  $$('.slide-toggle').each(function(el) {
    el.addEvent('click', function(e) {
      new Event(e).stop();
      var id = el.get('href').match(/#(.+)/)[1];
      var slide = $(id).retrieve('slide');
      if (slide) {
        slide.toggle();
      } else {
        $(id).toggleClass('hidden');
      }
    });
  });
  $(document.body).removeClass('loading');
  if ($('introduction')) {
    $('introduction').retrieve('slide').addEvent('complete', function() {
      if (!this.open) {
        $('introduction-more').removeClass('hidden');
      }
    });
    $('introduction-more').addEvent('click', function() {
      $('introduction-more').addClass('hidden');
    });
  }
  $$('.message_form').each(function(el) {
    new MessageForm(el);
  });
  if ($('upload_file')) {
    new UploadForm($('upload_file'));
  }
  if ($('backup_form')) {
    new BackupForm($('backup_form'));
  }
  $$('textarea, input[type=text]').each(function(el) {
    var w = el.getParent().getStyle('width').toInt();
    var pl = el.getStyle('padding-left').toInt();
    var pr = el.getStyle('padding-right').toInt();
    el.setStyle('width', w - pl - pr);
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
  
  data = new ManyCopies();
  
});

var MessageForm = new Class({
  
  initialize: function(el) {
    this.el = el;
    this.setup();
  },
  
  setup: function() {
    this.el.addEvent('submit', function(e) {
      if (this.el.getElement('.content').value == '') {
        new Event(e).stop();
        alert('Your message is empty!');
      }
    }.bind(this));
    this.el.getElement('input.preview').addEvent('click', this.preview.bind(this));
  },
  
  preview: function() {
    if (this.el.getElement('.content').value == '') {
      alert('Your message is empty!');
      return;
    }
    var message = {
      content: this.el.getElement('textarea[name=content]').value
    };
    if (this.el.getElement('.username')) {
      message.username = this.el.getElement('.username input').value;
    }
    new Request({
      url: '/api/preview',
      onComplete: function(response) {
        var preview = this.el.getElement('.post.preview');
        if (!preview) {
          preview = new Element('div', {
            'class': 'post preview'
          });
          preview.inject(this.el);
        }
        preview.set('html', response);
        var parent = this.el.getParent();
        parent.setStyle('height', 'auto');
        parent.setStyle('height', parent.getSize().y);
      }.bind(this)
    }).post(message);
  }
  
});

var UploadForm = new Class({
  
  initialize: function(el) {
    this.el = el;
    el.store('object', this);
    this.disable();
    this.setup();
  },
  
  setup: function() {
    var fileInput = this.el.getElement('input[type=file]');
    if (fileInput.disabled) {
      $('upload_file').destroy();
    } else {
      fileInput.addEvent('change', function() {
        var maxSize = this.el.getElement('input[name=max_file_size]').value.toInt();
        if (fileInput.files && fileInput.files[0].size &&
            fileInput.files[0].size > maxSize) {
            alert('Sorry, that file is too big to upload. Your file size: ' + getSize(fileInput.files[0].size) + '. Maximum accepted size: ' + getSize(maxSize));
          return;
        }
        if (fileInput.files && fileInput.files[0].size) {
          var size = getSize(fileInput.files[0].size);
          this.el.getElement('.file_label').set('html', 'Uploading ' + size);
        } else {
          this.el.getElement('.file_label').set('html', 'Uploading...');
        }
        fileInput.getParent('form').submit();
      }.bind(this));
      this.setupTouchControls();
      window.addEvent('mousemove', this.mouseMove.bind(this));
    }
  },
  
  setupTouchControls: function() {
    var trigger = $('upload_file').getElement('a.upload_file');
    this.toggleHandler = function(e) {
      new Event(e).stop();
      var slide = $('upload_details').retrieve('slide');
      if (!slide.open) {
        slide.slideIn();
      }
    }.bind(this);
    trigger.addEvent('click', this.toggleHandler);
  },
  
  mouseMove: function(e) {
    var trigger = $('upload_file').getElement('a.upload_file');
    var fileInput = this.el.getElement('input[type=file]');
    if (!this.mouseMoveSetup) {
      this.mouseMoveSetup = true;
      fileInput.setStyles({
        position: 'absolute',
        opacity: 0
      });
      fileInput.inject(fileInput.getParent('form'));
      trigger.removeEvent('click', this.toggleHandler);
      this.el.getElement('.file_label').set('html', '');
    }
    var position = $('upload_file').getPosition();
    var size = trigger.getSize();
    var fileInput = this.el.getElement('input[type=file]');
    if (e.page.x > position.x &&
        e.page.x < position.x + size.x &&
        e.page.y > position.y &&
        e.page.y < position.y + size.y) {
      fileInput.setStyles({
        left: e.page.x - position.x - 180,
        top: e.page.y - position.y - 10
      });
    } else {
      fileInput.setStyles({
        left: -1000000,
        top: -1000000
      });
    }
  },
  
  showDetails: function(filename, original, type) {
    this.el.getElement('input[name=filename]').value = filename;
    this.el.getElement('input[name=original]').value = original;
    this.el.getElement('input[name=type]').value = type;
    this.el.getElement('input[name=name]').value = original;
    var slide = $('upload_details').retrieve('slide');
    slide.slideIn();
    this.enable();
    this.el.getElement('.file_label').set('html', 'File upload complete');
  },
  
  disable: function() {
    this.el.addClass('disabled');
    this.formHandler = function(e) {
      new Event(e).stop();
    };
    this.el.getElement('form.details').addEvent('submit', this.formHandler);
  },
  
  enable: function() {
    this.el.removeClass('disabled');
    this.el.getElement('form.details').removeEvent('submit', this.formHandler);
  }
  
});


var BackupForm = new Class({
  
  initialize: function(el) {
    this.el = el;
    var button = el.getElement('input[type=button]');
    button.addEvent('click', function(e) {
      new Event(e).stop();
      if (button.hasClass('disabled')) {
        return;
      }
      var buttonOrigValue = button.get('value');
      button.addClass('disabled');
      button.set('value', 'archiving...');
      button.set('disabled', 'disabled');
      new Request({
        url: '/api/backup',
        onComplete: function(json) {
          var response = JSON.decode(json);
          if (response.status == 'ok') {
            el.getElement('input[name=file]').value = response.file;
            el.set('action', '/' + response.file);
            el.submit();
          } else if (response.output) {
            var output = '<span class="icon alert"></span>' + response.output;
            if (!$('zip-output')) {
              new Element('p', {
                id: 'zip-output',
                html: output
              }).inject(el);
            } else {
              $('zip-output').set('html', output);
            }
          }
          button.set('value', buttonOrigValue);
          button.removeClass('disabled');
          button.removeAttribute('disabled');
        }
      }).post();
    });
  }
  
});

function getSize(bytes) {
  if (bytes > 1024 * 1024) {
    return (bytes / (1024 * 1024)).round(1) + ' MB';
  } else if (bytes > 1024) {
    return (bytes / (1024)).round(1) + ' KB';
  } else {
    return bytes + ' bytes';
  }
}
