var ManyCopies = new Class({
  
  servers: {},
  objects: {},
  updateFrequency: 2 * 60 * 1000, // update every two minutes
  
  initialize: function() {
    if (!localStorage) {
      return;
    }
    if (localStorage.manyCopies) {
      try {
        var stored = JSON.decode(localStorage.manyCopies);
        this.objects = stored.objects || {};
        this.servers = stored.servers || {};
      } catch (e) {
        // TODO: alert the user
      }
    }
    if (this.readyToUpdate()) {
      this.syncWithServer();
    }
  },
  
  readyToUpdate: function() {
    return true;
    var timestamp = new Date().getTime();
    return (!this.lastUpdated ||
            timestamp - this.lastUpdated > this.updateFrequency);
  },
  
  syncWithServer: function() {
    new Request({
      url: 'api/sync_data',
      onComplete: this.handleSyncResponse.bind(this)
    }).post({
      last_updated: JSON.encode(this.servers)
    });
  },
  
  handleSyncResponse: function(json) {
    var response = JSON.decode(json);
    var now = Math.round(new Date().getTime() / 1000);
    response.objects.each(function(obj) {
      var existing = this.objects[obj.id];
      if (!existing) {
        this.objects[obj.id] = obj;
      } else if (existing.created == obj.created) {
        this.objects[obj.id] = obj;
      } else if (existing.created < obj.created) {
        this.objects[obj.id].updated = now;
      }
    }.bind(this));
    this.servers[response.server_id] = now;
    this.updateLocalStorage();
    var staleServers = this.findStaleServers(response.server_id, response.last_updated);
    if (staleServers.length > 0) {
      this.syncObjectsToServer(staleServers, response.last_updated);
    }
  },
  
  updateLocalStorage: function() {
    var json = JSON.encode({
      objects: this.objects,
      servers: this.servers
    });
    localStorage.manyCopies = json;
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
    }.bind(this));
    
    new Request({
      url: 'api/sync_data'
    }).post({
      servers: JSON.encode(staleServers),
      objects: JSON.encode(objects)
    });
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
