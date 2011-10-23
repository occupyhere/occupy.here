window.addEvent('domready', function() {
  var $ = document.id;
  if ($$('input[name=author]').length > 0) {
    var input = $$('input[name=author]')[0];
    input.addEvent('blur', function() {
      Cookie.write('author', input.value, {
        duration: 365 * 10
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
  if ($('id') && $('time') && $('date')) {
    var now = new Date();
    $('id').value = now.getTime();
    $('time').value = now.format('%I:%M %p');
    $('date').value = now.format('%b %e, %Y');
  }
  var data = new LocalData();
  if (data.itsPeanutButterJellyTime()) {
    data.check();
  }
});

var LocalData = new Class({
  
  topics: {},
  replies: {},
  updateFrequency: 0,
  
  initialize: function() {
    if (localStorage.topics) {
      this.topics = JSON.decode(localStorage.topics);
    }
    if (localStorage.replies) {
      this.replies = JSON.decode(localStorage.replies);
    }
  },
  
  itsPeanutButterJellyTime: function() {
    var timestamp = new Date().getTime();
    var lastUpdate = parseInt(localStorage.updated, 10);
    return (!localStorage.updated ||                          // never updated
            timestamp - lastUpdate > this.updateFrequency);   // cache expired
  },
  
  check: function() {
    localStorage.updated = new Date().getTime();
    new Request({
      url: '?x=sync_data',
      onComplete: this.handleCheckResponse.bind(this)
    }).post({
      known_topics: Object.keys(this.topics).join(','),
      known_replies: Object.keys(this.replies).join(',')
    });
  },
  
  handleCheckResponse: function(response) {
    var data = JSON.decode(response);
    for (var id in data.topics) {
      if (!this.topics[id]) {
        this.topics[id] = data.topics[id];
      }
    }
    for (var id in data.replies) {
      if (!this.replies[id]) {
        this.replies[id] = data.replies[id];
      }
    }
    localStorage.topics = JSON.encode(this.topics);
    localStorage.replies = JSON.encode(this.replies);
    if (data.send_topics.length > 0 ||
        data.send_replies.length > 0) {
      this.send(data.send_topics, data.send_replies);
    }
  },
  
  send: function(topicIds, replyIds) {
    var topics = [];
    var replies = [];
    topicIds.each(function(id) {
      topics.push(this.topics[id]);
    }.bind(this));
    replyIds.each(function(id) {
      replies.push(this.replies[id]);
    }.bind(this));
    new Request({
      url: '?x=sync_data',
      onComplete: function(response) {
        console.log(response);
      }
    }).post({
      new_topics: JSON.encode(topics),
      new_replies: JSON.encode(replies)
    });
  }
  
});

