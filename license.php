<?php 
$msg = '';
$msg_reg='';
$reg_done = false;

if(isset($_POST["license_key"] ) && ($_POST["license_key"]!="")) {
	$res = wp_remote_post("http://plugmatter.com/activate",
			array('body'=>array(
					"license_key"=>$_POST["license_key"],
					"siteurl"=>get_option('siteurl'),
					"package"=>Plugmatter_PACKAGE,	
					)
				)
			);
	$res_arr = explode(":",$res["body"]);
	if($res_arr[0] == "VERIFIED") {
		update_option('Plugmatter_PACKAGE', $res_arr[1]);
		update_option('Plugmatter_Featurebox_License', $_POST["license_key"]);
		$msg="<div class='pm_msg_success'><strong>Plugmatter Feature Box activated successfully</strong></div>";
	} else {
		$msg="<div class='pm_msg_error'><strong>Invalid License Key</strong></div>";
	}
} else if(isset($_POST["email"]) || isset($_POST["first_name"])) {
    if(isset($_POST["first_name"]) && $_POST["first_name"] == "") {
        $msg_reg="<div class='pm_msg_error'><strong>Please enter your First Name</strong></div>";
    } else if(!eregi("^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$", $_POST["email"])) {
        $msg_reg="<div class='pm_msg_error'><strong>Please Enter a Valid Email</strong></div>";
    } else {
	   $res = wp_remote_post("http://plugmatter.com/register_lite",
			array('body'=>array(
					"first_name"=>$_POST["first_name"],
                    "email"=>$_POST["email"]
					)
				)
			);
	   $res_arr = explode(":",$res["body"]);
       if($res_arr[0] == "ERROR") {
           $msg_reg="<div class='pm_msg_error'><strong>".$res_arr[1]."</strong></div>";
       } else {
           $msg_reg="<div class='pm_msg_success'><strong>".$res_arr[1]."</strong></div>";
           $reg_done = true;
       }
    }

}
//------------------------------------------------------------------------------------------------------
?>
<div class='pmadmin_wrap'>
	<div class='pmadmin_headbar'>
		<div class='pmadmin_pagetitle'><h2>General Settings</h2></div>
	    <div class='pmadmin_logodiv'><img src='<?php echo plugins_url()."/".Plugmatter_DIR_NAME."/images/logo.png";?>' height='35'></div>
	</div>
	<?php 	
	if($msg!='') { 
		echo "$msg";
	}
	if(get_option('Plugmatter_Featurebox_License') != "") {	
	?>
	<div class='pmadmin_body'  style="position:relative">
        <br>
        <!--<b>Congratulations! You have successfully installed the Plugin. One Quick Reminder Below...</b>-->
        <b><span style='color:red'>REMINDER:</span> Have you added the code snippet to your theme file to make the plugin work?</b>
        <br><br>
        If yes, then <b>Get Started</b> below.        
        <br><br>
		<div class="pmadmin_submit">
			<input id="submit" class="pm_primary_buttons" type="submit" value=" Get Started " onclick="location.href='<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=settings_submenu-page'" name="submit">
		</div>        
        <br><br>
        <b>OR</b>
        <br><br>
        Follow the help instructions in the user guide to add it. Click on the respective theme/framework you use:
        <br>
        <ul style='margin-left:35px;'>
            <li style='list-style:disc'><a href='http://plugmatter.com/user-guide#add_code' target="_blank">Any WordPress Theme</a></li>
            <li style='list-style:disc'><a href='http://plugmatter.com/user-guide#genesis' target="_blank">Thesis Framework</a></li>
            <li style='list-style:disc'><a href='http://plugmatter.com/user-guide#thesis' target="_blank">Genesis Framework</a></li>
        </ul>
        However, if you're not comfortable doing it yourself, <a href='mailto:support@plugmatter.com' target="_blank"><b>shoot us an email</b></a> and we will be glad to set it up for you.
		<br><br>	
	<?php
	} else {
	?>
	
	<div class='pmadmin_body'  style="position:relative">
    <div style='padding-bottom:30px;'>
        To get Support for your plugin from Plugmatter, you need to enter your Plugmatter License Key sent to you in your "License Key Email". Please contact <a href='http://plugmatter.com/support' target='_blank'>support</a> if you're unable to find your License Key.
    </div>        
	<form action="<?php $siteurl = get_option('siteurl');echo $siteurl."/wp-admin/admin.php?page=license_submenu_page"; ?>" id='pm_settings' method="post">	
		<div>
			<div class='plug_enable_lable' style='width:250px'>Enter Your License Key</div>
			<div class='plug_tgl_btn'>
				<input type='text' name='license_key' size='45' style='padding:6px;'>
			</div>
			<div style='clear:both'>&nbsp;</div>
		</div>
		<div class="pmadmin_submit">
			<input id="submit" class="pm_primary_buttons" type="submit" value="   Register Plugin   " name="submit">
		</div>
		<br><br>
	</form>
    <?php if(Plugmatter_PACKAGE == "plug_featurebox_lite") { ?>
    <div style="border-top:1px solid #ddd;padding-bottom:10px;">&nbsp;</div>
    <div class='plug_enable_lable' style='font-weight:bold;width:350px;'>Don't Have a License Key? Register Now!</div>
    <div style='padding-bottom:15px;clear:both;padding-top:10px;'>Your license key will be sent to your registered email.</div>
    <?php
    if($msg_reg!='') { 
		echo $msg_reg;
	} 
    if($reg_done != true) {
    ?><br><br>
	<form action="<?php $siteurl = get_option('siteurl');echo $siteurl."/wp-admin/admin.php?page=license_submenu_page"; ?>" id='pm_settings' method="post">	
		<div>
			<div class='plug_enable_lable' style='width:150px'>First Name</div>
			<div class='plug_tgl_btn'>
				<input type='text' name='first_name' size='30' style='padding:4px;' value='<?php echo isset($_POST["first_name"])?$_POST["first_name"]:""; ?>'>
			</div>
			<div style='clear:both'>&nbsp;</div>
			<div class='plug_enable_lable' style='width:150px'>Email</div>
			<div class='plug_tgl_btn'>
				<input type='text' name='email' size='30' style='padding:4px;' value='<?php echo isset($_POST["email"])?$_POST["email"]:""; ?>'>
			</div>
			<div style='clear:both'>&nbsp;</div>            
		</div>
		<div class="pmadmin_submit" style='margin-top:10px;'>
			<input id="submit" class="pm_primary_buttons" type="submit" value="   Get License key   " name="submit">
		</div>
		<br><br>
	</form>        
    <?php } } ?>
	</div>
	<?php } ?>
</div>