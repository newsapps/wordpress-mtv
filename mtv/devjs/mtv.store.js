(function() {
  var $;
  if (!$) {
    $ = jQuery;
  }
  window.MTV.Store = new Object({
    expires: null,
    get: function(key) {
      var json_data;
      if (this.has_local_storage()) {
        json_data = $.cookie(key + '-timer') ? localStorage.getItem(key) : null;
      } else {
        json_data = $.cookie(key);
      }
      if (json_data) {
        return JSON.parse(json_data);
      } else {
        return false;
      }
    },
    save: function(key, val) {
      var json_data;
      json_data = JSON.stringify(val);
      if (this.has_local_storage()) {
        $.cookie(key + '-timer', 'true', {
          expires: this.expires,
          path: '/'
        });
        return localStorage.setItem(key, json_data);
      } else {
        return $.cookie(key, json_data, {
          expires: this.expires,
          path: '/'
        });
      }
    },
    remove: function(key) {
      if (this.has_local_storage()) {
        $.cookie(key + '-timer', null, {
          path: '/'
        });
        return localStorage.removeItem(key);
      } else {
        return $.cookie(key, null, {
          path: '/'
        });
      }
    },
    has_local_storage: function() {
        try {
            return 'localStorage' in window && window['localStorage'] !== null;
        } catch (e) {
            return false;
        }
    }
  });
}).call(this);
