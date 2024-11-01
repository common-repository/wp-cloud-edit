jQuery(document).ready(function( $ ){
    var gtk = $("#gtoken").val();
    var qurl = $("#queryUrl").val();
    var nonce = $('input#_wpnonce').val();

    $("#addTag").click(function(){
        $("#sourceBoard #addSource").siblings().each(function(){
            $(this).addClass("hidden");
        })
        $("#sourceBoard #addSource").removeClass("hidden");
        $('#sourceBoard #addSource').removeAttr("hidden");
    });

    $("#manTag").click(function(){
        $.get(qurl,
            {qn:'rList', p:'man', token:gtk},
            function(msg){
                $("#sourceBoard #manSource").siblings().each(function(){
                    $(this).addClass("hidden");
                })
                $("#sourceBoard #manSource").removeClass("hidden");
                $("#sourceBoard #manSource").html('<h1>内容源 >> 管理内容源</h1><hr><div class="col-md-8 ruleList">'+msg+'</div>');
            });
    });
    $("#corr").click(function(){
        $("#contentBoard #manCorr").siblings().each(function(){
            $(this).addClass("hidden");
        })
        $("#contentBoard #manCorr").removeClass("hidden");
    });

    function setTokenMsg(pos){
        $(pos).html('<p class="text-danger"> <span class="glyphicon glyphicon-bell" style = "font-size:40px;">  请先设置TOKEN!!</span></p>');
    }
    
    if(gtk !== ''){
        var fci = $("div#cats a").first().attr('id');
        clickCat(fci);
        $("div#cats a").first().addClass('active');
    }else{
        setTokenMsg("#contentBoard");
    }
    
    $("div#cats a").click(function(){
        clickCat($(this).attr('id'));
        $(this).addClass('active');
        $(this).siblings().each(function(){
            $(this).removeClass('active');
        });
    });
    
    function clickCat(catId){
        $.get(window.location.href, 
            {'cm':'list', 'c':catId, 'token':gtk, '_wpnonce':nonce},
            function(msg){
                var s = msg.indexOf("[ruleorcat]")+11;
                var reg_rid = /^[0-9,\s]*$/;
                var ret = $.trim(msg.substr(s));
                if(reg_rid.test(ret)){
                    $.get(qurl,
                        {qn:'aList', ri:ret, c:catId, 'token':gtk},
                        function(al){
                            $("#contentBoard #catContent").siblings().each(function(){
                                $(this).addClass("hidden");
                            })
                            $("#contentBoard #catContent").removeClass("hidden");
                            $("#contentBoard #catContent").html(al);
                        });
                }else{
                    $("#contentBoard #addCorr").siblings().each(function(){
                        $(this).addClass("hidden");
                    })
                    $("#contentBoard #addCorr").removeClass("hidden");
                    $("#contentBoard #addCorr #cat").attr("placeholder",ret);
                }
            });
    }
    
    
    if(gtk !== ''){
        //firstRuleId(fri)
        var fri = $("div#rules a").first().attr('id');
        if( typeof(fri) == 'undefined' ){
            $("#sourceBoard #manSource").html("<h1>你还没有设置内容源，请先[新增内容源]</h1>");
        }else{
            clickRule(gtk, fri/*24491*/);
            $("div#rules a").first().addClass('active');
        }
    }else{
        setTokenMsg('#sourceBoard');
    }
    
    $('div#rules').on('click', 'a', function(event){
        clickRule(gtk, $(this).attr('id'));
        $(this).addClass('active');
        $(this).siblings().each(function(){
            $(this).removeClass('active');
        });
    });
    
    function clickRule(tk, ruleId){
        $.get(qurl,
                {qn:'aList', token:tk, ri:ruleId},
                function(msg){
                    $("#sourceBoard #catContent").siblings().each(function(){
                        $(this).addClass("hidden");
                    })
                    $("#sourceBoard #catContent").removeClass("hidden");
                    $("#sourceBoard #catContent").html(msg);
                });
    }
                        
});
