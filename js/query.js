jQuery(document).ready(function( $ ){
	var qurl = $('#queryUrl').val();
	var gtk = $('#gtoken').val();
    var nonce = $('input#_wpnonce').val();

	$('#manSource').on( "click", "button.del", function() {
	// $('button.del').click(function(){
		$(this).parents("tr").addClass("del");
		$.get(qurl,
            {qn:'rDel', token:gtk, ruleId:$(this).attr("value")},
            function(msg){
            	if(msg == "0"){
            		$('tr.del').fadeOut(500);
            		showRules(gtk);
            	}
          	});
	});

	function showRules(tk){
	  $.get(qurl,
	     {qn:'rList', p:'nav', token:tk},
	     function(msg){
	       $("div#rules").html(msg);
	     });
	}

	///////////addSource/////////////

	$("#addSource").on("click", "#selectAll", function(event){ 
	// $("#selectAll").click(function(event) { 
		event.preventDefault();
	  $("#source :checkbox").prop("checked", true);  
	}); 

	$("#addSource").on("click", "#reverse", function(event){ 
	// $("#reverse").click(function(event) {  
		event.preventDefault();
	  $("#source :checkbox").each(function(){
	    $(this).prop("checked", !$(this).prop("checked"));
	  });
	}); 

	$("#addSource").on("click", ".btn-toolbar button", function(event){ 
	// $('.btn-toolbar button').click(function(event){
		event.preventDefault();
		var pn=$(this).val();
		var cp=$(this).parents('.btn-toolbar').find('button.active').val();
		if(pn==cp){return;}

		var args = $("#args").val();
		if(args != 0){
			$.get(qurl,
	    		{qn:'sList', token:gtk, 'args':args, start:pn*10},
	    		function(msg){
	    			$(".sourceList").html(msg);
	    	});
		}else{
			$.get(qurl,
	    		{qn:'sList', token:gtk, start:pn*10},
	    		function(msg){
	    			$(".sourceList").html(msg);
	    	});
		}
	});

	///////////////source content////////////////

	$("#catContent").on("click", ".title", function(){
	// $('.title').click(function(){
	  $(this).siblings('#article').slideToggle().parents('li').siblings().find('#article').slideUp();
	}); 

	$("#catContent").on("click", "#selectAll", function(){
	// $("#selectAll").click(function () { 
		$(".list-group :checkbox").prop("checked", true);  
	}); 

	$("#catContent").on("click", "#reverse", function(){
	// $("#reverse").click(function () {  
		$(".list-group :checkbox").each(function(){
	    	$(this).prop("checked", !$(this).prop("checked"));
		});
	}); 

	$("#catContent").on("click", "button#SubaID", function(){
	// $('button#SubaID').click(function(){
	    var chk_value = [];
	    $("ul.al :checked").each(function(){
	      chk_value.push($(this).val());
	    });
	    if(chk_value.length == 0){
	    	alert("没有选中任何文章！");
	    }else{
		    $('ul.al').find(':checked').each(function(){
			    subArticle($(this).parents('li'),"publish");
		    });
        }
	});

	$("#catContent").on("click", "button.pp", function(event){
	// $('button.pp').click(function(event){
		event.preventDefault();
		$('#pp .popup-preview').html($(this).parents('li').find("#detail").html());
	 	$('#pp.popup').addClass('is-visible');
	});

	$("#catContent").on("click", "button.pe", function(event){
	// $('button.pe').click(function(event){
		event.preventDefault();
		subArticle($(this).parents('li'),"edit");
	});

	$("#catContent").on("click", "button#pub", function(event){
	// $('button#pub').on('click', function(event){
		event.preventDefault();
		subArticle($(this).parents('li'),"publish");
	});

	$("#catContent").on("click", ".btn-toolbar button", function(event){
	// $('.btn-toolbar button').click(function(event){
		event.preventDefault();
		var pn=$(this).val();
		var cp=$(this).parents('.btn-toolbar').find('button.active').val();
		if(pn==cp){return;}

		var currentUrl = location.href;
		if(currentUrl.indexOf("content") > -1){
			var catId = $("div#cats a.active").attr('id');
	        $.get(window.location.href, 
	            {'cm':'list', 'c':catId, 'token':gtk, '_wpnonce':nonce},
	            function(msg){
	                var s = msg.indexOf("[ruleorcat]")+11;
	                var reg_rid = /^[0-9,\s]*$/;
	                var ret = $.trim(msg.substr(s));
	                if(reg_rid.test(ret)){
	                    $.get(qurl,
	                        {qn:'aList', ri:ret, c:catId, 'token':gtk, start:pn*20},
	                        function(al){
						        $("#contentBoard #catContent").siblings().each(function(){
						            $(this).addClass("hidden");
						        })
	                            $("#contentBoard #catContent").removeClass("hidden");
	                            $("#contentBoard #catContent").html(al);
	                        });
	                }else{
						$("#contentBoard #catContent").siblings().each(function(){
						    $(this).addClass("hidden");
						})
	                    $("#contentBoard #catContent").removeClass("hidden");
	                    $("#contentBoard #catContent").html("<h3>请求异常</h3>");
	                }
	            });
		}else if(currentUrl.indexOf("source") > -1){
			var rid = $("div#rules a.active").attr('id');
			$.get(qurl,
	    		{qn:'aList', ri:rid, token:gtk, start:pn*20},
	    		function(msg){
	    			$("#sourceBoard #catContent").html(msg);
	    	});
		}
	});
	function subArticle(tag, ac){
		var cats = [];
		var catId = $("div#cats a.active").attr('id');
	    var title = $(tag).find("#title").text();
	    var detail = $(tag).find("#detail").text();
	    var itemId = $(tag).find('form').attr('id');
        var nonce = $('input#_wpnonce').val();
	    if(catId != undefined){
	    	cats.push(catId);
	    }

	    $.post(window.location.href, 
	            {t:title, d:detail, c:cats, f:itemId, action:ac, '_wpnonce':nonce},
	            function(msg){
	            	var r = $.trim(msg.substr(-7));
	                if(r == "failure"){
	                  	alert("文章入库失败，请稍后重试或联系技术人员！");
	                	alert($.trim(msg.substr(-7)));
	                }else if(r == "success"){
	                  	$('#'+itemId).parent().fadeOut(800);
	                }else{
	                	var editurl = msg.substr(msg.indexOf("[editurl]")+9);
	                	if(ac == "edit"){
	                		window.open(editurl,"_self");
	                	}
	                  	$('#'+itemId).parent().fadeOut(800);
	                }
	                
	            });
	}

	$("#catContent").on("click", "#prePub.popup-trigger", function(event){
	// $('#prePub.popup-trigger').on('click', function(event){
		event.preventDefault();
		$(this).val('prePub');
		$('#cats.popup').addClass('is-visible');
	});

	$("#catContent").on("click", "button#Submit", function(){
	// $('button#Submit').click(function(){
	    var chk_value = [];
	    $(".list-group :checked").each(function(){
	      chk_value.push($(this).val());
	    });
	    if(chk_value.length == 0){
	    	alert("没有选中任何文章！");
	    }else{
			$('ul.al').find(':checked').each(function(){
				$(this).parents('li').find('#prePub.popup-trigger').val('prePub');
			});
			$('#cats.popup').addClass('is-visible');
	    }
	});

});
