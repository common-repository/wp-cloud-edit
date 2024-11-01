jQuery(document).ready(function( $ ){
  var qurl = $('#queryUrl').val();
  var tk = $('#gtoken').val();

  //source submit
  $('#sSubmit').click(function(){
      var chk_value = [];
      $("#source :checked").each(function(){
        chk_value.push($(this).val());
      });
      $("[name='sourceIds']").val(chk_value);
      $('.popup').removeClass('is-visible');
  });

  //rule submit
  $('#rSubmit').click(function(){
      var ruleName = $("[name='ruleName']").val();
      var sourceIds = $("[name='sourceIds']").val();
      var keywords = $("[name='keywords']").val();
      var matchType = $("[name='matchType']:checked").val();
      var noKeywords = $("[name='noKeywords']").val();
      var nomatchType = $("[name='nomatchType']:checked").val();
      var order = $("[name='order']").val();

      
      if(ruleName.length != 0 && sourceIds.length != 0){
        

        var args = new Object();
        args.rule_name    = ruleName;
        args.sourceids   = sourceIds;
        if(keywords.length != 0){

          args.match_keywords  = keywords;
          args.match_type = matchType;
        }

        if(noKeywords.length != 0){

          args.nomatch_keywords  = noKeywords;
          args.nomatch_yype = nomatchType;
        }
        if(order.length != 0){
          args.sort = order;
        }else{
          args.sort = "1";
        }

        $.get(qurl,
            {'qn':'rAdd', 'token':tk, 'args':args},
            function(msg){
                $("#sourceBoard #catContent").siblings().each(function(){
                    $(this).addClass("hidden");
                })
                $("#sourceBoard #catContent").removeClass("hidden");
                $("#sourceBoard #catContent").html(msg);
                showRules(tk);
            });
      }else{
        alert("规则名和来源不能为空！！");
      }

  });
  function showRules(tk){
    $.get(qurl,
       {qn:'rList', p:'nav', token:tk},
       function(msg){
         $("div#rules").html(msg);
       });
  }

  //open popup
  $('button.showSources').on('click', function(event){
    event.preventDefault();
    $('#add.popup').addClass('is-visible');
    $.get(qurl,
          {qn:'sList', token:tk},
          function(msg){
            $(".sourceList").html(msg);
    });
  });
  //close popup
  $('#add.popup').on('click', function(event){
    if( $(event.target).is('.popup-close')/* || $(event.target).is('.popup') */) {
      event.preventDefault();
      $(this).removeClass('is-visible');
    }
  });
  //close popup when clicking the esc keyboard button
  $(document).keyup(function(event){
      if(event.which=='27'){
        $('#add.popup').removeClass('is-visible');
      }
  });

  $('#filter').click(function(){
      var types = [];
      $(this).siblings(':checked').each(function(){
        types.push($(this).val());
      });
      var q = $(this).siblings(':text').val();
      var args = new Object();
      if(types.length != 0){
        args['type'] = types.join(',');
      }
      if(q.length != 0){
        args['q'] = q;
      }
      if(args.length == 0){
          $.get(qurl,
              {'qn':'sList', 'token':tk, 'args':0},
              function(msg){
                $(".sourceList").html(msg);
          });
      }else{
          $.get(qurl,
              {'qn':'sList', 'token':tk, 'args':args},
              function(msg){
                $(".sourceList").html(msg);
          });
      }
  });
});