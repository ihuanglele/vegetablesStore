$(function() {
	

//			var h=$(".proTabBox").offset().top;
//			alert(h);

	   $(window).scroll(function() {
			var top = $(window).scrollTop();
			//alert(getPosition(JtabMod).top);
			var h=$(".proTabBox").offset().top;
			if (top > 699) {
				//alert(111);
				//$("#a").css({position:'fixed',top:'0'});
				$(".proTabBox").hide();
				$(".proTabBox2").show();
			} else {
				/*$("#J_tab_mod").removeAttr('style');*/
				//$("#a").css({position:'relative',top:'0'});
				$(".proTabBox").show();
				$(".proTabBox2").hide();	
			}
		});



    $(".proTabBox li,.proTabBox2 li").click(function() {
        var i = $(this).index();
		$(".proTabBox li").eq(i).addClass("on").siblings().removeClass("on");
		$(".proTabBox2 li").eq(i).addClass("on").siblings().removeClass("on");
        $(".productCont").eq(i).show().siblings(".productCont").hide();
    });




	
    $(".pro_specification li").click(function() {
        $(this).addClass("on").siblings().removeClass("on");
    });
	

	
	$("#store-selector .text").hover(function(){
		var p=$(this).parents("#store-selector");
		p.addClass("hover");
		$(".content",p).show();
		$(".close",p).show();
	});
	
	
	$("#store-selector .close").click(function(){
		// alert('111');
		var p=$(this).parents("#store-selector");
		p.removeClass("hover");
		$(".content",p).hide();
		$(".close",p).hide();
	});
	
/*
    $(".JD-stock .mt .tab li").click(function() {
        var i = $(this).index();
        $(this).addClass("curr").siblings().removeClass("curr");
        $("a",this).addClass("hover");
        $("a",$(this).siblings()).removeClass("hover");		
        $(".JD-stock .mc").eq(i).show().siblings(".mc").hide();
    });*/

    // $(".JD-stock .mt .tab li").mouseover(function() {

	
   $(".notice").click(function() {
        $(".noticeTip").show();
    });
    $(".noticeTip .tit a").click(function() {
        $(".noticeTip").hide();
    });
	
	

	
})

//浏览商品通知emar
function viewProductNotifyEmar(){
	/*
	_adwq.push(['_setDataType', 'view']); 

	//用户ID
	_adwq.push(['_setCustomer', '1234567']); 

	// 下面代码是商品组代码，根据订单中包括多少种商品来部署，每种商品部署一组 
	_adwq.push(['_setItem', 
	'09890', // 09890是一个例子，请填入商品编号 - 必填项 
	'电视', // 电视是一个例子，请填入商品名称 - 必填项 
	'12.00', // 12.00是一个例子，请填入商品现价 - 必填项 
	'1', // 1是一个例子，请填入商品数量 - 必填项 
	'A123', // A123是一个例子，请填入商品分类编号 - 必填项 
	'家电', // 家电是一个例子，请填入商品分类名称 - 必填项 
	'10.00', // 10.00是一个例子，请填入商品原价 - 必填项 
	'http://www.test.com/a.gif', // 请填入素材地址 
	'Y' // 请填入商品状态，Y代表有效，N代表无效 
	]); 

	// 下面是提交代码，此段代码必须放在以上代码后面 - 必填项 
	_adwq.push([ '_trackTrans' ]); 
*/

}