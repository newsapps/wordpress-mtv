(function() {
  var $;
  var __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; }, __indexOf = Array.prototype.indexOf || function(item) {
    for (var i = 0, l = this.length; i < l; i++) {
      if (this[i] === item) return i;
    }
    return -1;
  };
  if (!$) {
    $ = jQuery;
  }
  window.MTV = new Object({
    debugging: function() {
      if (WordPress.DEBUG && this.hasConsole()) {
        return true;
      } else {
        return false;
      }
    },
    hasConsole: function() {
        try {
            return 'console' in window && window['console'] !== null;
        } catch (e) {
            return false;
        }
    },
    do_ajax: function(url, data, success, error) {
      var json, params;
      json = JSON.stringify(data);
      params = {
        url: WordPress.ajaxurl,
        type: 'POST',
        data: {
          action: 'mtv',
          path: url,
          data: json
        },
        dataType: 'json',
        success: __bind(function(data, textStatus, jqXHR) {
          if (this.debugging()) {
            console.log("do_ajax: " + textStatus);
            console.log(data);
            console.log(jqXHR);
          }
          if (success) {
            return success(data, textStatus, jqXHR);
          }
        }, this),
        error: __bind(function(jqXHR, textStatus, errorThrown) {
          if (this.debugging()) {
            console.log("do_ajax: " + textStatus);
            console.log(jqXHR);
            console.log(errorThrown);
          }
          if (error) {
            return error(jqXHR, textStatus, errorThrown);
          }
        }, this)
      };
      if (this.debugging()) {
        return setTimeout(__bind(function() {
          return $.ajax(params);
        }, this), 2000);
      } else {
        return $.ajax(params);
      }
    }
  });
  if (__indexOf.call(window, Backbone) >= 0 || window['Backbone'] !== null) {
    Backbone.sync = function(method, model, success, error) {
      var data, url;
      data = method === 'create' || method === 'update' ? model.toJSON() : null;
      url = method !== 'read' ? "" + (model.url()) + "/" + method : model.url();
      return MTV.do_ajax(url, data, success, error);
    };
  }
}).call(this);
