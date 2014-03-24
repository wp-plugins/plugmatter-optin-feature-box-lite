<?php 
	$plugmatter_enable = get_option('plugmatter_enable');	
	
	if($plugmatter_enable != '1') {
		return;
	}	 

 	$plugmatter_show_temp_optinuser = get_option('plugmatter_show_temp_optinuser');
	if(isset($_COOKIE['plugmatter_conv_done']) && $_COOKIE['plugmatter_conv_done'] == '1' && $plugmatter_show_temp_optinuser == '0' ){	 
		 return ;
	}
	
	if(!is_home()) {
		$pm_post_meta = get_post_meta(get_the_ID(), "pm_meta_templ_id", true);
	}

	get_basic_template();
	 	  
	function get_basic_template(){
		$plugmatter_global_template = get_option('plugmatter_global_template');
  		$template_id = $plugmatter_global_template ;
	 	if($template_id != ""){
	 		show_template($template_id);
	 	}
	}
	 	  
	 	  
	function show_template($template_id, $ab_meta="") {
		global $wpdb;
	  	$custom_css="";
	 	$pm_service_action="";
	 	$pm_service_hiddens="";
        $pm_box_width = "max-width:960px;";
	 	     
	 	$table = $wpdb->prefix.'plugmatter_templates';

	 	$result = $wpdb->get_row("SELECT id,temp_name,base_temp_name,params FROM $table WHERE id= $template_id	");
	 	if(!$result) return;
	 	 
	 	$id=$result->id;
	 	$temp_name=$result->temp_name;
  		$base_temp_name=$result->base_temp_name;
  		$params=$result->params;
	 	  		 	  	
  	    $obj = json_decode($params);  	   

		foreach($obj as $doc){
			if($doc->type == "alignment") {
				if($doc->width != 0) $pm_box_width = "max-width:".$doc->width."px;max-width:".$doc->width."px;";
				$custom_css.= "#pm_featurebox { $pm_box_width margin: ".$doc->top_margin."px auto ".$doc->bottom_margin."px; }"; 
			} else if($doc->type == "text") {
	 	  		$pm_h1 = $doc->params->text;		 	  			  			
	 	  		$custom_css.= "h1#".$doc->id."{color:".$doc->params->color."; font-family:".$doc->params->font_family."; }" ;
	 	  		$gwf1 = urlencode($doc->params->font_family);
	 	  	} else if($doc->type == "textarea") {
	 	  		$pm_description = $doc->params->html;	 	  			  
	 	  		$custom_css.= "#".$doc->id."{color:".$doc->params->color."; font-size:". $doc->params->font_size ."; font-family:".$doc->params->font_family."; }" ;
	 	  		$gwf2 = urlencode($doc->params->font_family);	 	  			  
	 	  	} else if($doc->type == "color") {
				if($doc->params->gradient == "yes"){
					$custom_css.= "#".$doc->id."{background: -moz-linear-gradient(left,  ".$doc->params->bgcolor." 57%, rgba(255,255,255,0.48) 72%, rgba(255,255,255,0.03) 85%, rgba(255,255,255,0) 86%); /* FF3.6+ */
					background: -webkit-gradient(linear, left top, right top, color-stop(57%,".$doc->params->bgcolor."), color-stop(72%,rgba(255,255,255,0.48)), color-stop(85%,rgba(255,255,255,0.03)), color-stop(86%,rgba(255,255,255,0))); /* Chrome,Safari4+ */
					background: -webkit-linear-gradient(left,  ".$doc->params->bgcolor." 57%,rgba(255,255,255,0.48) 72%,rgba(255,255,255,0.03) 85%,rgba(255,255,255,0) 86%); /* Chrome10+,Safari5.1+ */
					background: -o-linear-gradient(left,  ".$doc->params->bgcolor." 57%,rgba(255,255,255,0.48) 72%,rgba(255,255,255,0.03) 85%,rgba(255,255,255,0) 86%); /* Opera 11.10+ */
					background: -ms-linear-gradient(left,  ".$doc->params->bgcolor." 57%,rgba(255,255,255,0.48) 72%,rgba(255,255,255,0.03) 85%,rgba(255,255,255,0) 86%); /* IE10+ */
					background: linear-gradient(to right,  ".$doc->params->bgcolor." 57%,rgba(255,255,255,0.48) 72%,rgba(255,255,255,0.03) 85%,rgba(255,255,255,0) 86%); /* W3C */
					filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=".$doc->params->bgcolor.", endColorstr='#00ffffff',GradientType=1 ); /* IE6-9 */
	 	  			}";
				} else {
					$custom_css.= "#".$doc->id."{background-color:".$doc->params->bgcolor."; }" ;
				}	 	  			 
	 	  	} else if($doc->type == "image") {
				$img_url = $doc->params->img_url;
	 	  		$custom_css .= "#".$doc->id."{background-image:".$img_url.";}" ;
	 	  	} else if($doc->type == "video") {
				$pm_video_src = $doc->params->video_src;
	 	  	} else if($doc->type == "button") {
				$pm_btn_txt = $doc->params->text;
				$pm_btn_class = $doc->params->btn_class;
	 	  	} else if($doc->type == "service") {
	 	  		$service_meta = array("Aweber" => array("action_url" => "http://www.aweber.com/scripts/addlead.pl","name" => "email", "name_field" => "name"),
							  "GetResponse" => array("action_url" => "https://app.getresponse.com/add_contact_webform.html","name" => "email", "name_field" => "name"),
							  "iContact" => array("action_url" => "https://app.icontact.com/icp/signup.php","name" => "fields_email", "name_field" => "fields_fname"),
							  "MailChimp" => array("action_url" => (isset($doc->params->action_url)?$doc->params->action_url:""), "name" => "EMAIL", "name_field" => "FNAME"),
							  "ConstantContact" => array("action_url" => "http://www.formstack.com/forms/index.php","name" => "email", "name_field" => "first_name"),
                              "CampaignMonitor" => array("action_url" => "http://".(isset($doc->params->cm_account_name)?$doc->params->cm_account_name:"").".createsend.com/t/r/s/".(isset($doc->params->cm_id)?$doc->params->cm_id:"")."/","name" => "cm-".(isset($doc->params->cm_id)?$doc->params->cm_id."-".$doc->params->cm_id:""), "name_field" => "cm-name"),                                      
	 	  					  "InfusionSoft" => array("action_url" => "https://ke128.infusionsoft.com/app/form/process/".(isset($doc->params->inf_form_xid)?$doc->params->inf_form_xid:""), "name" => "inf_field_Email", "name_field" => "inf_field_FirstName"),
	 	  					  "Feedburner" => array("action_url" => "http://feedburner.google.com/fb/a/mailverify","name" => "email", "name_field" => "name"),	 	  				
							  "MadMimi" => array("action_url" => "https://madmimi.com/signups/subscribe/". (isset($doc->params->webform_id)?$doc->params->webform_id:""),"name" => "signup[email]", "name_field" => "signup[name]"),	 	  											
							  "MailPoet" => array("action_url" => "#pm_mailpoet","name" => "email", "name_field" => "name"),	 	  																		  
	 	  					  "Custom" => array(),
				);
				$service_name = $doc->params->service;
				$pm_service_action = $service_meta[$service_name]['action_url'];	
				$pm_input_name = $service_meta[$service_name]['name'];
				$pm_input_name_field_name = $service_meta[$service_name]['name_field'];
				foreach($doc->params as $key=>$value){
				 	if($key != "service"){
						if($service_name == "Aweber" && $key == "redirect_url") {
							$key = "redirect";
						}                        
				 		$pm_service_hiddens.="<input type='hidden' name='".$key."' value='".$value."' />";								 		
				 	}
				}					
	 	  	}
	 	}
	 	if($doc->type != "user_designed_template") {
	 		wp_enqueue_style('pm_button_style', plugins_url('/css/pm_btn_style.css', __FILE__));
	 		
	 		wp_enqueue_style('custom-style', plugins_url('/templates/'.$base_temp_name.'/style.css', __FILE__));
	 		wp_add_inline_style( 'custom-style', $custom_css );
	 	  	  
	 		wp_register_style('pm_gwf1', "http://fonts.googleapis.com/css?family=$gwf1");
	 		wp_enqueue_style('pm_gwf1');
	 	  	  
	 		wp_register_style('pm_gwf2', "http://fonts.googleapis.com/css?family=$gwf2");
	 		wp_enqueue_style('pm_gwf2');	 	  	  	 	
	 		 	  	 	  	  
	 		include_once (Plugmatter_FILE_PATH."/templates/".$base_temp_name."/template.php");	 	  	
	    }
	    wp_register_script('pm_frontend_js',plugins_url('js/frontend.js', __FILE__), array('jquery'));
	    wp_enqueue_script('pm_frontend_js');
	}
?>	 	