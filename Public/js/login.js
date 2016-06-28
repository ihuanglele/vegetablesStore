$(function() {
	$(".codeTip .tit a").live("click",function(){
		$("#smsJCaptcha").val("");
		$(".thickdiv").hide();
	    $(".codeTip").hide();	
	});
	
	$("#user_name_login,#passwd_login,#loginJCaptcha").bind({
		blur:function(){
			var v=$.trim($(this).val());
			var id=$(this).attr("id");
			var msg="";
			if(v==""){
				if(id=="user_name_login"){
					msg="用户名不能为空！";
				}else if(id=="passwd_login"){
					msg="密码不能为空！";
				}else if(id=="loginJCaptcha"){
					msg="防伪码不能为空！";
				}
				$("#"+id+"_message").html(msg);
//				$("#"+id).focus();
				return false;
			}else{
				$("#"+id+"_message").html("");
			}
		}
	});
	
	$("#emailregform,#phoneregform").find("#password1,#password2,#email_input,#smsJCaptcha,#password1,#password2").bind({
		blur:function(){
			var td=$(this).parent();
			var v=$.trim($(this).val());
			var id=$(this).attr("id");
			var msg="";
			if(id=="password1"||id=="password2"){
				var tr1,tr2;
				if(id=="password1"){
					tr1=td.parent();
					tr2=tr1.next();
				}
				if(id=="password2"){
					tr2=td.parent();
					tr1=tr2.prev();
				}
				var v1=$.trim(tr1.find("#password1").val());
				var v2=$.trim(tr2.find("#password2").val());
				/*
				if(v1==""){
					tr1.find("#password1_message").html("请填写密码！");
					tr2.find("#password2_message").html("");
//					tr1.find("#password1").focus();
					return false;
				}else{
					if(v2==""){
						tr2.find("#password2_message").html("请填写确认密码！");
						tr1.find("#password1_message").html("");
//						tr2.find("#password2").focus();
						return false;
					}
					if(v1!=v2){
						tr2.find("#password2_message").html("两次密码必须相同！");
						tr1.find("#password1_message").html("");
//						tr2.find("#password2").focus();
						return false;
					}
				}
				*/
				if((v1!=""&&v2!="")&&(v1!=v2)){
					tr2.find("#password2_message").html("两次密码必须相同！");
					tr1.find("#password1_message").html("");
//					tr2.find("#password2").focus();
					return false;
				}
				tr1.find("#password1_message").html("");
				tr2.find("#password2_message").html("");
				return false;
			}else{
				if(v==""){
					if(id=="mobile_input"){
						msg="请填写手机！";
					}else if(id=="email_input"){
						msg="请填写邮箱！";
					}else if(id=="smsJCaptcha"){
						msg="图片防伪码不能为空！";
					}
					td.find("#"+id+"_message").html(msg);
//					td.find("#"+id).focus();
					return false;
				}else{
					td.find("#"+id+"_message").html("");
					var form_name=$(this).parent().parent().parent().parent().attr("id");
					if(id=="mobile_input"&&form_name=="phoneregform"){
						checkMobile('phoneregform');
						return false;
					}
					if(id=="email_input"&&form_name=="emailregform"){
						checkEmail('emailregform');
						return false;
					}
				}
			}
		}
	});
	
	$("#user_name_login").change(function(){
		$("#login_auth_code").hide();
	});

	initLogin();
});

function writeIsReturnCookie(val) {
	var validMin = 90 * 24 * 60;
	TonysfarmCookie.writeCookie(TonysfarmCookie.RT, val, TM.COOKIE_DOMAIN, '/', validMin);
}

/**
 * 登录后初始化当前登录用户昵称
 * 
 * 入参
 * #logId			顶部登录按钮id
 * #regId			顶部注册按钮id
 */
function initLogin() {

//console.log("----------------"+TonysfarmCookie.isLogin()+"-------------");
	
/*
	
	var requestRandom = Math.round(Math.random() * 1000000);

	$.ajax({
		method : loginStatusMethod.method,
		url : loginStatusMethod.url + requestRandom,
		beforeSend : function(XMLHttpRequest) {
		},
		dataType : loginStatusMethod.datatype,
		jsonp : 'jsoncallback',
		success : function(data) {
			if (data.status == "true") {
				var accountType = data.message.accountType;
				var nickName = data.message.nickName;
				$("#logId").remove();
				$("#regId").removeAttr("onclick");
				$("#none").remove();
				if (nickName != null && nickName != '') {
					$("#regId").html(
							nickName + '<a info="memberInfoId" href="' + base_path
									+ '/my/member.html" class="loginMember"><b>[进会员中心]</b></a>&nbsp;&nbsp;<a info="memberInfoId" href="javascript:logout(\'' + accountType + '\');">注销</a>');
				} else {
					$("#regId").html(
							data.message.memberName + '<a info="memberInfoId" href="' + base_path
									+ '/my/member.html" class="loginMember"><b>[进会员中心]</b></a>&nbsp;&nbsp;<a info="memberInfoId" href="javascript:logout(\'' + accountType + '\');">注销</a>');
				}
			}else{
				var redurl = location.href;
				if(redurl.indexOf("?")>-1){
					$("#regId").html('<a href="/login.html?'+encodeURIComponent(redurl+"&showid=1")+'">注册</a>');
					$("#logId").html('<a href="/login.html?'+encodeURIComponent(redurl+"&showid=0")+'">登录</a>');
				}else{
					$("#regId").html('<a href="/login.html?'+encodeURIComponent(redurl+"?showid=1")+'">注册</a>');
					$("#logId").html('<a href="/login.html?'+encodeURIComponent(redurl+"?showid=0")+'">登录</a>');
				}
			}
		}
	});
	*/
	
	
	var tc = new TonysfarmCookie("/");
	if (tc.isLogin()) {
		var accountType = tc.getAccountType();;
		var nickName = decodeURIComponent(tc.getNickName());
		$("#logId").remove();
		$("#regId").removeAttr("onclick");
		$("#none").remove();
		$("#regId").html(
					"您好, "+nickName + '<a info="memberInfoId" href="' + base_path
							+ '/my/member.html" class="loginMember"><b>[进会员中心]</b></a>&nbsp;&nbsp;<a info="memberInfoId" href="javascript:logout(\'' + accountType + '\');">注销</a>');
	}else{
		var redurl = location.href;
		if(redurl.indexOf("?")>-1){
			$("#regId").html('<a href="/login.html?'+encodeURIComponent(redurl+"&showid=1")+'">注册</a>');
			$("#logId").html('<a href="/login.html?'+encodeURIComponent(redurl+"&showid=0")+'">登录</a>');
		}else{
			$("#regId").html('<a href="/login.html?'+encodeURIComponent(redurl+"?showid=1")+'">注册</a>');
			$("#logId").html('<a href="/login.html?'+encodeURIComponent(redurl+"?showid=0")+'">登录</a>');
		}
	}
}

	/**
	 * 
	 * 用户登录
	 * 页面需要固定id的控件描述如下
	 *
	 * 入参
	 * #user_name_login  登录用户名，可以是验证手机或验证邮箱
	 * #passwd_login     登录密码
	 * #forever          记住我
	 * #loginJCaptcha    验证码的输入框
	 *
	 * 出参
	 * #error            登录错误信息
	 * #logId            顶部菜单会员名显示
	 * #regId			 顶部注册按钮
	 * #activeEmail		 待激活邮件地址
	 * #goEmail			 激活链接标签id
	 */
