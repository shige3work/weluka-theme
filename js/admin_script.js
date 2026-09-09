/**
 * admin js script
 * @package Weluka
 * @since Weluka Theme 00 1.0
 * modify v1.2.9.2
 * 
 */
jQuery(document).ready(function(){

	jQuery(document).on('change', 'select#page_template', _welukaPtemplateSelectChange );

	function _welukaPtemplateSelectChange() {
		var ptemplate_box = jQuery('#weluka_ptemp_meta');
		var this_value = jQuery(this).val();
		ptemplate_box.find('.inside > div').css('display','none');
		
		switch ( this_value ) {
			case 'page-blog.php':
				ptemplate_box.find('.weluka_pt_blog').css('display','block')
				break;
			default:
                ptemplate_box.find('.weluka_pt_info').css('display','block');
		}
	}
});

jQuery(window).on('load', function() {
	jQuery('select#page_template').trigger('change');
});

