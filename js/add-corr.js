jQuery(document).ready(function( $ ){
    var gtk = $("#gtoken").val();
    var qurl = $("#queryUrl").val();
    var nonce = $('input#_wpnonce').val();
    $.get(qurl,
            {qn:'rList', p:'cor', token:gtk},
            function(msg){
                $('div.rules').html(msg);
            });

    $('button.subCor').click(function(){
        var chks = [];
        var chks_name = [];
        var catId = $("div#cats a.active").attr('id');
        $('div.rules :checked').each(function(){
            chks.push($(this).val());
            chks_name.push($(this).prev().text());
        });
        if(chks.length == 0){
            alert('至少选择一个规则');
        }else{
            $.post(window.location.href, 
                {cor:chks, cor_name:chks_name, c:catId, token:gtk, '_wpnonce':nonce},
                function(msg){
                    if(msg.substr(-1) == "0"){
                        $("#contentBoard #infoSecc").siblings().each(function(){
                            $(this).addClass("hidden");
                        })
                        $("#contentBoard #infoSecc").removeClass("hidden");
                    }else{
                        $("#contentBoard #infoFail").siblings().each(function(){
                            $(this).addClass("hidden");
                        })
                        $("#contentBoard #infoFail").removeClass("hidden");
                    }
                });
        }
    });

    $('button.cancel').click(function(event){
        event.preventDefault();
        $(this).parents("tr").addClass("cancel");
        $.get(window.location.href, 
            {cm:'del', c:$(this).val(), token:gtk, '_wpnonce':nonce},
            function(msg){
                if(msg.substr(-1) == "0"){
                    $('tr.cancel').fadeOut(800);
                }else{
                    alert('操作失败，请稍后重试！');
                }
            });
    });
    
});
