jQuery(document).ready(function($){  
	//checkbox-tree
	if ($("ul.expandable").length>0) {
		$("ul.expandable").collapsibleCheckboxTree({
			  // When checking a box, all parents are checked (Default: true)
				   checkParents : false,
			  // When checking a box, all children are checked (Default: false)
				   checkChildren : false,
			  // When unchecking a box, all children are unchecked (Default: true)
				   uncheckChildren : true,
			  // 'expand' (fully expanded), 'collapse' (fully collapsed) or 'default'
				   initialState : 'default'

		 });
	}
	//visual|html editor
	$('a.toggleVisual').click(
		function() {

			var id = oqp_get_form_textarea_id(this);
			tinyMCE.execCommand('mceAddControl', false, id);
			return false;
		}
	);
	$('a.toggleHTML').click(
		function() {
			var id = oqp_get_form_textarea_id(this);
			tinyMCE.execCommand('mceRemoveControl', false, id);
			return false;
		}
	);
})  

function oqp_get_form_textarea_id(selector) {
	var form = jQuery(selector).parents('.oqp-form');
	var textarea = form.find('#oqp_desc');
	return textarea.attr('id');
}