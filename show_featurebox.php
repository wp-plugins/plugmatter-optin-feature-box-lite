<?php
function show_template($template_id, $ab_meta="") {
		global $wpdb;
	  	$custom_css=" ";
	 	$pm_service_action=" ";
	 	$pm_service_hiddens=" ";
	 	$pm_meta_tid = " ";
	 	     
	 	$table = $wpdb->prefix.'plugmatter_templates';

	 	$result = $wpdb->get_row("SELECT id,temp_name,base_temp_name,params FROM $table WHERE id= $template_id	");
	 	if(!$result) return;
	 	 
	 	$id=$result->id;
	 	$temp_name=$result->temp_name;
  		$base_temp_name=$result->base_temp_name;
  		$params=$result->params;

  		if(get_option("pmfb_track_analytics")){
  			$pm_meta_tid = $id."_".$temp_name;	
  		}
  		
	 	  		 	  	
  	    $obj = json_decode($params);  	   
        $gwf1arr = array();

		foreach($obj as $doc){
			if($doc->type == "alignment") {
				if($doc->width != 0) $pm_box_width = "max-width:".$doc->width."px;max-width:".$doc->width."px;";
				@$custom_css.= "#pm_featurebox { $pm_box_width margin: ".$doc->top_margin."px auto ".$doc->bottom_margin."px; }"; 
			} else if($doc->type == "pm_form_fields") {
				$pm_load_style  = $doc->fields_required;
			} else if($doc->type == "pm_custom_css") {
				$pm_custom_css  = $doc->pm_custom_css;
			} else if($doc->type == "user_designed_template"){
				 $page_id = $doc->id;
				 $thispost = get_post( $page_id );
				 $content = do_shortcode($thispost->post_content);
                 echo "<style>$custom_css</style>";
				 echo "<div  class='pmedit' pm_meta='color' id='pm_featurebox' ab_meta='$ab_meta' >".$content."</div>";				
			} else	if($doc->type == "text") {
                $objid = $doc->id;
	 	  		$$objid = $doc->params->text;		 	  			  			
                $elem_type = explode("_", $objid);
                $pre_selector = $elem_type[1]."#";
	 	  		@$custom_css.= $pre_selector.$doc->id."{color:".$doc->params->color."; font-family:".$doc->params->font_family."; font-weight:".$doc->params->variant." }" ;
	 	  		$gwf1arr[] = urlencode($doc->params->font_family);
	 	  	} else if($doc->type == "textarea") {
	 	  		$pm_description = $doc->params->html;	 	  			  
	 	  		$custom_css.= "#".$doc->id."{color:".$doc->params->color."; font-size:". $doc->params->font_size ."; font-family:".$doc->params->font_family."; }" ;
	 	  		$gwf2 = urlencode($doc->params->font_family);	 	  			  
	 	  	} else if($doc->type == "color") {
				if($doc->params->gradient == "yes"){
					$custom_css.= "#".$doc->id."{
						background: linear-gradient(to right,  ".$doc->params->bgcolor." 57%,rgba(255,255,255,0.48) 72%,rgba(255,255,255,0.03) 85%,rgba(255,255,255,0) 86%); /* W3C */
						background: -moz-linear-gradient(left,  ".$doc->params->bgcolor." 57%, rgba(255,255,255,0.48) 72%, rgba(255,255,255,0.03) 85%, rgba(255,255,255,0) 86%); /* FF3.6+ */
						background: -webkit-gradient(linear, left top, right top, color-stop(57%,".$doc->params->bgcolor."), color-stop(72%,rgba(255,255,255,0.48)), color-stop(85%,rgba(255,255,255,0.03)), color-stop(86%,rgba(255,255,255,0))); /* Chrome,Safari4+ */
						background: -webkit-linear-gradient(left,  ".$doc->params->bgcolor." 57%,rgba(255,255,255,0.48) 72%,rgba(255,255,255,0.03) 85%,rgba(255,255,255,0) 86%); /* Chrome10+,Safari5.1+ */
						background: -o-linear-gradient(left,  ".$doc->params->bgcolor." 57%,rgba(255,255,255,0.48) 72%,rgba(255,255,255,0.03) 85%,rgba(255,255,255,0) 86%); /* Opera 11.10+ */
						background: -ms-linear-gradient(left,  ".$doc->params->bgcolor." 57%,rgba(255,255,255,0.48) 72%,rgba(255,255,255,0.03) 85%,rgba(255,255,255,0) 86%); /* IE10+ */						
						filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=".$doc->params->bgcolor.", endColorstr='#00ffffff',GradientType=1 ); /* IE6-9 */
	 	  			}";
	 	  			$custom_css.="@media only screen and (min-width : 768px) and (max-width : 1023px) { ".
	 	  				"#".$doc->id. " {
							    background: linear-gradient(to right, ".$doc->params->bgcolor." 60%, rgba(255, 255, 255, 0.48) 70%, rgba(255, 255, 255, 0.03) 85%, rgba(255, 255, 255, 0) 86%) repeat scroll 0 0 rgba(0, 0, 0, 0) !important;
							    background: -moz-linear-gradient(left,  ".$doc->params->bgcolor.") 60%, ".$doc->params->bgcolor.") 70%, rgba(255,255,255,0.03) 85%, rgba(255,255,255,0) 86%) !important;
								background: -webkit-gradient(linear, left top, right top, color-stop(60%,rgba(255,255,255,1)), color-stop(70%,rgba(255,255,255,0.48)), color-stop(85%,rgba(255,255,255,0.03)), color-stop(86%,rgba(255,255,255,0))) !important;
								background: -webkit-linear-gradient(left,  ".$doc->params->bgcolor." 60%,rgba(255,255,255,0.48) 70%,rgba(255,255,255,0.03) 85%,rgba(255,255,255,0) 86%) !important;
								background: -o-linear-gradient(left,  ".$doc->params->bgcolor." 60%,rgba(255,255,255,0.48) 70%,rgba(255,255,255,0.03) 85%,rgba(255,255,255,0) 86%); !important;
								background: -ms-linear-gradient(left,  ".$doc->params->bgcolor." 60%,rgba(255,255,255,0.48) 70%,rgba(255,255,255,0.03) 85%,rgba(255,255,255,0) 86%); !important;
								height: 338px !important;

							}
							#pm_content { background-color :  ".$doc->params->bgcolor." }
	 	  			}  ";
	 	  			$custom_css.= "@media only screen and (min-width : 480px) and (max-width : 767px) {".
	 	  				"#".$doc->id. " {
							padding-top: 292px !important;
							background: linear-gradient(to top, ".$doc->params->bgcolor." 1%, rgba(255, 255, 255, 0.48) 28%, rgba(255, 255, 255, 0.03) 80%, rgba(255, 255, 255, 0) 86%) repeat scroll 0 0 rgba(0, 0, 0, 0) !important; /* W3C */
						    background: -moz-linear-gradient(center bottom,  ".$doc->params->bgcolor." 13%, rgba(255,255,255,0.48) 28%, rgba(255,255,255,0.03) 80%, rgba(255,255,255,0) 86%) !important; 
							background: -webkit-gradient(linear, bottom top, right top, color-stop(1%,".$doc->params->bgcolor."), color-stop(28%,rgba(255,255,255,0.48)), color-stop(80%,rgba(255,255,255,0.03)), color-stop(86%,rgba(255,255,255,0))) !important; /* Chrome,Safari4+ */
							background: -webkit-linear-gradient(bottom,  ".$doc->params->bgcolor." 1%,rgba(255,255,255,0.48) 28%,rgba(255,255,255,0.03) 80%,rgba(255,255,255,0) 86%) !important; /* Chrome10+,Safari5.1+ */
							background: -o-linear-gradient(bottom, ".$doc->params->bgcolor." 1%,rgba(255,255,255,0.48) 28%,rgba(255,255,255,0.03) 80%,rgba(255,255,255,0) 86%) !important; /* Opera 11.10+ */
							background: -ms-linear-gradient(bottom, ".$doc->params->bgcolor." 1%,rgba(255,255,255,0.48) 28%,rgba(255,255,255,0.03) 80%,rgba(255,255,255,0) 86%) !important; /* IE10+ */
							top: 0px !important;
							left: 0px !important;
						}
						#pm_content { background-color :  ".$doc->params->bgcolor." }
						#pm_h1_div { padding: 10px 0px; }
						#pm_featurebox { height: 572px !important; }
					   .pm_description { height: 78px;}
	 	  			}";
	 	  			$custom_css.= "@media only screen and (min-width : 320px) and (max-width : 479px) { ".
	 	  				"#".$doc->id. " {
							padding-top:300px;
							background: linear-gradient(to top, ".$doc->params->bgcolor." 1%, rgba(255, 255, 255, 0.48) 28%, rgba(255, 255, 255, 0.03) 80%, rgba(255, 255, 255, 0) 86%) repeat scroll 0 0 rgba(0, 0, 0, 0) !important;
						    background: -moz-linear-gradient(center bottom,  ".$doc->params->bgcolor." 13%, rgba(255,255,255,0.48) 28%, rgba(255,255,255,0.03) 80%, rgba(255,255,255,0) 86%) !important; 
							background: -webkit-gradient(linear, bottom top, right top, color-stop(1%, ".$doc->params->bgcolor." ), color-stop(28%,rgba(255,255,255,0.48)), color-stop(80%,rgba(255,255,255,0.03)), color-stop(86%,rgba(255,255,255,0))) !important;
							background: -webkit-linear-gradient(bottom,  ".$doc->params->bgcolor." 1%,rgba(255,255,255,0.48) 28%,rgba(255,255,255,0.03) 80%,rgba(255,255,255,0) 86%) !important;
							background: -o-linear-gradient(bottom, ".$doc->params->bgcolor." 1%,rgba(255,255,255,0.48) 28%,rgba(255,255,255,0.03) 80%,rgba(255,255,255,0) 86%) !important;
							background: -ms-linear-gradient(bottom, ".$doc->params->bgcolor." 1%,rgba(255,255,255,0.48) 28%,rgba(255,255,255,0.03) 80%,rgba(255,255,255,0) 86%) !important;
							top: 0px !important;
							left: 0px !important;
						}
						#pm_featurebox { height: 680px !important; }
						#pm_content { background-color :  ".$doc->params->bgcolor." }
					}";
					$custom_css.= "@media only screen and (min-width : 240px) and (max-width: 319px){ ".
	 	  				"#".$doc->id. " {
							padding-top:350px;
							background: linear-gradient(to top, ".$doc->params->bgcolor." 52%, rgba(255, 255, 255, 0.48) 65%, rgba(255, 255, 255, 0.03) 80%, rgba(255, 255, 255, 0) 86%) repeat scroll 0 0 rgba(0, 0, 0, 0) !important; /* W3C */
						    background: -moz-linear-gradient(center bottom,  ".$doc->params->bgcolor." 13%, rgba(255,255,255,0.48) 45%, rgba(255,255,255,0.03) 80%, rgba(255,255,255,0) 86%) !important; /* FF3.6+ */
							background: -webkit-gradient(linear, bottom top, right top, color-stop(52%,".$doc->params->bgcolor."), color-stop(65%,rgba(255,255,255,0.48)), color-stop(80%,rgba(255,255,255,0.03)), color-stop(86%,rgba(255,255,255,0))) !important; /* Chrome,Safari4+ */
							background: -webkit-linear-gradient(bottom,  ".$doc->params->bgcolor." 52%,rgba(255,255,255,0.48) 65%,rgba(255,255,255,0.03) 80%,rgba(255,255,255,0) 86%) !important; /* Chrome10+,Safari5.1+ */
							background: -o-linear-gradient(bottom, ".$doc->params->bgcolor." 52%,rgba(255,255,255,0.48) 65%,rgba(255,255,255,0.03) 80%,rgba(255,255,255,0) 86%) !important; /* Opera 11.10+ */
							background: -ms-linear-gradient(bottom, ".$doc->params->bgcolor." 52%,rgba(255,255,255,0.48) 65%,rgba(255,255,255,0.03) 80%,rgba(255,255,255,0) 86%) !important; /* IE10+ */
							top: 0px !important;
							left: 0px !important;
						}
						#pm_content { background-color :  ".$doc->params->bgcolor." }
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
                $pm_email_input_txt = $doc->params->email_input;
                $pm_name_input_txt = $doc->params->name_input;                
	 	  	} else if($doc->type == "service") {

	 	  		@$http_prep = substr($doc->params->action_url, 0, 4);
	 	  		if($http_prep != "http"){
	 	  			@$doc->params->action_url = "http:".$doc->params->action_url;
	 	  		}
	 	  		
	 	  		@$service_meta = array("Aweber" => array("action_url" => "http://www.aweber.com/scripts/addlead.pl","name" => "email", "name_field" => "name"),
							  "GetResponse" => array("action_url" => "https://app.getresponse.com/add_contact_webform.html","name" => "email", "name_field" => "name"),
							  "iContact" => array("action_url" => "https://app.icontact.com/icp/signup.php","name" => "fields_email", "name_field" => "fields_fname"),
							  "MailChimp" => array("action_url" => $doc->params->action_url, "name" => "MERGE0", "name_field" => "MERGE1"),
							  "MailChimp_SingleOptin" => array("action_url" => "#pm_mailchimp_singloptin", "name" => "MERGE0", "name_field" => "MERGE1"),
							  "ConstantContact" => array("action_url" => "#pm_constantcontact","name" => "cc_email", "name_field" => "cc_firstname"),
                              "CampaignMonitor" => array("action_url" => "http://".$doc->params->cm_account_name.".createsend.com/t/r/s/".$doc->params->cm_id."/","name" => "cm-".$doc->params->cm_id."-".$doc->params->cm_id, "name_field" => "cm-name"),
	 	  					  "InfusionSoft" => array("action_url" => "https://".$doc->params->account_subdomain.".infusionsoft.com/app/form/process/".$doc->params->inf_form_xid, "name" => "inf_field_Email", "name_field" => "inf_field_FirstName"),
	 	  					  "Feedburner" => array("action_url" => "http://feedburner.google.com/fb/a/mailverify","name" => "email", "name_field" => "name"),	 	  				
							  "MadMimi" => array("action_url" => "https://madmimi.com/signups/subscribe/".$doc->params->webform_id,"name" => "signup[email]", "name_field" => "signup[name]"),	 	  											
							  "MailPoet" => array("action_url" => "#pm_mailpoet","name" => "email", "name_field" => "name"),	 	  																		  
                              "Feedblitz" => array("action_url" => "http://www.feedblitz.com/f/f.fbz?Sub=".(isset($doc->params->sub)?$doc->params->sub:""),"name" => "email", "name_field" => "name"),
                              "Ontraport" => array("action_url" => "//forms.ontraport.com/v2.4/form_processor.php?","name" => "email", "name_field" => "firstname"),
                              "SendInBlue" => array("action_url" => "https://my.sendinblue.com/users/subscribe/js_id/".(isset($doc->params->js_id)?$doc->params->js_id:"")."/id/1","name" => "email", "name_field" => "NAME"),                                      
                              "Jetpack" => array("action_url" => "#pm_jetpack","name" => "email", "name_field" => "name"),                                      
                              "Custom" => array("action_url" => (isset($doc->params->action_url)?$doc->params->action_url:""), "name" => (isset($doc->params->email_field_name)?$doc->params->email_field_name:""), "name_field" => (isset($doc->params->name_field_name)?$doc->params->name_field_name:""))
				);
				$service_name = $doc->params->service;
				$pm_service_action = $service_meta[$service_name]['action_url'];	
				$pm_input_name = $service_meta[$service_name]['name'];
				$pm_input_name_field_name = $service_meta[$service_name]['name_field'];
                
                if($doc->params->service == "Jetpack") {
                    $pm_service_hiddens.="<input type='hidden' name='jetpack_subscriptions_widget' value='subscribe' />";
                }
                if($doc->params->service == "ConstantContact" || $doc->params->service == "MailChimp_SingleOptin") {
                    $pm_service_hiddens.="<input type='hidden' name='pmfb_tid' value='".$template_id."' />";
                }
                
                foreach($doc->params as $key=>$value){
				 	if($key != "service"){
						if($service_name == "Aweber" && $key == "redirect_url") {
							$key = "redirect";
						}
						if($service_name == "iContact" && $key == "redirect_url") {
							$key = "redirect";
						}						
						if($service_name == "iContact" && $key == "specialid") {
							$key = "specialid:" . $doc->params->listid;
						}      


				 		if($service_name == "Custom") {
							if(strpos($key, "value") !== false) {
								$tmp_custom_key = substr($key,0, -6);
								$custom_key = $doc->params->$tmp_custom_key;
								$custom_value = $value;
								$pm_service_hiddens.="<input type='hidden' name='".$custom_key."' value='".$custom_value."' />";
							}
						} else if ($doc->params->service == "ConstantContact") {
							if($key == "cc_redirect_url") {
								$pm_service_hiddens.="<input type='hidden' name='".$key."' value='".$value."' />";					
							}
							// ignore all the other fields ... not add in hidden fields
						}else {  
                            if($key != "action_url") {
				 			    $pm_service_hiddens.="<input type='hidden' name='".$key."' value='".$value."' />";					
                            }
						}

				 	}
				}
	 	  	}
	 	}
		
		if($pm_custom_css) $custom_css .= $pm_custom_css;
		
	 	if($doc->type != "user_designed_template") {
	 		wp_enqueue_style('pm_button_style', plugins_url('/css/pm_btn_style.css', __FILE__));	 		
	 		wp_register_style('pm_custom-style', plugins_url('/templates/'.$base_temp_name.'/style.css', __FILE__));

			wp_add_inline_style('pm_custom-style', $custom_css );
			wp_enqueue_style('pm_custom-style');

			if($pm_load_style == "pm_email_fname") {
	 			wp_register_style('pm_custom-style2', plugins_url('/templates/'.$base_temp_name.'/twofields.css', __FILE__));
	 		} 

	 		if($pm_load_style == "pm_email_only") {
	 			wp_register_style('pm_custom-style2', plugins_url('/templates/'.$base_temp_name.'/onefield.css', __FILE__));
	 		}
	 		wp_enqueue_style('pm_custom-style2');

            wp_register_style('pm_bootstrap', plugins_url('/css/pm_bootstrap.css', __FILE__));
	 		wp_enqueue_style('pm_bootstrap');
	 	  	  
	 		wp_register_style('pm_gwf1', "//fonts.googleapis.com/css?family=".implode("|", $gwf1arr));
	 		wp_enqueue_style('pm_gwf1');
	 	  	  
	 		wp_register_style('pm_gwf2', "//fonts.googleapis.com/css?family=$gwf2");
	 		wp_enqueue_style('pm_gwf2');	 	  	  	 	
	 		 	  	 	  	  
	 		include_once (Plugmatter_FILE_PATH."/templates/".$base_temp_name."/template.php");	 	  	
	    }
	    wp_register_script('pm_frontend_js',plugins_url('js/frontend.js', __FILE__), array('jquery'));
	    wp_enqueue_script('pm_frontend_js');
	}

?>

