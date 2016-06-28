$(document).ready(function() {

	//回复
	/*
	$(".replySingleComment").live('click', function() {
		
		var commentDataId = $(this).attr("commentDataId");
		
		var replyDiv = "#reply" + commentDataId;
		
		var _display = $(replyDiv).attr("class");
		
		if(_display == "replay_hide"){
			$(replyDiv).attr("class", "replay_warp");
		}else{
			$(replyDiv).attr("class", "replay_hide");
		}
	});
	*/
	
	//发表评论
	$(".replay_btn").live('click', function() {
		var commentDataId = $(this).attr("commentDataId");
		
 		var content = $("#commentReplyContent" + commentDataId).val();
		if(!content){
			alert("请输入回复内容！");
			$("#commentReplyContent" + commentDataId).focus();
			return;
		}
		if(content.length >500){
			alert("评论字数内容最多只能输入500个字符！");
			return;
		}
		 
		 
		$.ajax({
			type: "get",
			dataType:"json",    
			data :"commentDataId=" + commentDataId + "&content="+content,
			url: "other/.com/comment/reply.htm",
			success:function(json) {
				
				if(json.status=='success'){
					replySuccess(commentDataId);
			 	}else{
			 		alert(json.message);
			 	}
				
			}
		});

	});
	
	//上一页
	$(".prePage,.prePage2").live('click', function() {

		var currentPage = eval($("#currentPage").attr("value"));
		
		if(currentPage > 1){
			loadComment(currentPage - 1, 0);
		}

	});
	
	
	//下一页
	$(".nextPage,.nextPage2").live('click', function() {
		var totalPage = eval($("#commentTotalPage").attr("value"));
		
		var currentPage = eval($("#currentPage").attr("value"));
		
		if(currentPage < totalPage){
			loadComment(currentPage + 1, 0);
		}
		
	});
	
	//初始加载评论
	(function(){
 		loadComment(1,0);
	})()

});

function loadComment(cuurentPage, point){
	//套餐评论和单品评论都取pkgProduct
	var itemId = $("#pkgProduct").val();
	if(itemId == "0")
		itemId = "1808";

	//alert(itemId);
	
	var loadCommentUrl = "other/.com/comment/loadByStream.htm";

	$.ajax({
		method : "POST",
		url : loadCommentUrl,
		dataType : "JSONP",
		jsonp : "callBack",
		cache : false,
		data  : "itemId=" + itemId + "&currentPage=" + cuurentPage + "&point=" + point,
		success : function(data) {
			loadCommentData(data[0], cuurentPage);
		}
	});
	
}

