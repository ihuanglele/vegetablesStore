$(function() {
    // 导航菜单
    $(".mainNav li").each(function(i) {
        if($(this).hasClass("on")) {
            $(this).hover(function() {
		    $(this).find(".subNav").show();
		},
		function() {
		    $(this).find(".subNav").hide();
		});
        } else {
            $(this).hover(function() {
                $(this).find(".subNav").show();
                $(this).addClass("hover");
            },
            function() {
                $(this).find(".subNav").hide();
                $(this).removeClass("hover");
            });
        }
    });
	
	
	//地区切换
	$(".region").hover(function(){
		$(".region_switch").show();
		},function(){
		$(".region_switch").hide();
		}

	);
    
    // 购物车
    /*
    $(".cart").hover(function() {
        $(".minicart_list").show();
    },
    function() {
        $(".minicart_list").hide();
    });
    */
	
	
	
	
	//关注我们
	$(".attention").hover(function(){
		$(".attention_warp").show();
		},function(){
		$(".attention_warp").hide();
		}

	);
	
	
});





function WinSize() // 函数：获取尺寸
{
	var winWidth = 0;
	var winHeight = 0;

	yScroll = (document.documentElement.scrollHeight > document.documentElement.clientHeight) ? document.documentElement.scrollHeight
			: document.documentElement.clientHeight;

	xScroll = (document.documentElement.scrollWidth > document.documentElement.clientWidth) ? document.documentElement.scrollWidth
			: document.documentElement.scrollWidth;
	return {
		"W" : xScroll,
		"H" : yScroll
	}
}


function WinSize2() // 函数：获取尺寸
{
	var winWidth = 0;
	var winHeight = 0;

	yScroll = (document.documentElement.scrollHeight > document.documentElement.clientHeight) ? document.documentElement.scrollHeight
			: document.documentElement.clientHeight;

	xScroll = (document.documentElement.scrollWidth > document.documentElement.clientWidth) ? document.documentElement.scrollWidth
			: document.documentElement.scrollWidth;
	return {
		"W" : xScroll,
		"H" : yScroll
	}
}


function WinSize3() // 函数：获取尺寸
{
	var winWidth = 0;
	var winHeight = 0;

	yScroll = (document.documentElement.scrollHeight > document.documentElement.clientHeight) ? document.documentElement.scrollHeight
			: document.documentElement.clientHeight;

	xScroll = (document.documentElement.scrollWidth > document.documentElement.clientWidth) ? document.documentElement.scrollWidth
			: document.documentElement.scrollWidth;
	return {
		"W" : xScroll,
		"H" : yScroll
	}
}
