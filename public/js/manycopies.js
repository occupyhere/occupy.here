var ManyCopies = new Class({
  
  revision: 0,
  updated: 0,
  servers: {},
  objects: {},
  files: {},
  
  downloadQueue: [],
  uploadQueue: [],
  
  initialize: function() {
    this.parseLocalStorage();
    this.checkForIncompleteDownloads();
    if (this.isEnabled()) {
      this.syncWithServer();
    }
    if ($('many-copies')) {
      this.setupControls();
    }
  },

  parseLocalStorage: function() {
    if (!localStorage) {
      return;
    }
    if (localStorage.manycopies) {
      try {
        var stored = JSON.decode(localStorage.manycopies);
        if (stored.revision) {
          var data = stored['data' + stored.revision];
          this.revision = stored.revision;
          this.updated = stored.updated;
          this.servers = data.servers || {};
          this.objects = data.objects || {};
          this.files = data.files || {};
        }
      } catch (e) {
        // TODO: alert the user
      }
    }
  },
  
  checkForIncompleteDownloads: function() {
    for (var id in this.files) {
      var file = this.files[id];
      if (!file.complete) {
        this.enqueueFileDownload(id);
      }
    }
  },
  
  setupControls: function() {
    var toggle = $('many-copies').getElement('input.toggle');
    var clear = $('many-copies').getElement('input.clear');
    toggle.checked = this.isEnabled();
    toggle.addEvent('change', function() {
      if (toggle.checked) {
        this.enable();
      } else {
        this.disable();
      }
      this.updateStorageStatus();
    }.bind(this));
    clear.addEvent('click', function() {
      if ($('many-copies').hasClass('disabled')) {
        return false;
      } else {
        this.clearContents();
        this.updateStorageStatus();
      }
    }.bind(this));
    this.updateStorageStatus();
  },
  
  enable: function() {
    Cookie.write('manycopies', 'enabled', {
      path: '/',
      duration: 365 * 10
    });
    if ($('many-copies') && $('many-copies').getElement('.clear')) {
      var clear = $('many-copies').getElement('.clear');
      clear.removeAttribute('disabled');
    }
    this.syncWithServer();
  },
  
  disable: function() {
    this.clearContents();
    Cookie.write('manycopies', 'disabled', {
      path: '/',
      duration: 365 * 10
    });
    if ($('many-copies')) {
      var clear = $('many-copies').getElement('.clear');
      clear.set('disabled', 'disabled');
    }
  },
  
  clearContents: function() {
    localStorage.manycopies = '';
  },
  
  isEnabled: function() {
    return (Cookie.read('manycopies') != 'disabled');
  },
  
  updateStorageStatus: function() {
    if ($('many-copies')) {
      var size = $('many-copies').getElement('.size');
      if (this.isEnabled()) {
        $('many-copies').removeClass('disabled');
        size.set('html', 'You are storing: ' + this.getSize());
      } else {
        $('many-copies').addClass('disabled');
        size.set('html', 'Your browser is not storing any data');
      }
    }
  },
  
  getSize: function() {
    var bytes = localStorage.manycopies.length;
    if (bytes > 1024 * 1024) {
      return (bytes / (1024 * 1024)).round(1) + ' MB';
    } else if (bytes > 1024) {
      return (bytes / (1024)).round(1) + ' KB';
    } else {
      return bytes + ' bytes';
    }
  },
  
  syncWithServer: function() {
    new Request({
      url: 'api/sync_data',
      onComplete: this.handleSyncResponse.bind(this)
    }).post({
      revision: this.revision,
      last_updated: JSON.encode(this.servers)
    });
  },
  
  handleSyncResponse: function(json) {
    var response = JSON.decode(json);
    var now = Math.round(new Date().getTime() / 1000);
    this.revision = response.revision;
    response.objects.each(function(obj) {
      var existing = this.objects[obj.id];
      if (!existing) {
        // New object
        this.objects[obj.id] = obj;
        if (obj.id.substr(0, 4) == 'file') {
          this.enqueueFileDownload(obj.id);
        }
      } else if (existing.created == obj.created) {
        // Update an existing object
        this.objects[obj.id] = obj;
      } else if (existing.created < obj.created) {
        // ID conflict? Ours was created first!
        // TODO: handle id collisions...
        this.objects[obj.id].updated = now;
      }
    }.bind(this));
    this.servers[response.server_id] = now;
    this.updated = now;
    var staleServers = this.findStaleServers(response.server_id, response.last_updated);
    if (staleServers.length > 0) {
      this.syncObjectsToServer(staleServers, response.last_updated);
    } else {
      this.syncFiles();
    }
    for (var server_id in response.last_updated) {
      if (!this.servers[server_id] ||
        response.last_updated[server_id] > this.servers[server_id]) {
        this.servers[server_id] = response.last_updated[server_id];
      }
    }
    this.updateLocalStorage();
    if ($('many-copies')) {
      this.updateStorageStatus();
    }
  },
  
  updateLocalStorage: function() {
    var data = {
      revision: this.revision,
      updated: this.updated
    };
    data['data' + this.revision] = {
      objects: this.objects,
      servers: this.servers,
      files: this.files
    };
    var json = JSON.encode(data);
    localStorage.manycopies = json;
  },
  
  findStaleServers: function(serverId, servers) {
    var staleServers = [];
    for (var id in this.servers) {
      if (serverId !== id) {
        if (!servers[id] || this.servers[id] > servers[id]) {
          staleServers.push(id);
        }
      }
    }
    return staleServers;
  },
  
  syncObjectsToServer: function(staleServers, lastUpdated) {
    var objects = [], updatedObjects;
    
    staleServers.each(function(serverId) {
      updatedObjects = this.getUpdatedObjects(serverId, lastUpdated[serverId]);
      objects.append(updatedObjects);
    }.bind(this));
    
    for (var serverId in this.servers) {
      if (!staleServers.contains(serverId)) {
        updatedObjects = this.getUpdatedObjects(serverId, 0);
        objects.append(updatedObjects);
      }
    }
    
    objects.each(function(obj) {
      if (this.objects[obj.user_id]) {
        objects.push(this.objects[obj.user_id]);
      }
      if (obj.id.substr(0, 4) == 'file') {
        this.enqueueFileUpload(obj.id);
      }
    }.bind(this));
    
    new Request({
      url: 'api/sync_data',
      onComplete: this.syncFiles.bind(this)
    }).post({
      revision: this.revision,
      servers: JSON.encode(staleServers),
      objects: JSON.encode(objects)
    });
  },
  
  enqueueFileDownload: function(id) {
    this.downloadQueue.push(id);
    if (!this.files[id]) {
      this.files[id] = {
        data: '',
        complete: false
      };
    }
  },
  
  enqueueFileUpload: function(id) {
    this.uploadQueue.push(id);
  },
  
  syncFiles: function() {
    if (this.downloadQueue.length > 0) {
      this.downloadChunk(this.downloadQueue[0]);
    } else {
      this.checkServerForUploads();
    }
  },
  
  downloadChunk: function(id) {
    new Request({
      url: 'api/sync_data',
      onComplete: this.handleFileChunk.bind(this)
    }).post({
      revision: this.revision,
      download: id,
      offset: this.files[id].data.length
    });
  },
  
  checkServerForUploads: function() {
    var files = [];
    for (var id in this.files) {
      if (this.files[id].complete) {
        files.push(id);
      }
    }
    new Request({
      url: 'api/sync_data',
      onComplete: this.handleUploadRequest.bind(this)
    }).post({
      revision: this.revision,
      files: JSON.encode(files)
    });
  },
  
  handleFileChunk: function(json) {
    var response = JSON.decode(json);
    var file = this.files[response.id];
    if (file) {
      file.data += response.data;
      file.complete = response.complete;
    }
    if (!file || response.complete) {
      this.downloadQueue.erase(response.id);
    }
    try {
      this.updateLocalStorage();
      this.syncFiles();
    } catch (e) {
      // TODO: add feedback
    }
  },
  
  handleUploadRequest: function(json) {
    var response = JSON.decode(json);
    if (response.id) {
      this.uploadChunk(response.id, response.offset, response.size);
    }
  },
  
  uploadChunk: function(id, offset, size) {
    var file = this.files[id];
    if (file) {
      var data = file.data;
      var chunk = data.substr(offset, size);
      var complete = (offset + size >= data.length) ? 1 : 0;
      var uploadNextChunk = function() {
        this.uploadChunk(id, offset + size, size);
      }.bind(this);
      var callback = complete ? this.checkServerForUploads.bind(this) : uploadNextChunk;
      new Request({
        url: 'api/sync_data',
        onComplete: callback
      }).post({
        revision: this.revision,
        upload: id,
        offset: offset,
        data: chunk,
        complete: complete
      });
    }
  },
  
  getUpdatedObjects: function(serverId, lastUpdated) {
    if (!lastUpdated) {
      lastUpdated = 0;
    }
    var updatedObjects = Object.filter(this.objects, function(obj) {
      if (obj.server_id == serverId && obj.updated > lastUpdated) {
        return true;
      }
      return false;
    });
    return Object.values(updatedObjects);
  }
  
});
