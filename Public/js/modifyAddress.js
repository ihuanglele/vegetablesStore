	/**
	 * 验证手机号
	 * @param mobileNo
	 * @returns {Boolean}
	 */
	function checkMobile(mobileNo){
		var pattern = /^1\d{10}$/;
		if(pattern.test(mobileNo)){
			return true;
		}
		return false;
	}


		var check = null;
    	// 填写地址验证
    	var address = {
    		partyId:$("#partyId"),
    		memberAddressId:$("#memberAddressId"),
    		receiverName:$("#receiverName"),
    		province:$("#province"),
    		city:$("#city"),
    		district:$("#district"),
    		deliveryAddress:$("#deliveryAddress"),
    		receiverMobile:$("#receiverMobile"),
    		isDefault:$("#isDefault"),
    		validate:function(){
    			if(!address.receiverName.val()){
					$("#dialog_box_tc").text("收货人必填");
					$(".dialog").show();
	                return false;
				}else if(!address.province.val() || address.province.val()=='-1'){
					$("#dialog_box_tc").text("省份/直辖市必填");
					$(".dialog").show();
	                return false;
				}else if(!address.city.val() || address.city.val()=='-1'){
					$("#dialog_box_tc").text("城市必填");
					$(".dialog").show();
	                return false;
				}else if(!address.district.val() || address.district.val()=='-1'){
					$("#dialog_box_tc").text("区县必填");
					$(".dialog").show();
	                return false;
				}else if($.trim(address.deliveryAddress.val()).length<1){
					$("#dialog_box_tc").text("详细地址必填");
					$(".dialog").show();
	                return false;
				}else if(!address.receiverMobile.val()){
					$("#dialog_box_tc").text("手机必填");
					$(".dialog").show();
	                return false;
				}else if(!checkMobile(address.receiverMobile.val())){
					$("#dialog_box_tc").text("手机号格式不正确");
					$(".dialog").show();
	                return false;
				}
				return true;
    		},
    		saveAddress:function(){
    			if(address.validate()){
    				var data = {
    					"partyId":address.partyId.val(),
    					"memberAddressId":address.memberAddressId.val(),
    					"receiverName":address.receiverName.val(),
    					"province":address.province.val(),
    					"city":address.city.val(),
    					"district":address.district.val(),
    					"deliveryAddress":address.deliveryAddress.val(),
    					"receiverMobile":address.receiverMobile.val(),
    					"isDefault":address.isDefault.val(),
    				};
    				$.ajax({
		                type:'post',
		                url:ctx+'/user/saveAddress?ajax=true',
		                data:data,
		                success:function(data){
		                    if(data=='1'){
		                         location.href=ctx+"/user/address";
		                    }else{
		                         $("#dialog_box_tc").text(data);
		                         $(".dialog").show();
		                    }
		                }
	             	});
    			}
    		}
    		
    	}
//    	$('#receiverName').bind("focus", function() {
//		    $(".dialog").hide();
//		});
//		$('#province').bind("focus", function() {
//		    $(".dialog").hide();
//		});
//		$('#city').bind("focus", function() {
//		    $(".dialog").hide();
//		});
//		$('#district').bind("focus", function() {
//		    $(".dialog").hide();
//		});
//		$('#deliveryAddress').bind("focus", function() {
//		    $(".dialog").hide();
//		});
//		$('#receiverMobile').bind("focus", function() {
//		    $(".dialog").hide();
//		});
    	$("#article").on("click", function() {
		    $(".dialog").hide();
    	});
		
   		// 绑定点击事件
    	$("#btnModifyAddress").on("click",function(){
    		address.saveAddress();
    	});