function doLogin() {
	// 入参
	var name = $.trim($("#user_name_login").val());
	var password = $.trim($("#passwd_login").val());
	var autoLogin = $("#forever").attr("checked") == 'checked' ? "on" : '';
	var loginJCaptcha = $.trim($("#loginJCaptcha").val());
	
	// 出参
	var tip='@nickname<a info="memberInfoId" href="/my/member.html" class="loginMember"><b>[进会员中心]</b></a>&nbsp;&nbsp;<a info="memberInfoId" href="javascript:logout("@accountType");">注销</a>';
	if (name == "") {
		$("#error").html("用户名不能为空！");
		$("#user_name_login").focus();
		return false;
	}
	
	if (password == "") {
		$("#error").html("密码不能为空！");
		$("#passwd_login").focus();
		return false;
	}
	
	$("#error").html("");

	//调用登录方法
	$.ajax({
		method : loginMethod.method,
		url : loginMethod.url,
		beforeSend : function(XMLHttpRequest) {
		},
		data : "name=" + name + "&password=" + password + "&autoLogin=" + autoLogin + "&loginJCaptcha=" + loginJCaptcha + "&random=" + Math.floor(Math.random() * 100),		
		dataType : loginMethod.datatype,
		jsonp : 'jsoncallback',
		success : function(data) {
			if (data.status == "true") {
				var nickName = data.message.nickName;
				$("#logId").remove();
				$("#regId").removeAttr("onclick");
				$("#none").remove();
				var accountType = data.message.accountType;
				
				//处理顶部登录框的提示信息
				if (nickName != null && nickName != '') {
					tip = tip.replace('@nickname', nickName);
					tip = tip.replace('@accountType', accountType);
					$("#regId").html(tip);
				} else {
					nickName = data.message.memberName;
					tip = tip.replace('@nickname', nickName);
					tip = tip.replace('@accountType', accountType);
					$("#regId").html(tip);
				}
				
				//关闭注册窗口
				closeRegisterDialog();
				
				//登录成功刷新当前页面
				if (window.opener != null) {
					window.opener.location.reload();
				}

				window.location.reload();
				
			//false代表账号或密码错误！
			} else if (data.status == "false") {
				$("#error").html(data.message);
				
			//active代表邮箱注册，没有激活，需要去验证
			} else if (data.status == "active") {
				closeRegisterDialog();
				var div=$("#win2");
				div.find("#activeEmail").html(data.message.email);
				div.find("#goEmail").attr("href", "/goEmail.htm?email=" + data.message.email);
				div.find("#goEmail").attr("partyId", data.message.partyId);
				openEmailDialog();
				
			//验证码错误，刷新验证码
			} else if (data.status == "show"||data.status == "refresh") {
				$("#error").html(data.message);
				refreshCaptcha();
				$("#login_auth_code_div").show();
				$("#loginJCaptcha").focus();
				
				
			//vip不可登录
			//} else if (data.status == "vip") {
			//	closeRegisterDialog();
			//	$.dialog({
			//		type : "warn",
			//		content : "您是VIP客户不能登录！</br>请致电&nbsp;<font style='color:red;'>4008202162</font>",
			//		modal : true
			//	});
			//	return;
			} else {
				$("#error").html("验证码输入错误，请重新输入！");
				
				refreshCaptcha();
				$("#loginJCaptcha").focus();
			}
		},
		complete : function(XMLHttpRequest, textStatus) {
		}
	});
}


/**
 * 注销登录
 * 
 * 入参
 * loginType
 * 1：空字符串			手机或邮箱注册用户登录注销
 * 2：q					qq 登录注销
 * 3：qqwb				腾讯微博登录注销
 * 4：sinawb				新浪微博登录注销
 */
function logout(loginType) {
	var requestRandom = Math.round(Math.random() * 100) + 1;
	$.ajax({
		method : logoutMethod.method,
		datatype : logoutMethod.datatype,
		url : logoutMethod.url + requestRandom,
		success : function(data) {
			if (data.status == "true") {
				if (loginType == 'q') {//qq 登录
					QC.Login.signOut();
					window.location.href = home_path;
				} else if (loginType == 'qqwb') {//腾讯微博登录
					T.logout(function() {
						if (checkCookie('QQWBToken3_801436771')) {
							delCookie('QQWBToken3_801436771');
						}

						if (checkCookie('QQWBRefreshToken3_801436771')) {
							delCookie('QQWBRefreshToken3_801436771');
						}

						window.location.href = home_path;
					});
				} else if (loginType == 'sinawb') {//新浪微博登录
					WB2.logout(function() {
						window.location.href = home_path;
					});
				} else {//注销账户登录
					window.location.href = home_path;
				}
			} else {
				showmessage(data.message);
			}
		},
		complete : function(XMLHttpRequest, textStatus) {
		}
	});
}

/** 初始化新浪微博登录 
 * 
 *入参
 * #wb_connect_btn			新浪登录按钮id
 */
function initSinaWb() {
	WB2.anyWhere(function(W) {
		W.widget.connectButton({
			id : "wb_connect_btn",
			type : '3,5',
			callback : {
				login : function(o) { // 登录后的回调函数
					cooperationLoginValidate('sinawb', o.id, o.screen_name);
				}
			}
		});
	});
}

/** 初始化qq登录 
 * 
 * 入参
 * #qqLoginBtn			qq登录按钮
 * 
 */
function initQQ() {
	// 调用QC.Login方法，指定btnId参数将按钮绑定在容器节点中
	QC.Login({
		// btnId：插入按钮的节点id，必选
		btnId : "qqLoginBtn",
		// 用户需要确认的scope授权项，可选，默认all
		scope : "all",
		// 按钮尺寸，可用值[A_XL| A_L| A_M| A_S| B_M| B_S| C_S]，可选，默认B_S
		size : "B_M"
	}, function(reqData, opts) {// 登录成功
		qqLoginSuccess('q', reqData.nickname);
	}, function(opts) {// 注销成功
		window.location.href = base_path + "/index.html";
	});
}

/** qq登录成功操作 */
function qqLoginSuccess(accountType, nickName) {
	if (QC.Login.check()) {
		QC.Login.getMe(function(openId, accessToken) {
			cooperationLoginValidate(accountType, openId, nickName);
		});
	}
}
/**
 * 腾讯微博登录
 */
function qqWbLogin() {
	T.login(function(loginStatus) {
		getUserInfo();
	}, function(loginError) {
	});
}
/**
 * 腾讯微博登录成功后获取用户信息
 */
function getUserInfo() {
	T.api("/user/info").success(function(response) {
		if (response.ret === 0) {
			data = response.data;
			cooperationLoginValidate('qqwb', data.openid, data.nick);
		} else {
			showmessage(response.ret);
		}
	}).error(function(code, message) {
		showmessage(message);
	});
}

/** 初始化腾讯微博登录 */
function initQQWb() {
	T.init({
		appkey : 801436771
	});

	if (!T.loginStatus()) {
	} else {
		getUserInfo();
	}
	var login_btn = document.getElementById("login_btn");
	login_btn.onclick = qqWbLogin;
}

