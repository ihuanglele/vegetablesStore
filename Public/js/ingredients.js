function search_product(id) {
    var key = $.trim($("#"+id).val());
    if (key == "") {
        key=$.trim($("#"+id).attr("placeholder"));
    }
    window.location.href = "/search.html?keyword=" + key;
}