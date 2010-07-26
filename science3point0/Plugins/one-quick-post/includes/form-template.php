<?php



function oqp_form_taxonomies_html($taxonomies) {
	
	if (empty($taxonomies)) return false;
	
	global $blog_id;

	foreach($taxonomies as $tax_slug=>$taxonomy) {

	
		unset($tax_obj);
		unset($tax_html);
		
		$tax_obj = get_taxonomy( $tax_slug );
	
		//is it hierarchical ?
		if (!isset($taxonomy['hierarchical'])) {
			$taxonomy['hierarchical']=$tax_obj->hierarchical;
		}

		
		$html='<p>'."\n";
		$html.="\t".'<label for="oqp_'.$tax_obj->name.'">'.$tax_obj->label.'</label>'."\n";
		
		if (!$taxonomy['hierarchical']) {
			//TO FIX TO CHECK
			$tax_html.=oqp_js_autocomplete($blog_id,"oqp_form_".$form_id,"oqp_".$tax_obj->name,$tax_obj->name);
			$tax_html.="\t".'<input type="text" name="oqp_'.$tax_obj->name.'" id="oqp_'.$tax_obj->name.'" class="autocomplete" value="'.$taxonomy['selected'].'"/>'."\n";
		}else {

			$tax_hierarchical_defaults=array(
				'type'=>'checkbox',
				'style'=>false,
				'taxonomy'=>$tax_obj->name,
				'echo'=>false,
				'hide_empty'=>false
			);
			
			$taxonomy = wp_parse_args($taxonomy,$tax_hierarchical_defaults);

				
				$tax_html.="\t".'<ul class="expandable" id="oqp_tax_'.$tax_obj->name.'">'."\n";
				$tax_html.="\t".oqp_terms_list($taxonomy,$blog_id)."\n";
				$tax_html.="\t</ul>\n";
		}
		$html.=apply_filters('oqp_form_render_taxonomy_'.$tax_obj->name,$tax_html,$tax_obj->name,$taxonomy['selected']);
		
		$html.='</p>'."\n";
		
		echo $html;
	}
}


//choose blog form
function oqp_switch_blog_form($post_id,$user_id,$blog_select=true) {
	global $blog_id;

	$options=get_option('oqp_options');
	
	if (!$selected_blog_id)
		$selected_blog_id=$blog_id;

	if (!oqp_is_multiste()) return false;

	if ((!$blog_select) || ($post_id)) {//disabled blog selection if forbidden or if editing a post
		return false;
	}
	

	
	if (!$user_id) {
		$dummy=oqp_get_dummy_user();
		$user_id=$dummy->ID;
	}


	$blogs = oqp_get_blogs_of_user($user_id);		

	
	//remove current blog
	if (!empty($blogs)) {
		foreach ($blogs as $key=>$blog) {
			if ($blog->userblog_id==$blog_id)
				unset ($blogs[$key]);
				//TO FIX TO DO
				//remove blogs where the post type selected is not enabled ?
				
		}
	}
	
	if (empty($blogs)) return false;

	?>
	<p>
		<label for="blog">
			<?php $the_selected_blog=get_blog_details($blog_id);?>
			<?php _e('Blog');?>: <em><?php echo $the_selected_blog->blogname;?></em>
		</label><br/>
			<select name="oqp-switch-blog-id" id="oqp-switch-blog-id" onchange="this.form.submit()">
				<option value=""><?php _e('Use another blog','oqp');?></option>
				<?php 
				foreach ($blogs as $blog) {

					$ublog_name=$blog->blogname;
					$ublog_id=$blog->userblog_id;
					
					
					
					echo'<option value="'.$ublog_id.'">'.$ublog_name.'</option>';
					echo"<br/>";
				}
				
				?>

			</select>
	</p>
	<input type="hidden" name="oqp-action" value="blog-switch"/>

<?php
}


?>