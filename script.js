/**
 * DokuWiki Plugin likeit
 *
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  lisps    
 */

/* DOKUWIKI:include_once script/jquery.hoverIntent.js */

jQuery(function(){

	jQuery(document).on('click','.plugin_likeit.button',function(e) {
		var $this = jQuery(this);
		var $container = $this.parent();
		
		if($container.hasClass('disabled')) return false;
	
		$container.addClass('loading');
		ajaxedit_send2(
				'likeit',
				ajaxedit_getIdxByIdClass(escapeStr($container.attr('id')),"plugin_likeit.container:not('.disabled')"),
				function(data) {
					$container.removeClass('loading');
					ret = ajaxedit_parse(data);
					if (ajaxedit_checkResponse(ret)) {
						if(ret.list && ret.count) {
							$container.find('.plugin_likeit.list').html(ret.list);
							$container.find('.plugin_likeit.count').html(ret.count);
						}
					}
				},
				{}	
			);
	});
	
	jQuery('.plugin_likeit.container').hoverIntent({
		over:function() {jQuery(this).find('.plugin_likeit.list').show(100);}, //callback function to trigger
		out:function() {jQuery(this).find('.plugin_likeit.list').hide(100);},
		sensitivity:6,
		interval:600, //polling interval
		
	});
	
	
	function escapeStr(str) 
	{
		if (str)
			return str.replace(/([ #;?%&,.+*~\':"!^$[\]()=>|\/@])/g,'\\$1');      

		return str;
	}
});
 

