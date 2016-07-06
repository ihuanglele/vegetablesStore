M = {
		$mask : $('#my_mask'),
		$msg  : $('#msg'),
		$btnLeft : $('#btnLeft'),
		$btnRight : $('#btnRight'),
		$loading : $('#loading'),
		show  : function(){
			M.$mask.show();
			return M;
		},
		hide  : function(){
			M.$mask.hide();
			return M;
		},
		setMsg : function(value){
			M.$msg.text(value);
			return M;
		},
		setButton : function(txt,url,$btn){
			$btn.text(txt).on('click',function(){
				if(url)
					goUrl(url);
				else
					M.$mask.hide();
			});
			return M;
		},
		setButtonLeft : function(txt,url){
			return M.setButton(txt,url,M.$btnLeft);
		},
		setButtonRight : function(txt,url){
			return M.setButton(txt,url,M.$btnRight);
		},
		setLoading : function(){
			M.$loading.show();
			M.$loading.prev().hide();
			return M;
		},
		setMask : function(){
			M.$loading.hide();
			M.$loading.prev().show();
			return M;
		}
};