/** 后台验证合作登录用户信息 */
function cooperationLoginValidate(accountType, openId, nickName) {
	$.ajax({
		method : cooperationLoginMethod.method,
		url : cooperationLoginMethod.url,
		beforeSend : function(XMLHttpRequest) {
		},
		data : "cooperationLoginId=" + (openId + accountType) + "&accountType=" + accountType + "&nickName=" + encodeURIComponent(encodeURIComponent(nickName)),
		dataType : cooperationLoginMethod.datatype,
		jsonp : 'jsoncallback',
		success : function(data) {
			if (data.status == 'notExist') {
				openBindWin(accountType, openId, nickName, data.message.partyId);
			} else {
				if (window.opener != null) {
					window.opener.location.reload();
				}

				window.location.reload();
			}
		},
		complete : function(XMLHttpRequest, textStatus) {
			closeLoginWin();
		}
	});
}

var htmlObject = function(id) {
	return document.getElementById(id);
}

/**
 * 绑定账号
 * 
 * 入参
 * #accountType				账户类型
 * #openId					openId
 * #nickName				昵称
 * #partyId					会员id
 * 
 * 出参
 * #accountTypeName			账户类型名称
 * #accountTypeName1		账户类型名称
 * #accountNickName			会员昵称
 * user_name_bind_login		不知道
 */
function openBindWin(accountType, openId, nickName, partyId) {
	var win = new WinSize();
	$(".thickdiv").show();
	htmlObject("win3").style.display = "block";
	$('#accountType').val(accountType);
	$('#openId').val(openId);
	$('#nickName').val(nickName);
	$('#partyId').val(partyId);

	if (accountType == "q") {
		$('#accountTypeName').html('QQ');
		$('#accountTypeName1').html('QQ');
	} else if (accountType == "qqwb") {
		$('#accountTypeName').html('腾讯微博');
		$('#accountTypeName1').html('腾讯微博');
	} else if (accountType == "sinawb") {
		$('#accountTypeName').html('新浪微博');
		$('#accountTypeName1').html('新浪微博');
	}

	$('#accountNickName').html(nickName);
	$('#user_name_bind_login').focus();
}

/**
 * 关闭绑定窗口
 * 
 * 出参
 * #bg3						背景窗口id
 * #win3					登录窗口id
 * #user_name_bind_login	绑定用户名称
 * #passwd_bind_login		密码
 * #bind_error				绑定错误信息
 */
function closeBindWin() {
	$(".loginTip").hide();
	$(".thickdiv").hide();
	$("#win3").hide();
	
	$("#user_name_bind_login").val("");
	$("#passwd_bind_login").val("");
	$("#bind_error").html("");
	$("#bind_error").removeClass().addClass("loginerror");

	if (window.opener != null) {
		window.opener.location.reload();
	}

	window.location.reload();
}

/**
 * #loginType				登录类型
 * q						qq登录
 * qqwb						腾讯微博登录
 * sinawb					新浪微博登录					
 */
function cancelCooprationRegist() {
	var loginType = $('#accountType').val();

	if (loginType == 'q') {
		QC.Login.signOut(function() {
			// window.location.href = base_path + "/index.htm";
		});
	} else if (loginType == 'qqwb') {
		T.logout(function() {
			// window.location.href = base_path + "/index.htm";
		});
	} else if (loginType == 'sinawb') {
		WB2.logout(function() {
			// window.location.href = base_path + "/index.htm";
		});
	}

	// closePromptWin();
}

/**
 * 绑定账户
 * 
 * 入参
 * #accountType					账户类型
 * #openId						openId
 * #nickName					昵称
 * #partyId						会员id
 * #user_name_bind_login		绑定用户名称
 * #passwd_bind_login			绑定用户密码
 * 
 * 出参
 * #bind_error					绑定错误
 * #passwd_login				密码
 * 
 */
function doBind() {
	var accountType = $('#accountType').val();
	var openId = $('#openId').val();
	var nickName = $('#nickName').val();
	var partyId = $('#partyId').val();
	var name = $.trim($("#user_name_bind_login").val());
	var password = $.trim($("#passwd_bind_login").val());

	if (name == "") {
		$("#bind_error").html("邮箱/手机号不能为空！");
		$("#user_name_bind_login").focus();
		return false;
	}

	if (password == "") {
		$("#bind_error").html("密码不能为空！");
		$("#passwd_login").focus();
		return false;
	}

	$("#bind_error").html("");

	//调用绑定方法
	$.ajax({
		method : bindAccountMethod.method,
		url : bindAccountMethod.url,
		beforeSend : function(XMLHttpRequest) {
		},
		data : "name=" + name + "&password=" + password + "&cooperationLoginId=" + (openId + accountType) + "&accountType=" + accountType + "&nickName="
				+ encodeURIComponent(encodeURIComponent(nickName)) + "&cooperationLoginPartyId=" + partyId + "&random=" + Math.floor(Math.random() * 100),
		dataType : bindAccountMethod.datatype,
		jsonp : 'jsoncallback',
		success : function(data) {
			if (data.status == "true") {
				showmessage(data.message);

				closeBindWin();
			} else {
				$("#bind_error").html(data.message);
			}
		},
		complete : function(XMLHttpRequest, textStatus) {
		}
	});
}

/**
 * 关闭登录窗口
 * 
 * 出参
 * #bg			登录窗口背景
 * #win			登录窗口
 */
function closeLoginWin() {
	closeLoginDialog();
}

var flag = false;//是否表单验证通过，true:通过，false：未通过
var isLogin = false;//是否登录，true：已登录，false：未登录

function loadEvent() {
	$(document).find(".form_input").keydown(function(event) {
		switch (event.keyCode) {
		case 32:
			return false;
			break
		default:
		}
	});
	
	/**
	 * 在所有含有info属性的标签上添加点击事件，验证是否登录。
	 * 如果未登录弹出登录窗口
	 */
	$("[info]").live("click", function() {
		requestRandom = Math.round(Math.random() * 100) + 1;
		//调用获取用户信息
		$.ajax({
			method : loadEventMethod.method,
			url : loadEventMethod.url + "?random=" + requestRandom,
			beforeSend : function(XMLHttpRequest) {
			},
			dataType : loadEventMethod.datatype,
			jsonp : 'jsoncallback',
			async : false,
			success : function(data) {
				if (data.status == "true") {
					isLogin = true;
				} else {
					isLogin = false;
				}
			},
			error : function(XMLHttpRequest, textStatus, errorThrown) {
				isLogin = false;
			}
		});
		if (!isLogin) {
			openLoginDialog();
			return isLogin;
		}
	});
}

/**
 * 检查是否已登录，未登录打开登录窗口
 */
function checkLogin() {
	
	
	requestRandom = Math.round(Math.random() * 100) + 1;
	//调用获取用户信息
	$.ajax({
		method : checkLoginMethod.method,
		url : checkLoginMethod.url + requestRandom,
		beforeSend : function(XMLHttpRequest) {
		},
		dataType : checkLoginMethod.datatype,
		jsonp : 'jsoncallback',
		async : false,
		success : function(data) {
			if (data.status == "true") {
				isLogin = true;
			} else {
				isLogin = false;
			}
		},
		error : function(XMLHttpRequest, textStatus, errorThrown) {
			isLogin = false;
		}
	});
	if (!isLogin) {
		openLoginDialog();
	}
	
	return isLogin;
	/*
	var tc = new TonysfarmCookie("/");
	if (!tc.isLogin()) {
		openLoginDialog();
		return false;
	}
	return true;*/
}
/**
 * 检查是否已登录，返回boolean。true：已登录，false：未登录
 */
