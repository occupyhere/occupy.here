var data;

$(window).addEvent('domready', function() {
  $$('.toggle').each(function(el) {
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
    this.setup();
  },
  
  setup: function() {
    var trigger = $('upload_file').getElement('a.upload_file');
    this.position = $('upload_file').getPosition();
    this.size = trigger.getSize();
    window.addEvent('mousemove', this.mouseMove.bind(this));
    var fileInput = this.el.getElement('input[type=file]');
    fileInput.setStyle('opacity', 0);
    fileInput.addEvent('change', function() {
      fileInput.getParent('form').submit();
    });
  },
  
  mouseMove: function(e) {
    var fileInput = this.el.getElement('input[type=file]');
    if (e.page.x > this.position.x &&
        e.page.x < this.position.x + this.size.x &&
        e.page.y > this.position.y &&
        e.page.y < this.position.y + this.size.y) {
      fileInput.setStyles({
        left: e.page.x - this.position.x - 180,
        top: e.page.y - this.position.y - 10
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
  }
  
});
