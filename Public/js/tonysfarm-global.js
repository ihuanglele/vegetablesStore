var TM = {}

TM.DOMAIN = 'tonysfarm.com';
TM.COOKIE_DOMAIN = '.' + TM.DOMAIN;
TM.ROOT = 'http://www.' + TM.DOMAIN;
TM.ROOT_ENCODED = encodeURIComponent(TM.ROOT);

var tonysfarm = {

Person : {

  trim : function(s) {
    if (!s) return s;
    return s.replace(/^\s+/, '').replace(/\s+$/, '');
  },

  focusNav : function(id) {
	if (document.getElementById(id)) {
		document.getElementById(id).className = 'current';
	}
  },

  format : function(template) {
    if(arguments.length == 1) {
      return template;
    }
    for(var i = 1, k = arguments.length; i < k; i++) {
      template = template.replace(new RegExp('\\{' + (i - 1) + '\\}', 'g'), arguments[i]);
    }
    if(template.indexOf('\\{') > -1) {
      template = template.replace('\\{', '{');
    }
    if(template.indexOf('\\}') > -1) {
      template = template.replace('\\}', '}');
    }
    return template;
  },

  link : function() {
    $('.bar-list a').click(function() {
      $('.bar-list li').each(function() {
        $(this).removeClass('count');
      });
      $(this).parent('li').addClass('count');
    });
    $('.bar-list a').each(function() {
      var that = $(this);
      var title = that.attr('title');
      if (!title || jQuery.trim(title).length == 0) {
        that.attr('title', that.text());
      }
    });
    var page = document.getElementById("my-page");
    if(!page) return;
    var cur = $('#my-page').val();
    if(!cur) return;
    $('.bar-list li').each(function() {
      var th = $(this);
      if(th.attr('name') == cur) {
        th.addClass('count');
      }
    });
  },

  inputHover : function(hint) {
    var f = false;
    $('#form-input :text, #form-input :password, #form-input select, #form-input textarea, #form-input :checkbox').each(function() {
      var me = $(this);
      var p = me.parent().children(':text, :password, select, textarea');
      var hint = me.parent().children('em');
      //hint.err = me.parent().children('label').html().replace(/<[^>]+>.*?<\/[^>]+>/g, '') + '不能为空';
      hint.err = me.parent().children('label').html().replace(/<[^>]+>.*?<\/[^>]+>/g, '') + 'can not be null';
      hint.each(function() {
        $(this).txt = $(this).text();
      });
      me.bind({
        mouseover : function() {
          if(!me.f && !me.hasClass('form-error')) {
            p.addClass('form-hover');
          }
          if(!f) hint.addClass('hint');
        },
        focus : function() {
          me.f = true;
          if(!me.hasClass('form-error')) {
            p.addClass('form-focus');
            hint.addClass('hint');
          }
          f = true;
          me.select();
        },
        blur : function() {
          me.f = undefined;
          p.removeClass('form-hover').removeClass('form-focus').removeClass('form-error');
          hint.removeClass('hint').removeClass('error');
          if(!checkRequired(p)) {
            hint.text(hint.err);
            hint.addClass('error');
            p.addClass('form-error');
          } else {
            hint.text(hint.txt);
          }
        },
        mouseout : function() {
          if(!me.f && !me.hasClass('form-error')) {
            p.removeClass('form-hover').removeClass('form-focus');
            hint.removeClass('hint');
          }
        }
      });

      function checkRequired(elements) {
        var ok = true;
        elements.each(function() {
          var t = $(this);
          if(t.attr('required') && tonysfarm.Person.trim(t.val()).length == 0) {
            ok = false;
          }
        });
        return ok;
      }
    });
  },

  initFocus : function(selector) {
    $(selector).focus();
  },

  defaultRadioValue : function(selector, value, defaultValue) {
    if(!value || tonysfarm.Person.trim(value).length == 0) {
      value = defaultValue;
    }
    $(selector + '[value=' + value + ']').attr('checked', value);
  },

  formValues : function(formSelector) {
    var s = [];
    $(formSelector + ' input, ' + formSelector + ' textarea, ' + formSelector + ' select').each(function() {
      var me = $(this);
      var t = me.attr('type') ? me.attr('type').toLowerCase() : '';
      s.push( (t == 'radio') ? me.attr('checked') : me.val() );
    });
    return s.join('|');
  }
}}