function check_login() {
	requestRandom = Math.round(Math.random() * 100) + 1;
	//调用获取用户信息
	$.ajax({
		method : checkLoginMethod.method,
		url : checkLoginMethod.url + requestRandom,
		beforeSend : function(XMLHttpRequest) {
		},
		dataType : checkLoginMethod.datatype,
		jsonp : 'jsoncallback',
		async : false,
		success : function(data) {
			if (data.status == "true") {
				isLogin = true;
			} else {
				isLogin = false;
			}
		},
		error : function(XMLHttpRequest, textStatus, errorThrown) {
			isLogin = false;
		}
	});
	return isLogin;
}

/**
 * 顶部注册按钮事件
 * 
 * 出参
 * #bt1				注册按钮id
 * #bt2				登录按钮id
 * #register		注册窗口
 * #login			登录窗口
 * 
 * .current			选择时的样式名称
 */
function fbt1() {
	$("#bt1").addClass('current');
	$("#bt2").removeClass('current');
	$("#register").css("display", 'block');
	$("#login").css("display", 'none');
	$(document).unbind("keydown");//解绑keydown
}
/**
 * 顶部登录按钮事件
 * 
 * 出参
 * #bt1				注册按钮id
 * #bt2				登录按钮id
 * #register		注册窗口
 * #login			登录窗口
 * 
 * .current			选择时的样式名称
 * .loginbox		登录框外层div样式
 */
function fbt2() {
	refreshCaptcha('');
	$("#bt2").addClass('current');
	$("#bt1").removeClass('current');
	$("#register").css("display", 'none');
	$("#login").css("display", 'block');
	$("#login .loginbox").show().siblings().hide();
	//添加keydown，当按下enter键时，调用登录方法
	$(document).bind("keydown", function(event) {
		switch (event.keyCode) {
		case 13:
			doLogin();
			break;
		}
	});
}
/**
 * 卡片登录事件
 * 
 * 出参
 * #bt1				注册按钮id
 * #bt2				登录按钮id
 * #register		注册窗口
 * #login			登录窗口
 * 
 * .current			选择时的样式名称
 * .cardloginbox	卡片登录框外层div样式

function fbt3() {
	refreshCaptcha('');//刷新验证码
	$("#bt2").addClass('current');
	$("#bt1").removeClass('current');
	$("#register").css("display", 'none');
	$("#login").css("display", 'block');
	$("#login .cardloginbox").show().siblings().hide();
	//添加keydown，当按下enter键时，调用登录方法
	$(document).bind("keydown", function(event) {
		switch (event.keyCode) {
		case 13:
			doLogin();
			break;
		}
	});
}

function regeml1() {
	var c = $("#regemlopt div").attr("class");

	if (!c || c == "") {
		$("#regmobopt div").removeClass("hiopt");
		$("#regcard div").removeClass("hiopt");
		$("#regemlopt div").addClass("hiopt");
	}

	$("#emailregform").css("display", 'block');
	$("#phoneregform").css("display", 'none');
}

function regmob1() {
	var c = $("#regmobopt div").attr("class");

	if (!c || c == "") {
		$("#regemlopt div").removeClass("hiopt");
		$("#regcard div").removeClass("hiopt");
		$("#regmobopt div").addClass("hiopt");
	}

	$("#emailregform").css("display", 'none');
	$("#phoneregform").css("display", 'block');
}
*/

//function regcard() {
	/*
	 * var c = $("#regcard div").attr("class");
	 * 
	 * if (!c || c == "") { $("#regemlopt div").removeClass("hiopt");
	 * $("#regmobopt div").removeClass("hiopt"); $("#regcard
	 * div").addClass("hiopt"); }
	 */

	//fbt3();

//}

/**
 * 手机注册
 * 
 * 入参
 * #phoneregform 			手机注册form
 * #authcode				手机短信验证码
 * #mobile_input 			手机号
 * #password1				密码
 * #checkbox				注册协议
 * 
 * 出参
 * #DL_overlay				注册成功背景div
 * #DL_window				注册成功消息div
 */
function register() {
	checkMobile('phoneregform');
	if (flag) {
		checkAuthCode();
	}
	if (flag==true && checkPassword1('phoneregform')==true) {
		var form = $("#phoneregform");
		var authcode = $.trim($("#authcode").val());
		var mobile_input = $.trim(form.find("input[id='mobile_input']").val());
		var password_input = $.trim(form.find("input[id='password1']").val());
		var checked = form.find("input[type='checkbox']").attr("checked");
		if (checked != "checked") {
			showmessage("请确认并勾选注册协议！");
			return false;
		}
		var requestRandom = Math.round(Math.random() * 100) + 1;
		//调用手机号注册方法
		$.ajax({
			method : mobileRegistMethod.method,
			url : mobileRegistMethod.url,
			beforeSend : function(XMLHttpRequest) {
			},
			data : "memberDO.mobile_verified=" + mobile_input + "&memberDO.password=" + password_input + "&authCode=" + authcode + "&random=" + requestRandom,
			dataType : mobileRegistMethod.datatype,
			jsonp : 'jsoncallback',
			timeout:10000,
			success : function(data) {
				showmessage(data.message);
				if (data.status == "true") {
					//closeRegisterDialog();
					
					var uid = getLoginUserId();
					/*
					_adwq.push([
					'_setAction','87pmjd', // 注册ID - 固定值 - 必填项(此处无需修改)
					//'userid' // 注册用户名 或 注册用户ID (必填，后期核对数据) 
					uid
					]); 
					*/
					console.log("==============="+uid);
					
					window.location.reload();
				}
				/*else {
					showmessage(data.message);
				}
				*/
			},
			error:function (XMLHttpRequest, textStatus, errorThrown) {
				showmessage('系统忙，请稍后再试！');
			},
			complete : function(XMLHttpRequest, textStatus) {
			}
		});
	}
}

/**
 *	提交注册时消息提示
 */
function alertMessage() {
	showmessage('请求已发送，请稍等！');
	return false;
}

/**
 * 邮箱注册
 * 
 * 入参
 * #emailregform 			邮箱注册form
 * #email_input				注册邮箱
 * #mobile_input 			手机号
 * #password1				密码
 * #checkbox				注册协议
 * 
 * 出参
 * #activeEmail				注册邮箱地址
 * #goEmail					激活链接a标签
 * #register_btn				注册按钮
 */
