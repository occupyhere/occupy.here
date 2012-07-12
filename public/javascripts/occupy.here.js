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
    if ($('user').get('data-username') == '') {
      $('new_message_form').getElement('.username').removeClass('hidden');
    }
    this.el.addEvent('submit', function(e) {
      if (this.el.getElement('.content').value == '') {
        new Event(e).stop();
        alert('Your message is empty!');
      }
    }.bind(this));
  }
  
});
