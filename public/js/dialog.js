(function($) {    
  $.fn.dialog = function(options) {
    var opts = $.extend({}, $.fn.dialog.defaults, options);    
    return this.each(function() {
      $this = $(this);
      var o = $.meta ? $.extend({}, opts, $this.data()) : opts;
      $this.hide();
      var mainModal = getModal(o.id);
      if(typeof options == 'string'){
        mainModal.modal(options);
        return;
      }
      mainModal.find('.mainModalLabel').text(o.title);
      if(o.url){
        mainModal.find('.modal-body').load(o.url);
      }else{
        mainModal.find('.modal-body').html($this.html());
      }
      mainModal.find('.modal-footer').html("");
      for(var i=0; i<o.buttons.length; i++){
        var btn = $('<button class="btn"></button>').addClass('btn').text(o.buttons[i].text);
        if(o.buttons[i].class)
          btn.addClass(o.buttons[i].class);
        if(o.buttons[i].close == true)
          btn.attr("data-dismiss", "modal");
        if(o.buttons[i].click){
          btn.click(o.buttons[i].click);
        }
        mainModal.find('.modal-footer').append(btn);
      }
      mainModal.modal();
    });    
  };

  function getModal(id){
    id = id || '';
    if($('#mainModal' + id).size() == 0){ 
      var modal = '<div id="mainModal' + id + '" class="modal hide fade">'+
                    '<div class="modal-header" id="modalbox">'+
                      '<button class="close" data-dismiss="modal">×</button>'+
                      '<h3 class="mainModalLabel">Modal header</h3>'+
                    '</div>'+
                    '<div class="modal-body"></div>'+
                    '<div class="modal-footer"></div>'+
                  '</div>';
      $('body').append(modal);
    }
    return $('#mainModal' + id);
  }

  // 插件的defaults    
  $.fn.dialog.defaults = {    
    title: '提示',    
    buttons:[
      {
        text : "Yes",
        class : "btn-danger",
        click : function(){
          alert("是");
        }
      },
      {
        text : "No",
        close : true
      },
    ]  
  };    
})(jQuery);  