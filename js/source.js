jQuery(document).ready(function( $ ){

   //open popup
  $('.popup-trigger').on('click', function(event){
    event.preventDefault();
    $('#cats.popup').addClass('is-visible');
  });
  //close popup
  $('.popup').on('click', function(event){
    if( $(event.target).is('.popup-close') || $(event.target).is('.popup') ) {
      event.preventDefault();
      $(this).removeClass('is-visible');
    }
  });
  //close popup when clicking the esc keyboard button
  $(document).keyup(function(event){
      if(event.which=='27'){
        $('#cats.popup').removeClass('is-visible');
      }
  });


  $('button.pub').click(function(){
    var cats = [];
      $("#cats :checked").each(function(){
          cats.push($(this).val());
      });
      var nonce = $('input#_wpnonce').val();

        if(cats.length == 0){
            alert("类目是必选项");
        }else{
            $("button[value='prePub']").each(function(){

                var title = $(this).parents('li').find("#title").text();
                var detail = $(this).parents('li').find("#detail").text();
                var itemId = $(this).parents('form').attr('id');

                $.post(window.location.href, 
                    {t:title, d:detail, c:cats, f:itemId, '_wpnonce':nonce},
                    function(msg){
                        if(msg == '0'){
                          alert("文章入库失败，请稍后重试或联系技术人员！");

                        }else{
                          $('#cats.popup').removeClass('is-visible');
                          $('#'+itemId).parent().fadeOut(800);
                        }
                        
                    });
            });

        }
  });

});
