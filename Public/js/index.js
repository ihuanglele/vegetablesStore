$(function() {
	
		$(".activity_title a").each(function(i) {
            $(this).click(function(){
				$(this).addClass("on").siblings().removeClass("on");
			    $(".activity_cont").eq(i).show().siblings(".activity_cont").hide();	
				
				
				
			});
        });
		
		
		
		      var p=$(".activity_cont");
				// 首页 活动区域 一排4个产品 每排的最后一个产品 li增加样式控制 class="last"
				$(".activityProList li",p).each(function(n) {
					if((n+1)%4==0){
						$(this).addClass("mr0");
						}
					
				});
		
		
		
//// 首页 活动区域 一排4个产品 每排的最后一个产品 li增加样式控制 class="last"
//$(".activity_cont .activityProList li").each(function(i) {
//	if((i+1)%4==0){
//		$(this).addClass("mr0");
//		}
//	
//});


		
		
	    $(".getMore").click(function(){
			
				if ($(".proClassifyWarpper").eq(6).is(":hidden")) {
						$(".proClassifyWarpper").eq(6).show();
						$(".getMoreTxt").text("收起更多鲜货");	
						$(".getMoreIcon img").attr("src","images/zd_arrow.jpg");
				} else {
						$(".proClassifyWarpper").eq(6).hide();
						$(".getMoreTxt").text("更多鲜货，就等你来");	
						$(".getMoreIcon img").attr("src","images/zk_arrow.jpg");
				}
	
		});	
		
		
	
	
	
});





