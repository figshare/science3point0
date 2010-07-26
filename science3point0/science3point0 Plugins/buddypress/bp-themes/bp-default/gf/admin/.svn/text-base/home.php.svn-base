<?php gf_admin_links();?>
<?php
if(gf_is_manage_forum())
    locate_template(array("gf/admin/manage-forums.php"),true);
 else if(gf_is_manage_users()) 
     locate_template(array("gf/admin/manage-users.php"),true);
 else if(gf_is_settings())
     locate_template(array("gf/admin/settings.php"),true);
else if(gf_is_forum_create())
	locate_template(array("gf/admin/create-forum.php"),true);
elseif(gf_is_forum_edit())
locate_template(array("gf/admin/edit-forum.php"),true);
elseif(gf_is_forum_delete())
locate_template(array("gf/admin/delete-forum.php"),true);
else
	locate_template(array("gf/admin/index.php"),true);

?>