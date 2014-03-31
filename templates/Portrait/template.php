	<div  class="pmedit" pm_meta="color" id="pm_featurebox" ab_meta="<?php echo $ab_meta ?>">
		<div id='pm_featurebox_con'>
		<div id="pm_polaroid">	
			<div class="pmedit" pm_meta="image" id="pm_image" pm_image_size="293x293"></div>
		</div>
		<div id="pm_content">
			<div id="pm_h1_div">
				<h1 class="pmedit" pm_meta="text" def_font="Roboto Slab" id="pm_h1"><?php echo $pm_h1 ?></h1>		
			</div>
			<div class="pmedit pm_description" pm_meta="textarea" def_font="Open Sans" id="pm_description">
				<?php echo $pm_description ?>
			</div>
			<div>
				<form id="pm_form_submit" action='<?php echo $pm_service_action; ?>'  method="post" >				
				<div class="pmedit pm_input_div" pm_meta="service" id="pm_form">
				    <input type="text" name='<?php echo $pm_input_name; ?>' placeholder="<?php echo $pm_email_input_txt ?>" size='55' id="pm_input" /> 
				</div>
				<div id="pm_button_div" class="pmedit" pm_meta="button" >
				    <input type="button" value="<?php echo $pm_btn_txt ?>" class="<?php echo $pm_btn_class ?>"  id="pm_button"/>
				</div>			
				<div class='pm_clear'>&nbsp;</div>
			 	<?php echo isset($pm_service_hiddens)?$pm_service_hiddens:""; ?>
			 	</form>				
			</div>
		</div>
		</div>
	</div>
