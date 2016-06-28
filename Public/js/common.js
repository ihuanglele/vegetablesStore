$(function() {
	
	/*
	$("#logId").click(function(){
		$(".loginTip").show();
		$(".loginTip .log").show();
	    $(".loginTip .reg").hide();	
        $(".loginTip #loginTab").addClass("curr");
		$(".loginTip #regTab").removeClass("curr");
	});	
	
	$("#regId").click(function(){
		$(".loginTip").show();
		$(".loginTip .reg").show();
	    $(".loginTip .log").hide();	
        $(".loginTip #loginTab").removeClass("curr");
		$(".loginTip #regTab").addClass("curr");

	
	});	
	
	$(".loginTip .tit a").click(function(){
		$(".loginTip").hide();

	});	
	*/
    

	
	//购物车hover
	/*
	$(".shopCartBox").hover(function(){
			$(".shopCart").addClass("shopCart_on");
			$(".shopCartList").show();		
	},function(){
			$(".shopCart").removeClass("shopCart_on");
			$(".shopCartList").hide();

	});
		*/
		
	//导航	
	$(".nav li").each(function(i) {
		$(this).hover(function(){
			$(".subNav",$(this)).show();
			var $subNav=$(".subNav",$(this));
//			if($subNav.has("dl")){
//				var num=$("dl",$subNav).length;
//				var $width=num*100;	
//				$subNav.css("width",$width);
//			}

			//alert($width);
		    
		},function(){
			$(".subNav",$(this)).hide();
		});
	});
		
	
	
		
	//booter
	$(".security_tab li").hover(function(){
		var i=$(this).index();
		$(this).addClass("on").siblings().removeClass("on");
		$(".security_cont").eq(i).show().siblings(".security_cont").hide();
	});
		
	
	

	
		
	//左侧栏目
	$(".menuList li .menuName").click(function(){
	    var parent=$(this).parents("li");
		parent.addClass("curr").siblings("li").removeClass("curr");
		$($(".menuName"),parent).removeClass("on");
		$(this).addClass("on");
		
	});
	
		
		
	
	
	//加入购物车
//	 $(".addShopCart").click(function(){	
//	$(".pop_cart").animate({right:"56px",top:"260px"}, 600)
//				  .show(function(){
//					setTimeout(function(){
//					  $(".pop_cart").hide();
//				     },2000)  
//					});	
//
//	});		
		
	
	//加入购物车
	$(".siderbar-wx").hover(function(){	
		$(".thick_wx").show();
	},function(){
		$(".thick_wx").hide();
	});		
	
	
	//产品搜索
	$(".siderbar-search").hover(function(){	
		$(".thick_search").show();
	},function(){
		$(".thick_search").hide();
	});
	
	
	
	
	
	
	//加号
	$(".addNumBox .add").click(function(){
		var $parent=$(this).parent(".addNumBox");
		var $num=window.Number($(".inputBorder",$parent).val());
		//alert($(".inputBorder",$parent).val());
		//alert( $num);
		$(".inputBorder",$parent).val($num+1);
		//$parent.find(".delete").addClass("minus").removeClass("minus_disable");
		
	});
	
	
	
	//减号
	$(".addNumBox .minus").click(function(){
	 var $parent=$(this).parent(".addNumBox");
	 var $num=window.Number($(".inputBorder",$parent).val());
		if($num>2){
			$(".inputBorder",$parent).val($num-1);
		}else{
			$(".inputBorder",$parent).val(1);
			//$parent.find(".delete").addClass("minus_disable").removeClass("minus");	
		}
	});








	//城市切换
	$(".siteSwitch").hover(function(){	
		$(".siteSwitch dd").show();
	},function(){
		$(".siteSwitch dd").hide();
	});	
	
	
	
	

	
})