function loadCommentData(data, cuurentPage){
	
	var comment = data.comment;
	var totalPage = data.totalPage;
	
	if(comment == null){
		return;
	}
	
	var commentText = '<input id="commentTotalPage" type="hidden" value="' + totalPage  + '">';
	
	$.each(data.commentDataList, function(n, commentData) {
		
		var content = commentData.content;
		var replies = commentData.replys;

		commentText += '<div class="comment_list">';
		commentText += '  <div class="user"><img height="60" width="60" src="' + commentData.avatar + '"><P>' + commentData.memberName + '</P></div>';
		commentText += '  <div id="comment_cont_22" class="comment_cont">';
		commentText += '    <div class="item-reply-lz">';
		commentText += '      <div class="reply-list">';
		commentText += '        <div class="time">' + commentData.releaseTimeDisplay + '</div>';
		commentText += '        <div class="star"><span class="star' + commentData.point + '"></span>' + commentData.point + '分 '+ commentData.manyi + '</div> ';
		commentText += '        <div class="comment_text">' + content + '</div>';
		//commentText += '        <div class="replybtn" id="commentDataDiv' + commentData.commentDataId + '"><a class="replySingleComment" commentDataId="' + commentData.commentDataId + '">回复</a></div>';
		commentText += '        <div class="replybtn" id="commentDataDiv' + commentData.commentDataId + '"></div>';
		commentText += '        <div id="reply' + commentData.commentDataId + '" class="replay_hide"> <i></i>';
		commentText += '          <h3>回复 <span>' + commentData.memberName + '</span></h3> ';
		commentText += '          <textarea class="replay" rows="" cols="" id="commentReplyContent' + commentData.commentDataId + '"></textarea>';
		commentText += '          <input type="button" class="replay_btn" commentDataId="' + commentData.commentDataId + '">';
		commentText += '        </div>';
		commentText += '      </div>';
		
		commentText += '      <div id="item-reply-wrap">';
		
		$.each(commentData.replys, function(m, replyData) {
			//alert(replyData.memberName);
		  commentText += '      <div class="item-reply"> <strong>' + replyData.floor + '</strong>';
		  commentText += '        <div class="reply-list">';
		  commentText += '          <div class="comment_text"><b>' + replyData.memberName + '：</b>' + replyData.content + '</div>';
		  commentText += '          <div class="replybtn"><span>' + replyData.releaseTimeDisplay + '</span> </div>';
		  //commentText += '          <div style="display: block;" class="replay_warp" id="replay_183"> <i></i><h3>回复 <span>天**涯</span></h3>';
		  //commentText += '                        <textarea name="content_183" id="content_183" cols="" rows="" class="replay"></textarea>';
		  //commentText += '                        <input name="" onclick="replay(183);" class="replay_btn" type="button">';
		  //commentText += '          </div>';
		  commentText += '        </div>';
		  commentText += '      </div>';
		  
		});

		commentText += '      </div>'; //item-reply-wrap
		commentText += '    </div>';
		commentText += '  </div>';
		commentText += '  </div>';     
		
	});
	
	$("#item_comment").html(commentText);
	
	refreshCommentInfo(comment);

	refreshCommentNextPage(comment, cuurentPage);
}


function refreshCommentInfo(comment){
	
	if(comment == null){
		return;
	}
	$("#totalCommentCount").html(comment.total);
	
	$("#commentCountAtDetail").html(comment.total);
	
	$("#commentGoodPercentage").html(comment.goodPercentage);
	
	$("#commentGoodPercentageTotal").html(comment.goodPercentage);
	
	$("#commentRatingPercentage").html(comment.ratingPercentage);
	
	$("#commentBadPercentage").html(comment.badPercentage);
	
	$("#commentGoodPercentageWidth").attr("style", "width:" + comment.goodPercentage);
	
	$("#commentRatingPercentageWidth").attr("style" , "width:" +  comment.ratingPercentage);
	
	$("#commentBadPercentageWidth").attr("style", "width:" + comment.badPercentage);
	
}

function refreshCommentNextPage(comment, cuurentPage){
	var pageText = "";
	
	if(comment == null){
		$("#commentNextPage").html(pageText);
		
		return;
	}

	pageText = '<a href="#" class="prePage">上一页</a> ' + 
		'<a href="#" class="prePage2"></a>' +
		'<input id="currentPage" type="text" value="' + cuurentPage + '" class="currPage"/>' + 
    '<a href="#" class="nextPage2"></a>' + 
    '<a href="#" class="nextPage">下一页</a>';
	
	$("#commentNextPage").html(pageText);
	
}

function replySuccess(commentDataId){
	var replyDiv = "#reply" + commentDataId;
	$(replyDiv).attr("class", "replay_hide");
	location.reload();
	
	/*
	var replyText = "";
	replyText += '      <div class="item-reply"> <strong>' + replyData.floor + '</strong>';
	replyText += '        <div class="reply-list">';
	replyText += '          <div class="comment_text"><b>' + replyData.memberName + '：</b>' + replyData.content + '</div>';
	replyText += '          <div class="replybtn"><span>' + replyData.releaseTimeDisplay + '</span> </div>';
	replyText += '        </div>';
	replyText += '      </div>';
	*/
}