function register2() {
	checkEmail('emailregform');
	if (flag ==true && checkPassword1('emailregform')==true) {
		var form = $("#emailregform");
		var email_input = $.trim(form.find("#email_input").val());
		var mobile_input = $.trim(form.find("input[id='mobile_input']").val());
		var password_input = $.trim(form.find("input[id='password1']").val());
		var checked = form.find("input[type='checkbox']").attr("checked");
		if (checked != "checked") {
			showmessage("请确认并勾选注册协议！");
			return false;
		}
		var requestRandom = Math.round(Math.random() * 100) + 1;
		//调用邮箱地址注册方法
		$.ajax({
			method : emailRegistMethod.method,
			url : emailRegistMethod.url,
			beforeSend : function(XMLHttpRequest) {
//				form.find("#register_btn").attr("onclick", "javascript:alertMessage();");
			},
			data : "memberDO.email_verified=" + email_input + "&memberDO.mobile=" + mobile_input + "&memberDO.password=" + password_input + "&random=" + requestRandom,
			dataType : emailRegistMethod.datatype,
			jsonp : 'jsoncallback',
			timeout:10000,
			success : function(data) {
				if (data.status == "true"||data.status=="active") {
					
					var uid =data.message.partyId;

					/*					
					_adwq.push([
					'_setAction','87pmjd', // 注册ID - 固定值 - 必填项(此处无需修改)
					//'userid' // 注册用户名 或 注册用户ID (必填，后期核对数据) 
					uid
					]); 
					*/
					console.log("==============="+uid);
					
					closeRegisterDialog();
					var div=$("#win2");
					div.find("#activeEmail").html(data.message.email);
					div.find("#goEmail").attr("href", "/goEmail.htm?email=" + data.message.email);
					div.find("#goEmail").attr("partyId", data.message.partyId);
					sendEmail();
					openEmailDialog();
				} else {
					showmessage(data.message);
				}
			},
			error:function (XMLHttpRequest, textStatus, errorThrown) {
				showmessage('系统忙，请稍后再试！');
			},
			complete : function(XMLHttpRequest, textStatus) {
//				form.find("#register_btn").attr("onclick", "javascript:register2();");
			}
		});
	}
}

/**
 * 手机注册验证手机验证码
 * 
 * 入参
 * #phoneregform 				手机框所在form
 * #authcode					验证码
 * #mobile_input				手机号
 * .check_yes			验证通过时，图片小图标css（对号）
 * .check_no			验证失败，图片小图标css（叉号）
 * .success				验证通过时，文字样式
 */
function checkAuthCode() {
	//检验手机号时候输入正确
	checkMobile('phoneregform');
	var form = $("#phoneregform");
	if (flag) {
		var authcode = $.trim(form.find("#authcode").val());
		var p = /^[0-9]{6}$/;
		if (p.test(authcode)) {
			var mobile_input = $.trim(form.find("input[id='mobile_input']").val());
			var requestRandom = Math.round(Math.random() * 100) + 1;
			//调用验证短信验证码的方法
			$.ajax({
				method : checkAuthCodeMethod.method,
				url : checkAuthCodeMethod.url,
				beforeSend : function(XMLHttpRequest) {
				},
				data : "mobile=" + mobile_input + "&authCode=" + authcode + "&random=" + requestRandom,
				dataType : checkAuthCodeMethod.datatype,
				jsonp : 'jsoncallback',
				success : function(data) {
					if (data.status == "true") {
						form.find("#authcode_message").html("");
						flag = true;
					} else {
						form.find("#authcode_message").html("验证码错误");
						flag = false;
					}
				}
			});
		} else {
			form.find("#authcode_message").html("验证码为6位数字");
			flag = false;
		}
		if(flag){
			form.find("#authcode_message").html("");
		}
	}
}


/**
 * 刷新登录验证码
 * 
 * 出参
 * #loginJCaptcha_image				登录时图片验证码标签（img）
 */
function refreshCaptcha() {
	$('#loginJCaptcha_image').hide().attr('src', captchaUrl + Math.floor(Math.random() * 100)).fadeIn();
}

/**
 * 刷新发送短信验证码
 * 
 * 出参
 * #smsJCaptchaImg				手机注册时图片验证码标签（img）
 */
function refreshsmsCaptcha() {
	$('#smsJCaptchaImg').hide().attr('src', captchaUrl + Math.floor(Math.random() * 100)).fadeIn();
}

function openSmsCode_page(){
	checkMobile('phoneregform');
	if (flag) {
		refreshsmsCaptcha();
		$("#codeTip").show();
	}
}

/**
 * 手机注册，发送验证码
 * 
 * 入参
 * #phoneregform		手机注册所在form
 * #mobile_input		注册手机号
 * #smsJCaptcha			图片验证码输入框
 * 
 * 出参
 * #smsJCaptchaImg		手机注册时图片验证码标签（img）
 * #smsId				发送短信验证按钮
 */
function sendSms() {
	checkMobile('phoneregform');
	var form = $("#phoneregform");
	if (flag) {
		var mobile_input = $.trim(form.find("input[id='mobile_input']").val());
		var requestRandom = Math.round(Math.random() * 100) + 1;
		var smsJCaptcha = $.trim($("#codeTip").find("#smsJCaptcha").val());
		/*if (smsJCaptcha == "") {
			$("#smsJCaptcha_message").html("请输入验证码");
			return;
		} else {
			$("#smsJCaptcha_message").html("");
		}*/

		//验证图片验证码是否输入正确
//		if(!validateSmsJcaptcha()){
//			return ;
//		}
		//发送验证码
		$.ajax({
			method : sendSmsMethod.method,
			url : sendSmsMethod.url,
			beforeSend : function(XMLHttpRequest) {
				$("#smsJCaptcha_message").html("");
				$("#smsJCaptcha").removeClass("bordersty2_wrong");
				$("#code_wrong").hide();
			},
			data : "mobile=" + mobile_input + "&random=" + requestRandom + "&loginJCaptcha=" + smsJCaptcha,
			dataType : sendSmsMethod.datatype,
			jsonp : 'jsoncallback',
			success : function(data) {
				if (data.status == "true") {
					$("#smsId").attr("onclick", "javascript:void(0)");
					countDown("sendSms", 60, "smsId");
					showmessage(data.message);
					$(".thickdiv").hide();
					$("#codeTip").hide();
				}else if(data.status == "refresh"){
					if (smsJCaptcha == "") {
						$("#smsJCaptcha_message").html("请输入防伪码");
						$("#smsJCaptcha").removeClass("bordersty2_wrong");
					} else {
						$("#smsJCaptcha_message").html("防伪码错误");
						$("#smsJCaptcha").removeClass("bordersty2_wrong").addClass("bordersty2_wrong");
						$("#code_wrong").show();
					}
					$(".thickdiv").show();
					$("#codeTip").show();
				}else{
					showmessage(data.message);
				}
				refreshsmsCaptcha();//刷新验证码
			},
			complete : function(XMLHttpRequest, textStatus) {
			}
		});
	}
}

/**
 * 发送手机验证码时，验证图片验证码
 * 
 * 入参
 * #smsJCaptcha 		图片验证码输入框
 * #smsJCaptchaImg		图片验证码标签（img）
 */
function validateSmsJcaptcha() {
	var f = false;
	var form = $("#codeTip");
	var smsJCaptcha = $.trim(form.find("#smsJCaptcha").val());
	if (smsJCaptcha == "") {
		form.find("#smsJCaptcha_message").html("请输入防伪码");
	} else{
		form.find("#smsJCaptcha_message").html("");
		f=true;
	}
	return f;
}
/**
 * 注册时验证密码是否正确，true：正确，false：错误
 * 
 * 入参
 * obj					form id
 * #password1_input		密码
 * #password2_input		重复密码
 * 
 * 出参
 * #phoneregform		手机注册form
 * .check_yes			验证通过时，图片小图标css（对号）
 * .check_no			验证失败，图片小图标css（叉号）
 * .success				验证通过时，文字样式
 */
