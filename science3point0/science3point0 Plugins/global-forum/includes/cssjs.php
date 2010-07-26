<?php
//include css
add_action("wp_print_styles","gf_enqueue_css");
//we do no need any js for global forums
//load css from the current theme directory or default back to plugin
function gf_enqueue_css(){
    //load stylesheet from active theme or plugin
     if ( file_exists(STYLESHEETPATH . '/gf/css/style.css'))
            $theme_uri=get_stylesheet_directory_uri();//child theme
    else if ( file_exists(TEMPLATEPATH . '/gf/css/style.css') )
	    $theme_uri=get_template_directory_uri();//parent theme
    else $theme_uri=GF_PLUGIN_URL;
    if(!empty($theme_uri)){
        $stylesheet_uri=$theme_uri."/gf/css/style.css";
        wp_enqueue_style("global-forum", $stylesheet_uri);
    }
}

?>