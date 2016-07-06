/**
 * Created by huanglele on 2016/7/6.
 */
/***********弹框显示隐藏: 赠品部分 start *******/
var pophide = function(){
    var $this = $(this);
    $('#J-present-box').addClass('pop-hide');
    setTimeout(function () {
        $('#J-present-mask').animate({'opacity':0},300,'ease')
    }, 200);
    setTimeout(function () {
        $('#J-present-box').css({'display':'none'}).removeClass('pop-hide')
        $('#J-present-mask').css({'display':'none','opacity':'1'});
    }, 1000)
}

var popshow = function(){
    $('#J-present-box').css({'display':'block'})
    $('#J-present-mask').css({'display':'block'})
}

$('#J-present-close').on('click',pophide);
$('#J-present-show').on('click',popshow);
/*************************弹框显示隐藏**end ************/

/**************************购物车删除动画**start************/
$('#navDelete').on('click', function() {
    if ($(this).attr('data-state') == "close") {
        $(this).attr('data-state', 'open').html('取消').addClass('on')
        $('.item-wrap').animate({
            'right' : 50
        }, 300, 'ease')
        $('.del-btn').animate({
            'right' : 0
        }, 300, 'ease')

    } else {
        $(this).attr('data-state', 'close').html('删除').removeClass('on')
        $('.item-wrap').animate({
            'right' : 0
        }, 300, 'ease')
        $('.del-btn').animate({
            'right' : -50
        }, 300, 'ease')
    }
});
//删除购物车单个商品,需要重新发送请求
$("#card-list").on("click",".del-btn", function(event) {
    var productId = $(this).attr("data-id");
    $.ajax({
        url:base_path + '/common/delCart',
        data:{
            id:productId
        },
        success:function(res){
            if(res.status){
                $('#item'+productId).remove();
                refreshCartInfo();
            }
        }
    })
    event.preventDefault();// 阻止a标签href事件
});
/*********************购物车删除 结束**********************/

/*********************增加修改购物车中购买数量 start************/
var plus = function(){
    //event.preventDefault();//阻止a标签href事件
    var $this = $(this);
    var $valText = $this.prev();
    var val = $valText.val();
    $valText.val(++val);
    $this.prev().prev().removeClass('minus_disabled');
    var productId = $this.attr("value");
    var productNum = val;
    refreshCartInfo();
    event.preventDefault();// 阻止a标签href事件
}
//减少数量
var minus = function(){
    //event.preventDefault();//阻止a标签href事件
    var $this = $(this);
    var $valText = $this.next();
    var val = $valText.val();
    if(val < 2)
        return;
    if(val<3){
        $this.addClass('minus_disabled');
    }
    $valText.val(--val);
    var productId = $this.attr("value");
    var productNum = val;
    refreshCartInfo();
    refreshCartInfo
    event.preventDefault();// 阻止a标签href事件
}
$('#card-list').on('click','.minus',minus);
$('#card-list').on('click','.plus',plus);

/*********************增加修改购物车中购买数量 end************/

// 为点击数量的input元素设置取消事件冒泡
$("#card-list").on('click','.buyNum',function(){
    return false;
});


/**
 * 修改购物车中商品
 */
refreshCartInfo = function() {
    var goodsNum = 0;
    var amount = 0;
    $('#card-list>li').each(function(){
        var num = $('input[name="num[]"]',$(this)).val();
        var price = $('.buy_price',$(this)).html();
        var subtotal = numMulti(num, price);
        amount += subtotal;
        goodsNum++;
    })
    if(goodsNum==0){
        $('.join-pay').css('background','rgba(0,0,0,0.1)');
        var html = '<div class="empty" data-describe="没有找到商品信息"> <div class="pic"> <i class="pic_icon pi_classify"></i> </div> <div class="txt"> <p>您的购物车空空如也</p> </div> </div>';
        $('#card-list').html(html);
    }
    $('#itemSize').html(goodsNum);
    $('#amountMoney').html(amount);
}

/***************执行去结算,跳转到订单确认页面************/
var doCartOrder = function(){
    var productNum = $("#itemSize").text();
    if(productNum=='0'){	// 购物车中没有商品，就不去结算了
        $(".join-pay").removeAttr("href");
        // 设置按钮的背景色
        $(".join-pay").css('background', 'rgba(0,0,0,0.1)');
        alert("您的购物车没有商品，不能结算");
        return false;
    }else{
        $('#form').submit();
    }
}
// 去结算
$(".join-pay").on('click',doCartOrder);
// 设置显示删除成功提示信息
function showAddResult(obj){
    if($('.altmsg').attr('name')=='off'){
        $('.altmsg').attr('name','on').removeClass('altmsg-anima-off').addClass('altmsg-anima');
        setTimeout(function() {
            $('.altmsg').removeClass('altmsg-anima').addClass('altmsg-anima-off').attr('name','off');
        }, 1000);
    }else{
        $('.altmsg').attr('name','off').removeClass('altmsg-anima');
    }
}

/**
 * 乘法运算，避免数据相乘小数点后产生多位数和计算精度损失。
 *
 * @param num1被乘数 | num2乘数
 */
function numMulti(num1, num2) {
    var baseNum = 0;
    try {
        baseNum += num1.toString().split(".")[1].length;
    } catch (e) {
    }
    try {
        baseNum += num2.toString().split(".")[1].length;
    } catch (e) {
    }
    return Number(num1.toString().replace(".", "")) * Number(num2.toString().replace(".", "")) / Math.pow(10, baseNum);
};