function checkPassword1(obj) {
	var form = $("#" + obj);
	var password1_input = $.trim(form.find("input[id='password1']").val());
	var password2_input = $.trim(form.find("input[id='password2']").val());
	var p = /^.{6,20}$/;
	if (password1_input == '') {
		form.find("#password1_message").html("请设置密码");
		return false;
	} else if (p.test(password1_input)) {
		if (password1_input != password2_input) {
			form.find("#password1_message").html("");
			if (password2_input == '') {
				form.find("#password2_message").html("请输入确认密码");
			} else {
				form.find("#password2_message").html("两次密码必须相同");
			}
			return false;
		} else {
			var uname = '';
			if (obj == "phoneregform") {
				uname = $.trim(form.find("input[id='mobile_input']").val());
			} else {
				uname = $.trim($("#emailregform").find("input[id='mobile_input']").val());
			}
			if (uname == password2_input && uname != '') {
				form.find("#password1_message").html("不可与账号相同");
				return false;
			} else {
				form.find("#password1_message").html("");
				return true;
			}
		}
	} else {
		form.find("#password1_message").html("密码6-20位");
		return false;
	}
	form.find("#password1_message").html("");
	form.find("#password2_message").html("");
}


/**
 * 验证邮箱地址
 * 
 * 入参
 * obj					form id
 * #email_input			邮箱地址
 * .check_yes			验证通过时，图片小图标css（对号）
 * .check_no			验证失败，图片小图标css（叉号）
 * .success				验证通过时，文字样式
 */
function checkEmail(obj) {
	var form = $("#" + obj);
	var email_input = $.trim(form.find("#email_input").val());

	if (email_input == '') {
		form.find("#email_input_message").html("请填写邮箱");
		flag = false;
	} else if (isEmail(email_input)) {
		if (email_input.length > 60) {
			form.find("#email_input_message").html("邮箱长度小于60位");
			flag = false;
		} else {
			var requestRandom = Math.round(Math.random() * 100) + 1;
			$.ajax({
				method : checkEmailMethod.method,
				url : checkEmailMethod.url,
				beforeSend : function(XMLHttpRequest) {
				},
				data : "memberDO.email_verified=" + email_input + "&random=" + requestRandom,
				async : false,
				dataType : checkEmailMethod.datatype,
				jsonp : 'jsoncallback',
				success : function(data) {
					if (data.status == "true") {
						form.find("#email_input_message").html('');
						flag = true;
					} else {
						form.find("#email_input_message").html(data.message);
						flag = false;
					}
				}
			});
		}
	} else {
		form.find("#email_input_message").html("邮箱格式不正确");
		flag = false;
	}
	return flag;
}

/**
 * 验证手机号
 * 
 * 入参
 * obj					form id
 * #mobile_input		手机号
 * 
 * 出参
 * #messalert			手机号不能使用时的提示。由于最近国家通信部门整治骚扰短信，对于联通和电信的手机用户验证码暂时收不到，建议您使用移动号码或者转到邮箱注册，给您带来不便请您谅解！
 * .check_yes			验证通过时，图片小图标css（对号）
 * .check_no			验证失败，图片小图标css（叉号）
 * .success				验证通过时，文字样式
 */
function checkMobile(obj) {
	var form = $("#" + obj);
	var mobile_input = $.trim(form.find("input[id='mobile_input']").val());
	if (mobile_input == '') {
		form.find("#mobile_input_message").html("请填写手机");
		flag = false;
	} else if (isMobile(mobile_input)) {
		var requestRandom = Math.round(Math.random() * 100) + 1;
		$.ajax({
			method : checkMobileMethod.method,
			url : checkMobileMethod.url,
			async : false,
			beforeSend : function(XMLHttpRequest) {
			},
			data : "memberDO.mobile_verified=" + mobile_input + "&formName=" + obj + "&random=" + requestRandom,
			dataType : checkMobileMethod.datatype,
			jsonp : 'jsoncallback',
			success : function(data) {
				if (data.status == "true") {
					form.find("#mobile_input_message").html('');
					flag = true;
				} else {
					form.find("#mobile_input_message").html(data.message);
					flag = false;
				}
			}
		});
	} else {
		form.find("#mobile_input_message").html("手机号码不正确");
		flag = false;
	}
	if(flag){
		form.find("#mobile_input_message").html("");
	}
	return flag;
}

/**
 * 检查手机号或邮箱格式是否填写
 * 
 * 入参
 * obj					form id
 * #mobile_input		手机号
 * 
 * 出参
 * #messalert			手机号不能使用时的提示。由于最近国家通信部门整治骚扰短信，对于联通和电信的手机用户验证码暂时收不到，建议您使用移动号码或者转到邮箱注册，给您带来不便请您谅解！
 * .check_yes			验证通过时，图片小图标css（对号）
 * .check_no			验证失败，图片小图标css（叉号）
 * .success				验证通过时，文字样式
 */
function checkMobileEmail(obj) {
	var form = $("#" + obj);
	var mobile_input = $.trim(form.find("input[id='mobile_input']").val());

	if (mobile_input == '') {
		form.find("#mobile_input_message").html("请填写手机");
		flag = false;
		// } else if (isTelecomMobile(mobile_input) ||
		// isUnicomMobile(mobile_input)) {//过滤电信和联通号码
		// form.find("input[name='mobile_input']").next().children()
		// .removeClass().addClass("check_no");
		// form.find("input[name='mobile_input']").next().children().next().removeClass("check_no");
		// form.find("input[name='mobile_input']").next().children().next()
		// .html('');
		// $("#messalert").css("display", "block");
		// flag = false;
	} else if (isMobile(mobile_input)) {
		var requestRandom = Math.round(Math.random() * 100) + 1;
		$.ajax({
			method : checkMobileMethod.method,
			url : checkMobileMethod.url,
			async : false,
			beforeSend : function(XMLHttpRequest) {
			},
			data : "memberDO.mobile_verified=" + mobile_input + "&formName=" + obj + "&random=" + requestRandom,
			dataType : checkMobileMethod.datatype,
			jsonp : 'jsoncallback',
			success : function(data) {
				if (data.status == "true") {
					form.find("#mobile_input_message").html('');
					flag = true;
				} else {
					form.find("#mobile_input_message").html(data.message);
					flag = false;
				}
			}
		});
	} else {
		form.find("#mobile_input_message").html("手机号码不正确");
		flag = false;
	}
	return flag;
}

/**
 * 重发邮件，等待过程中再次重发提示
 */
function reSendEmailMessage() {
	showmessage("重发邮件请求已发送，请稍等！");
}
/**
 * 重发注册邮件
 * 
 * 出参
 * #reSendEmail			重发邮件按钮
 */
