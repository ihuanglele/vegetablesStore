//收货地址删除动画
    $('#navDelete').on('click',function () {
        if($(this).attr('data-state')=="close"){
            $(this).attr('data-state','open').html('取消').addClass('on');
            $('.item-wrap').animate({'right':50},300,'ease');
            $('.del-btn').animate({'right':0},300,'ease');

        }else{
            $(this).attr('data-state','close').html('删除').removeClass('on');
            $('.item-wrap').animate({'right':0},300,'ease');
            $('.del-btn').animate({'right':-50},300,'ease');
        }
    });
    
    $(".del-btn").on("click",function(){
    	var memberAddressId = $(this).attr("value");
    	$.ajax({
            type:'post',
            url:ctx+'/user/deleteAddress?ajax=true',
            data:{"memberAddressId":memberAddressId},
            success:function(data){
                if(data=='1'){
                     //location.href=ctx+"/user/address";
                	$("#"+memberAddressId).empty();
                	showDeleteSuccess();
                }else{
                     $("#dialog_box_tc").text(data);
                     $(".dialog").show();
                }
            }
     	});
    	event.preventDefault();// 阻止a标签href事件
    });
    
    /**
	 * 地址删除成功提示信息
	 */
	function showDeleteSuccess(){
		if($('.altmsg').attr('name')=='off'){
            $('.altmsg').attr('name','on').removeClass('altmsg-anima-off').addClass('altmsg-anima');
            setTimeout(function() {
                $('.altmsg').removeClass('altmsg-anima').addClass('altmsg-anima-off').attr('name','off');
            }, 1000);
        }else{
            $('.altmsg').attr('name','off').removeClass('altmsg-anima');
        }
	}