function reSendEmail() {
	var requestRandom = Math.round(Math.random() * 100) + 1;
	$.ajax({
		method : reSendEmailMethod.method,
		url : reSendEmailMethod.url,
		beforeSend : function(XMLHttpRequest) {
			$("#reSendEmail").attr("href", "javascript:reSendEmailMessage();");
			showmessage("邮件已发送，请注意查收！");
		},
		data : "memberDO.partyId=" + $("#goEmail").attr("partyId") + "&random=" + requestRandom,
		dataType : reSendEmailMethod.datatype,
		jsonp : 'jsoncallback',
		success : function(data) {
			//showmessage(data.message);
		},
		complete : function(XMLHttpRequest, textStatus) {
			$("#reSendEmail").attr("href", "javascript:reSendEmail();");
		}
	});
}

/**
 * 注册成功调用发送邮件
 * 
 * 出参
 * #sendEmail			重发邮件按钮
 */
function sendEmail() {
	var requestRandom = Math.round(Math.random() * 100) + 1;
	$.ajax({
		method : reSendEmailMethod.method,
		url : reSendEmailMethod.url,
		beforeSend : function(XMLHttpRequest) {
			$("#reSendEmail").attr("href", "javascript:reSendEmailMessage();");
		},
		data : "memberDO.partyId=" + $("#goEmail").attr("partyId") + "&random=" + requestRandom,
		dataType : reSendEmailMethod.datatype,
		jsonp : 'jsoncallback',
		success : function(data) {
			//showmessage(data.message);
		},
		complete : function(XMLHttpRequest, textStatus) {
			$("#reSendEmail").attr("href", "javascript:reSendEmail();");
		}
	});
}

/**
 * 手机注册，重发送验证码倒计时
 * 
 * 入参
 * f			发送验证码方法名称
 * secs			等待时间单位：秒
 * smsId		发送短信验证码按钮id
 */
function countDown(f, secs, smsId) {
	$("#" + smsId).html(secs + "秒后重新发送");
	if (--secs > 0) {
		setTimeout("countDown('" + f + "', " + secs + ",'" + smsId + "')", 1000);
	} else {
		$("#" + smsId).html("重发验证码");
		$("#" + smsId).attr("onclick", "javascript:" + f + "();");
	}
}

/**
 * 检查密码强度
 * 
 * 入参
 * #value 密码
 * 
 * 出参
 * num		强度系数
 */
function clientSideStrongPassword(value) {
	if (value.trim().length == 0) {
		return num;
	}

	if (value.length > 0 && value.length < 7) {
		num = 2;
		return num;
	}

	var pat1 = /[a-zA-Z]+/;

	if (pat1.test(value)) {
		++num;
	}

	var pat2 = /[0-9]+/;

	if (pat2.test(value)) {
		++num;
	}

	var chr = "";

	for ( var i = 0; i < value.length; i++) {
		chr = value.substr(i, 1);

		if ("!@#$%^&*()_+-='\";:[{]}\|.>,</?`~".indexOf(chr) >= 0) {
			++num;
			break;
		}
	}

	return num;
}
/**
 * 验证密码强度
 * 
 * 入参
 * obj					form id
 * value				密码
 * 
 * 
 * 出参
 * #passwd_power		强度，弱
 * #passwd_power1		强度，中
 * #passwd_power2		强度，强
 * #pswd_result			强度文字提示
 * .level				密码强度样式，默认
 * .level_1				密码强度样式，弱
 * .level_2				密码强度样式，中
 * .level_3				密码强度样式，强
 */
function setPwdStrengthEx(obj, value) {
	var form = $("#" + obj);
	var ret = clientSideStrongPassword(value);

	if (ret == 1) {
		form.find("#passwd_power").removeClass().addClass("level");
		form.find("#passwd_power1").removeClass().addClass("level");
		form.find("#passwd_power2").removeClass().addClass("level");
		form.find("#pswd_result").html('');
	} else if (ret == 2) {
		form.find("#passwd_power").removeClass().addClass("level level_1");
		form.find("#passwd_power1").removeClass().addClass("level");
		form.find("#passwd_power2").removeClass().addClass("level");
		form.find("#pswd_result").html('弱');
	} else if (ret == 3) {
		form.find("#passwd_power").removeClass().addClass("level level_1");
		form.find("#passwd_power1").removeClass().addClass("level level_2");
		form.find("#passwd_power2").removeClass().addClass("level");
		form.find("#pswd_result").html('中');
	} else if (ret == 4) {
		form.find("#passwd_power").removeClass().addClass("level level_1");
		form.find("#passwd_power1").removeClass().addClass("level level_2");
		form.find("#passwd_power2").removeClass().addClass("level level_3");
		form.find("#pswd_result").html('强');
	}
}

/*
// 未找到使用位置
function chkmob() {
	var mobileNo = $.trim($("#mobile_input").val());
	var v = $("#mobileoption").is(":visible")

	if (v && mobileNo == "") {
		msgerr("mobile", "请填写手机");
		return false;
	}
	if (!v && mobileNo != "" && !isMobile(mobileNo)) {
		msgerr("mobile", "格式不正确");
		return false;
	}
	if (v && !isMobile(mobileNo)) {
		msgerr("mobile", "格式不正确");
		return false;
	}
	if (v)
		msgok("mobile");
	if (!v && mobileNo != "") {
		msgok("mobile");
	}
	if (!v && mobileNo == "") {
		$("#mobile_tip").html("");
		$("#mobile_ico").removeClass("ico-error");
		$("#mobile_ico").removeClass("ico-ok");
	}
	return true;
}
//未找到使用位置
function chkeml() {
	var emailAccount = $.trim($("#email_input").val());
	var v = $("#emailoption").is(":visible")

	if (v && emailAccount == "") {
		msgerr("email", "请填写邮箱");
		return false;
	}
	if (!isEmail(emailAccount)) {
		msgerr("email", "格式不正确");
		return false;
	}
	if (v)
		msgok("email");
	return true;
}

//未找到使用位置
function chkaut() {
	var authcode = $.trim($("#authcode").val());
	var reg = /^[0-9]{6}$/;

	if (authcode == "") {
		msgerr("authcode", "请填写验证码");
		return false;
	}
	if (!reg.test(authcode)) {
		msgerr("authcode", "请输6位数字");
		return false;
	}
	msgok("authcode");
	return true;
}

function chkpwd() {
	var pwd = $.trim($("#password1").val());

	if (pwd == "") {
		msgerr("pwd", "请设置密码");
		return false;
	}
	msgok("pwd");
	return true;
}

function chkpwd2() {
	var pwd = $.trim($("#password1").val());
	var pwd2 = $.trim($("#password2").val());

	if (pwd2 == "") {
		msgerr("pwd2", "请重复输入密码");
		return false;
	}
	if (pwd != "" && pwd != pwd2) {
		msgerr("pwd2", "密码不一致");
		return false;
	}
	msgok("pwd2");
	return true;
}

function msgerr(objId, msg) {
	if (msg != "") {
		$("#" + objId + "_tip").html(msg);
		$("#" + objId + "_ico").removeClass("ico-ok");
		$("#" + objId + "_ico").addClass("ico-error");
		$("#" + objId + "_ico").show();
	}
}

function msgok(objId) {
	$("#" + objId + "_tip").html("");
	$("#" + objId + "_ico").removeClass("ico-error");
	$("#" + objId + "_ico").addClass("ico-ok");
	$("#" + objId + "_ico").show();
}

*/

/**
 * 打开登录窗口
 * 
 * 出参
 * #bt1				注册按钮id
 * #bt2				登录按钮id
 * #register		注册窗口
 * #login			登录窗口
 * #div-authcode    未找到
 * #login_btn		登录id
 */
function openLoginDialog() {
	$(".thickdiv").show();
	$(".loginTip").show();
	$(".loginTip .log").show();
    $(".loginTip .reg").hide();	
    $(".loginTip #loginTab").addClass("curr");
	$(".loginTip #regTab").removeClass("curr");
	
	resetLoginForm();
	
	$(document).bind("keydown", function(event) {
		switch (event.keyCode) {
		case 13:
			doLogin();
			break;
		}
	});

	/*
	if (WB2.checkLogin()) {
		WB2.logout();
	}

	if (QC.Login.check()) {
		QC.Login.signOut();
	}

	if (T.loginStatus()) {
		T.logout();
	}

	initQQ();
	initQQWb();
	initSinaWb();
	*/
}
/**
 * 打开注册窗口
 * 出参
 * #bt1				注册按钮id
 * #bt2				登录按钮id
 * #register		注册窗口
 * #login			登录窗口
 */
function openRegisterDialog() {
	$(".thickdiv").show();
	$(".loginTip").show();
	$(".loginTip .reg").show();
    $(".loginTip .log").hide();	
    $(".loginTip #loginTab").removeClass("curr");
	$(".loginTip #regTab").addClass("curr");
	
	resetMobileRegForm();
	resetEmailRegForm();

	if (WB2.checkLogin()) {
		WB2.logout();
	}

	if (QC.Login.check()) {
		QC.Login.signOut();
	}

	if (T.loginStatus()) {
		T.logout();
	}

	initQQ();
	var login_btn = document.getElementById("login_btn");

	initQQWb();
	initSinaWb();
}

/**
 * 关闭注册窗口
 */
function closeRegisterDialog() {
	$(".thickdiv").hide();
	$(".loginTip").hide();
//	WinTip();
	resetMobileRegForm();
//	closeLoginDialog();
}
/**
 * 关闭登录窗口
 * 
 * 出参
 * #loginJCaptcha_image				登录验证码
 * #user_name_login					登录账号
 * #passwd_login					登录密码
 * #error							错误消息
 * .loginerror						错误消息css
 */
function closeLoginDialog() {
	$("#loginJCaptcha_image").attr("src", "");
	$("#user_name_login").val("");
	$("#passwd_login").val("");
	$("#error").html("");
	$("#error").removeClass().addClass("loginerror");
	$(document).unbind("keydown");
}

/*
function closeUserDialog() {
	$('#loginDialog').hide();
	$('.thickdiv').hide();
}
*/
var T1 = function(id) {
	return document.getElementById(id);
}

function WinTip() {
	var win = new WinSize();
	var Tip = T1("bg");
	Tip.style.width = win.W + "px";
	Tip.style.height = win.H + "px";

	if (Tip.style.display == "block") {
		Tip.style.display = "none";
		// T1("win").style.display = "none";
		var $T1 = $(T1("win"));
		// $T1.slideUp();
		$T1.animate({
			top : "-500px"
		}, 600, function() {
			$T1.hide()
		});
	} else {
		Tip.style.display = "block";
		// T1("win").style.display = "block";
		var $T1 = $(T1("win"));
		// $T1.slideDown();
		$T1.show().animate({
			top : "50px"
		}, 600);
	}
}

function WinSize() {// 函数：获取尺寸
	var winWidth = 0;
	var winHeight = 0;

	yScroll = (document.documentElement.scrollHeight > document.documentElement.clientHeight) ? document.documentElement.scrollHeight : document.documentElement.clientHeight;
	xScroll = (document.documentElement.scrollWidth > document.documentElement.clientWidth) ? document.documentElement.scrollWidth : document.documentElement.scrollWidth;

	return {
		"W" : xScroll,
		"H" : yScroll - 37
	}
}

/**
 * 重置手机注册表单
 * 
 * 出参
 * #phoneregform				注册table
 * #mobile_input				手机号
 * #mobile_input_message		手机号错误提示
 * #smsJCaptcha					发送短信图片验证码
 * #smsJCaptcha_message			发送短信图片验证码错误提示
 * #authcode				    手机注册短信验证码
 * #authcode_message		    手机注册短信验证码错误提示
 * #password1				    手机注册密码
 * #password1_message		    手机注册密码错误提示
 * #password2				    手机注册确认密码
 * #password2_message		    手机注册确认密码错误提示
 *  
 */
function resetMobileRegForm() {
	refreshsmsCaptcha();
	var div=$("#phoneregform");
	div.find("#mobile_input").val("");
	div.find("#mobile_input_message").html("");
	
	div.find("#smsJCaptcha").val("");
	div.find("#smsJCaptcha_message").html("");
	
	div.find("#authcode").val("");
	div.find("#authcode_message").html("");

	div.find("#password1").val("");
	div.find("#password1_message").html("");
	
	div.find("#password2").val("");
	div.find("#password2_message").html("");
	
	div.find("#phoneReg_error").html("");
	
	div.find("input[type='checkbox']").attr("checked","checked");
}

/**
 * 重置邮箱注册表单
 * 
 * 出参
 * #emailregform				注册table
 * #email_input					手机号
 * #email_input_message			手机号错误提示
 * #password1				    手机注册密码
 * #password1_message		    手机注册密码错误提示
 * #password2				    手机注册确认密码
 * #password2_message		    手机注册确认密码错误提示
 *  
 */
function resetEmailRegForm() {
	var div=$("#emailregform");
	div.find("#email_input").val("");
	div.find("#email_input_message").html("");

	div.find("#password1").val("");
	div.find("#password1_message").html("");
	
	div.find("#password2").val("");
	div.find("#password2_message").html("");
	
	div.find("#emailReg_error").html("");
	
	div.find("input[type='checkbox']").attr("checked","checked");
}

/**
 * 重置登录表单
 * 
 * 出参
 * #user_name_login				登录账号
 * #user_name_login_message		登录账号错误提示
 * #passwd_login				密码
 * #passwd_login_message		密码错误提示
 * #loginJCaptcha				登录图片验证码
 * #loginJCaptcha_message		登录图片验证码错误提示
 * #forever				    	记住我
 *  
 */
function resetLoginForm() {
	refreshCaptcha();
	$("#login_auth_code_div").hide();
	$("#error").html("");
	$("#user_name_login").val("");
	$("#user_name_login_message").html("");
	$("#passwd_login").val("");
	$("#passwd_login_message").html("");
	$("#loginJCaptcha").val("");
	$("#loginJCaptcha_message").html("");
	$("#forever").removeAttr("checked");
}

function openEmailDialog(){
	if($("#win2").is(":visible")){
		$(".thickdiv").hide();
		$("#win2").hide();
	}else{
		$(".thickdiv").show();
		$("#win2").show();
	}
}

function search_product(id){
	var key=$.trim($("#"+id).val());
	if(key==""){
		key=$.trim($("#"+id).attr("placeholder"));
	}
	window.location.href="/search.html?keyword="+key;
}

function getLoginUserId(){
	var tc = new TonysfarmCookie("/");
	if (tc.isLogin()) {
		var uid = decodeURIComponent(tc.getAccountId());
		return uid;
	}else{
		return "";
	}
}
