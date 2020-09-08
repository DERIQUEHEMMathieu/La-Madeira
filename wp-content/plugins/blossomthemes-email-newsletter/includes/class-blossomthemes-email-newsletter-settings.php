<?php
/**
 * Settings section of the plugin.
 *
 * Maintain a list of functions that are used for settings purposes of the plugin
 *
 * @package    BlossomThemes Email Newsletters
 * @subpackage BlossomThemes_Email_Newsletters/includes
 * @author    blossomthemes
 */
class BlossomThemes_Email_Newsletter_Settings {

	function __construct()
	{	
		add_action( 'wp_ajax_bten_get_platform', array( $this, 'bten_get_platform' ) );	
	}

	function bten_get_platform(){
		if ($_POST['calling_action'] == 'bten_platform_settings') 
    	{
        	echo $this->bten_platform_settings($_POST['platform']);
        	exit;
	    }
	}
	
	function mailerlite_lists($api_key='')
	{
		$blossomthemes_email_newsletter_settings = get_option( 'blossomthemes_email_newsletter_settings', true );

		if( empty($api_key) && isset( $blossomthemes_email_newsletter_settings['mailerlite']['api-key'] ) && $blossomthemes_email_newsletter_settings['mailerlite']['api-key'] !='' )
		{
			$api_key =  $blossomthemes_email_newsletter_settings['mailerlite']['api-key'];
		}

		$key = preg_replace('/[^a-z0-9]/i', '', $api_key);
        $ML_Lists = new ML_Lists($key);
        $lists = $ML_Lists->getAll();
        $data = json_decode($lists, TRUE);
        return $data;
		
	}

	function mailchimp_lists($api_key='')
	{
		$blossomthemes_email_newsletter_settings = get_option( 'blossomthemes_email_newsletter_settings', true );

		if( empty($api_key ) && isset( $blossomthemes_email_newsletter_settings['mailchimp']['api-key'] ) && $blossomthemes_email_newsletter_settings['mailchimp']['api-key'] !='' )
		{
			$api_key =  $blossomthemes_email_newsletter_settings['mailchimp']['api-key'];
		}

        $MC_Lists = new MC_Lists($api_key);
        $lists = $MC_Lists->getAll();
        $data = json_decode($lists, TRUE);
        return $data;
		
	}

	/**
	 * Get Sendinblue lists.
	 *
	 * @return void
	 */
	function sendinblue_lists() {
		$lists = array();

		$blossomthemes_email_newsletter_settings = get_option( 'blossomthemes_email_newsletter_settings', false );

		if ( ! $blossomthemes_email_newsletter_settings ) {
			return $lists;
		}

		$api_key = isset( $blossomthemes_email_newsletter_settings['sendinblue']['api-key'] ) ? esc_attr( $blossomthemes_email_newsletter_settings['sendinblue']['api-key'] ) : '';
		// get lists.
		$lists = get_transient( 'bten_sib_list_' . md5( $api_key ) );
		if ( false === $lists || false == $lists ) {
			
			$mailin    = new SendinblueApiClient();
			$lists     = array();
			$list_data = $mailin->getAllLists();

			if (!empty($list_data['lists'])) {
				foreach ( $list_data['lists'] as $value ) {
					if ( 'Temp - DOUBLE OPTIN' == $value['name'] ) {
						$tempList = $value['id'];
						update_option( 'bten_sib_temp_list', $tempList );
						continue;
					}
					$lists[] = array(
						'id'   => $value['id'],
						'name' => $value['name'],
					);
				}
			}
		}
		if ( count( $lists ) > 0 ) {
			set_transient( 'bten_sib_list_' . md5( $api_key ), $lists, 4 * HOUR_IN_SECONDS );
		}
		return $lists;
	}

	function convertkit_lists($apikey = '')
	{
		$blossomthemes_email_newsletter_settings = get_option( 'blossomthemes_email_newsletter_settings', true );
		$list = array();
		if( empty($apikey ) && isset( $blossomthemes_email_newsletter_settings['convertkit']['api-key'] ) && $blossomthemes_email_newsletter_settings['convertkit']['api-key'] !='' )
		{			
			$apikey = $blossomthemes_email_newsletter_settings['convertkit']['api-key'];
		}
			
			$api = new Convertkit($apikey);

			try {
				$result = $api->getForms();

				if (isset($result->forms)) {
				    foreach ($result->forms as $l) {
				    	$list[$l->id] = array('name' => $l->name);
				    }
				}
			}
			catch (Exception $e) {
			    echo $e->getMessage();
	        }
		
		return $list;
	}

	/**
	 * Get Response API
	 * 
	 */
	function getresponse_lists($api_key = '')
	{
		$blossomthemes_email_newsletter_settings = get_option( 'blossomthemes_email_newsletter_settings', true );
		$list = array();

		if( empty($api_key ) && isset( $blossomthemes_email_newsletter_settings['getresponse']['api-key'] ) && $blossomthemes_email_newsletter_settings['getresponse']['api-key'] !='' )
		{
			$api_key = $blossomthemes_email_newsletter_settings['getresponse']['api-key']; //Place API key here
		}

		if(!empty($api_key))
		{
			$getresponse = new GetResponse( $api_key );
			$account = $getresponse->accounts();

			if( isset( $account->accountId ) && '' != $account->accountId ){

				$campaigns = $getresponse->getCampaigns();

				if( !empty( $campaigns ) ){
					foreach ($campaigns as $k => $v) {
						$list[$v->campaignId]['name'] = $v->name;
					}
				} # END if( !empty( $campaigns ) )
				else{
					echo ('Connection Problem: Invalid API key');
				}

			}

		}
		return $list;
	}

	/**
	 * ActiveCampaign API
	 * 
	 */
	function activecampaign_lists($api_key = '', $url = '')
	{
		$blossomthemes_email_newsletter_settings = get_option( 'blossomthemes_email_newsletter_settings', true );
		$list = array();
		if( empty($api_key) && empty($url) && isset($blossomthemes_email_newsletter_settings['activecampaign']['api-url']) && $blossomthemes_email_newsletter_settings['activecampaign']['api-url']!='' && isset($blossomthemes_email_newsletter_settings['activecampaign']['api-key']) && $blossomthemes_email_newsletter_settings['activecampaign']['api-key']!='' )
		{	
			$api_key = $blossomthemes_email_newsletter_settings['activecampaign']['api-key']; //Place API key here
			$url = $blossomthemes_email_newsletter_settings['activecampaign']['api-url'];
		}

		if(!empty($api_key) && !empty($url)){

			$ac = new ActiveCampaign($url, $api_key);
			try 
			{
				$response = $ac->api( "list/list", array('ids' => 'all') );
				// print_r($response);
				if (is_object($response) && !empty($response) && $response->success==1) {
					foreach ($response as $v) {
						if(is_object($v)) {
							$list[$v->id] = array('name' => $v->name);
						}
					}
				}
				elseif( is_object($response) && !empty($response) && $response->success==0 ){
					echo $response->result_message;
				}
				else{
					echo $response;
				}

			} catch (Exception $e){
				echo $e->getMessage();
			}

		}			
		return $list;
	}

	function get_status($url)
	{
	    // must set $url first. 
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	    // do your curl thing here
	    $data = curl_exec($ch);
	    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    curl_close($ch);
	    return $http_status;
	}

	function blossomthemes_email_newsletter_settings_tabs() {

        $tabs = array(
            'general'           => 'general.php',
            'popup'            => 'popup.php',
            );
        $tabs = apply_filters( 'blossomthemes_email_newsletter_settings_tabs', $tabs );
        return $tabs;
    }
	
	function blossomthemes_email_newsletter_backend_settings()
	{ ?>
		<div class="wrap">
			<div class="btnb-header">
	            <h3><?php _e('Settings','blossomthemes-email-newsletter');?></h3>
	        </div>
	        <?php
            if( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ==true )
            { ?>
                <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
                <p><strong><?php _e('Settings updated.','blossomthemes-email-newsletter');?></strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e('Dismiss this notice.','blossomthemes-email-newsletter');?></span></button>
            	</div>
            <?php
            }
            ?>
            <div id="tabs-container">
			    <ul class="tabs-menu">
				<?php
				$settings_tab = $this->blossomthemes_email_newsletter_settings_tabs();
					$count = 0;
					foreach ($settings_tab as $key => $value) { 
						$tab_label = preg_replace('/_/', ' ', $key);
						?>
			        	<li <?php if($count==0){ ?>class="current"<?php } ?>><a href="<?php echo $key;?>"><?php echo $tab_label;?></a></li>
			        <?php $count++;
			        } ?>
			    </ul>
		    	<div class="tab">
					<form method="POST" name="form1" action="options.php" id="form1" class="btemn-settings-form">
						<?php
							settings_fields( 'blossomthemes_email_newsletter_settings' );
							do_settings_sections( __FILE__ );

							$counter = 0; 
					        foreach ($settings_tab as $key => $value) { ?>
					        <div id="<?php echo $key;?>" class="tab-content" <?php if($counter==0){ ?> style="display: block;" <?php } else{ ?> style="display: none;" <?php } ?>>
					           	<?php	
									include_once BLOSSOMTHEMES_EMAIL_NEWSLETTER_BASE_PATH . '/includes/tabs/'.$value;
					        	?>
					        </div>
					        <?php $counter++; }
						?>

						<div class="blossomthemes_email_newsletter_settings-settings-submit">
					        <?php echo submit_button();?>
					    </div>
					</form>
				</div>
			</div>	
		</div>
	<?php
	}

	function bten_platform_settings($platform='')
	{
		$blossomthemes_email_newsletter_settings = get_option( 'blossomthemes_email_newsletter_settings', true );
		switch ($platform) {
			case 'mailchimp':
				ob_start();				
				?>
				<div id="mailchimp" class="newsletter-settings<?php if( $platform == 'mailchimp' || !isset($platform) ){ echo ' current'; }?>">
					<div class="blossomthemes-email-newsletter-wrap-field">
						<label for="blossomthemes_email_newsletter_settings[mailchimp][api-key]"><?php _e('API Key : ','blossomthemes-email-newsletter');?></label> 
						<input type="text" id="bten_mailchimp_api_key" name="blossomthemes_email_newsletter_settings[mailchimp][api-key]" value="<?php echo isset($blossomthemes_email_newsletter_settings['mailchimp']['api-key']) ? esc_attr( $blossomthemes_email_newsletter_settings['mailchimp']['api-key'] ): '';?>">
						<?php echo '<div class="blossomthemes-email-newsletter-note">'.sprintf( __( 'Get your API key %1$shere%2$s', 'blossomthemes-email-newsletter' ), '<a href="https://us15.admin.mailchimp.com/account/api/" target="_blank">','</a>' ).'</div>';?>
					</div>
					<div class="blossomthemes-email-newsletter-wrap-field">
						<label for="blossomthemes_email_newsletter_settings[mailchimp][list-id]"><?php _e('List Id : ','blossomthemes-email-newsletter');?>
							<span class="blossomthemes-email-newsletter-tooltip" title="<?php esc_html_e( 'Choose the default list. If no groups/lists are selected in the newsletter posts, users will be subscribed to the list selected above.', 'blossomthemes-email-newsletter' ); ?>"><i class="far fa-question-circle"></i>
							</span>
						</label> 
							<?php
							$data = $this->mailchimp_lists();
					        ?>
					        <div class="select-holder">
						        <select id="bten_mailchimp_list" name="blossomthemes_email_newsletter_settings[mailchimp][list-id]">
						        	<?php $mailchimp_list = isset($blossomthemes_email_newsletter_settings['mailchimp']['list-id']) ? $blossomthemes_email_newsletter_settings['mailchimp']['list-id'] : '';
						        		  if( sizeof($data['lists']) < 1 ){ ?>
								  <option value="-"><?php _e('No Lists Found','blossomthemes-email-newsletter');?></option>
								  <?php } else{
									$max = max(array_keys($data['lists']));
							        for ($i=0; $i <= $max; $i++) { ?>
								  		<option <?php selected( $mailchimp_list, esc_attr($data['lists'][$i]['id'] ) );?> value="<?php echo esc_attr($data['lists'][$i]['id']);?>"><?php echo esc_attr($data['lists'][$i]['name']);?></option>
							        
								  <?php }} ?>
								</select>
							</div>
							<input type="button" rel-id="bten_mailchimp_list" class="button bten_get_mailchimp_lists" name="" value="Grab Lists">
							
					</div>
					<div class="blossomthemes-email-newsletter-wrap-field"> 
						<input type="checkbox" class="enable_notif_opt" name="blossomthemes_email_newsletter_settings[mailchimp][enable_notif]" <?php $j = isset( $blossomthemes_email_newsletter_settings['mailchimp']['enable_notif'] ) ? esc_attr( $blossomthemes_email_newsletter_settings['mailchimp']['enable_notif'] ): '0';?> id="blossomthemes_email_newsletter_settings[mailchimp][enable_notif]" value="<?php echo esc_attr($j);?>" <?php if($j=='1'){ echo "checked";}?>/>

						<label for="blossomthemes_email_newsletter_settings[mailchimp][enable_notif]"><?php _e('Confirmation','blossomthemes-email-newsletter');?>
							<span class="blossomthemes-email-newsletter-tooltip" title="<?php esc_html_e( 'Check this box if you want subscribers to receive confirmation mail before they are added to list.', 'blossomthemes-email-newsletter' ); ?>">
								<i class="far fa-question-circle"></i>
							</span>
						</label>
					</div>
				</div>
				<?php
				$output = ob_get_contents();
				ob_end_clean();
	      		return $output;
				break;

			case 'mailerlite':
				ob_start();
				?>
				<div id="mailerlite" class="newsletter-settings<?php if($platform == 'mailerlite'){ echo ' current'; }?>">
					<div class="blossomthemes-email-newsletter-wrap-field">
						<label for="blossomthemes_email_newsletter_settings[mailerlite][api-key]"><?php _e('API Key : ','blossomthemes-email-newsletter');?></label> 
						<input type="text" id="bten_mailerlite_api_key" name="blossomthemes_email_newsletter_settings[mailerlite][api-key]" value="<?php echo isset($blossomthemes_email_newsletter_settings['mailerlite']['api-key']) ? esc_attr( $blossomthemes_email_newsletter_settings['mailerlite']['api-key'] ): '';?>">
						<?php echo '<div class="blossomthemes-email-newsletter-note">'.sprintf( __( 'Get your api key %1$shere%2$s', 'blossomthemes-email-newsletter' ), '<a href="https://app.mailerlite.com/subscribe/api" target="_blank">','</a>' ).'</div>';?>  
					</div>
					<div class="blossomthemes-email-newsletter-wrap-field">
						<label for="blossomthemes_email_newsletter_settings[mailerlite][list-id]"><?php _e('List Id : ','blossomthemes-email-newsletter');?>
							<span class="blossomthemes-email-newsletter-tooltip" title="<?php esc_html_e( 'Choose the default list. If no groups/lists are selected in the newsletter posts, users will be subscribed to the list selected above.', 'blossomthemes-email-newsletter' ); ?>"><i class="far fa-question-circle"></i>
							</span>
						</label> 
						<?php
						$data = $this->mailerlite_lists();
				        ?>
				        <div class="select-holder">
					        <select id="bten_mailerlite_list" name="blossomthemes_email_newsletter_settings[mailerlite][list-id]">
					        	<?php $mailerlite_list = isset($blossomthemes_email_newsletter_settings['mailerlite']['list-id']) ? $blossomthemes_email_newsletter_settings['mailerlite']['list-id']: '';
					        	if( empty($data['Results']) ){ ?>
							  <option value="-"><?php _e('No Lists Found','blossomthemes-email-newsletter');?></option>
							  <?php } else{
								$max = max(array_keys($data['Results']));
						        for ($i=0; $i <= $max; $i++) { ?>
							  		<option <?php selected( $mailerlite_list, esc_attr($data['Results'][$i]['id'] ) );?> value="<?php echo esc_attr($data['Results'][$i]['id']);?>"><?php echo esc_attr($data['Results'][$i]['name']);?></option>
						        
							  <?php }} ?>
							</select>
						</div>
						<input type="button" rel-id="bten_mailerlite_list" class="button bten_get_mailerlite_lists" name="" value="Grab Lists">
					</div>
				</div>
				<?php
				$output = ob_get_contents();
				ob_end_clean();
	      		return $output;
				break;

			case 'convertkit':
				ob_start();
				?>
				<div id="convertkit" class="newsletter-settings<?php if($platform == 'convertkit'){ echo ' current'; }?>">
					<div class="blossomthemes-email-newsletter-wrap-field">
						<label for="blossomthemes_email_newsletter_settings[convertkit][api-key]"><?php _e('API Key : ','blossomthemes-email-newsletter');?></label> 
						<input type="text" id="bten_convertkit_api_key" name="blossomthemes_email_newsletter_settings[convertkit][api-key]" value="<?php echo isset($blossomthemes_email_newsletter_settings['convertkit']['api-key']) ? esc_attr( $blossomthemes_email_newsletter_settings['convertkit']['api-key'] ): '';?>">  
					</div>
					<div class="blossomthemes-email-newsletter-wrap-field">
						<label for="blossomthemes_email_newsletter_settings[convertkit][api-secret]"><?php _e('API Secret : ','blossomthemes-email-newsletter');?></label> 
						<input type="text" id="bten_convertkit_api_secret" name="blossomthemes_email_newsletter_settings[convertkit][api-secret]" value="<?php echo isset($blossomthemes_email_newsletter_settings['convertkit']['api-secret']) ? esc_attr( $blossomthemes_email_newsletter_settings['convertkit']['api-secret'] ): '';?>">  
					</div>
					<div class="blossomthemes-email-newsletter-wrap-field">
						<label for="blossomthemes_email_newsletter_settings[convertkit][list-id]"><?php _e('List Id : ','blossomthemes-email-newsletter');?>
							<span class="blossomthemes-email-newsletter-tooltip" title="<?php esc_html_e( 'Choose the default list. If no groups/lists are selected in the newsletter posts, users will be subscribed to the list selected above.', 'blossomthemes-email-newsletter' ); ?>"><i class="far fa-question-circle"></i>
							</span>
						</label> 
							<?php
							$data = $this->convertkit_lists();
					        ?>
					        <div class="select-holder">
							    <select id="bten_convertkit_list" name="blossomthemes_email_newsletter_settings[convertkit][list-id]">
						        	<?php $convertkit_list = isset( $blossomthemes_email_newsletter_settings['convertkit']['list-id'] ) ? $blossomthemes_email_newsletter_settings['convertkit']['list-id'] : '';
						        		  if( sizeof($data) < 1 ){ ?>
								  	<option value="-"><?php _e('No Lists Found','blossomthemes-email-newsletter');?></option>
								  	<?php } else{
							        foreach ($data as $key => $value) {
							 			?>
								  		<option <?php selected( $convertkit_list, esc_attr($key) );?> value="<?php echo esc_attr($key);?>"><?php echo esc_attr($value['name']);?></option>
								  	<?php }} ?>
								</select>
							</div>
						<input type="button" rel-id="bten_convertkit_list" class="button bten_get_convertkit_lists" name="" value="Grab Lists">
					</div>
				</div>
				<?php
				$output = ob_get_contents();
				ob_end_clean();
	      		return $output;
				break;

			case 'getresponse':
				ob_start();
				?>
				<div id="getresponse" class="newsletter-settings<?php if($platform == 'getresponse'){ echo ' current'; }?>">
					<div class="blossomthemes-email-newsletter-wrap-field">
						<label for="blossomthemes_email_newsletter_settings[getresponse][api-key]"><?php _e('API Key : ','blossomthemes-email-newsletter');?></label> 
						<input type="text" id="bten_getresponse_api_key" name="blossomthemes_email_newsletter_settings[getresponse][api-key]" value="<?php echo isset($blossomthemes_email_newsletter_settings['getresponse']['api-key']) ? esc_attr( $blossomthemes_email_newsletter_settings['getresponse']['api-key'] ): '';?>">  
					</div>
					<div class="blossomthemes-email-newsletter-wrap-field">
						<label for="blossomthemes_email_newsletter_settings[getresponse][list-id]"><?php _e('List Id : ','blossomthemes-email-newsletter');?>
							<span class="blossomthemes-email-newsletter-tooltip" title="<?php esc_html_e( 'Choose the default list. If no groups/lists are selected in the newsletter posts, users will be subscribed to the list selected above.', 'blossomthemes-email-newsletter' ); ?>"><i class="far fa-question-circle"></i>
							</span>
						</label> 
						<?php
						$data = $this->getresponse_lists();
						?>
					    <div class="select-holder">
					    	<select id="bten_getresponse_list" name="blossomthemes_email_newsletter_settings[getresponse][list-id]">
				        	<?php 
				        	$getresponse_list = isset($blossomthemes_email_newsletter_settings['getresponse']['list-id']) ? $blossomthemes_email_newsletter_settings['getresponse']['list-id'] : '';	
				        	if( sizeof($data) < 1 ){ ?>
						  	<option value="-"><?php _e('No Lists Found','blossomthemes-email-newsletter');?></option>
						  	<?php } else{ ?>
							<option value=""><?php _e('Choose Campaign ID','blossomthemes-email-newsletter');?></option>
							<?php
					        foreach ($data as $key => $value) {
					 			?>
						  		<option <?php selected( $getresponse_list, esc_attr($key) );?> value="<?php echo esc_attr($key);?>"><?php echo esc_attr($value['name']);?></option>
						  	<?php }} ?>
						</select></div>
						<input type="button" rel-id="bten_getresponse_list" class="button bten_get_getresponse_lists" name="" value="Grab Lists">
					</div>
				</div>
				<?php
				$output = ob_get_contents();
				ob_end_clean();
	      		return $output;
				break;

			case 'activecampaign':
				ob_start();
				?>
				<div id="activecampaign" class="newsletter-settings<?php if($platform == 'activecampaign'){ echo ' current'; }?>">
					<div class="blossomthemes-email-newsletter-wrap-field">
						<label for="blossomthemes_email_newsletter_settings[activecampaign][api-url]"><?php _e('API Url : ','blossomthemes-email-newsletter');?></label> 
						<input type="text" id="bten_activecampaign_api_url" name="blossomthemes_email_newsletter_settings[activecampaign][api-url]" value="<?php echo isset($blossomthemes_email_newsletter_settings['activecampaign']['api-url']) ? esc_attr( $blossomthemes_email_newsletter_settings['activecampaign']['api-url'] ): '';?>">  
					</div>
					<div class="blossomthemes-email-newsletter-wrap-field">
						<label for="blossomthemes_email_newsletter_settings[activecampaign][api-key]"><?php _e('API Key : ','blossomthemes-email-newsletter');?></label> 
						<input type="text" id="bten_activecampaign_api_key" name="blossomthemes_email_newsletter_settings[activecampaign][api-key]" value="<?php echo isset($blossomthemes_email_newsletter_settings['activecampaign']['api-key']) ? esc_attr( $blossomthemes_email_newsletter_settings['activecampaign']['api-key'] ): '';?>">  
					</div>
					<div class="blossomthemes-email-newsletter-wrap-field">				
						<label for="blossomthemes_email_newsletter_settings[activecampaign][list-id]"><?php _e('List Id : ','blossomthemes-email-newsletter');?>
							<span class="blossomthemes-email-newsletter-tooltip" title="<?php esc_html_e( 'Choose the default list. If no groups/lists are selected in the newsletter posts, users will be subscribed to the list selected above.', 'blossomthemes-email-newsletter' ); ?>"><i class="far fa-question-circle"></i>
							</span>
						</label> 
						<?php
						$data = $this->activecampaign_lists();
						// print_r($data);
						?>
						<div class="select-holder">
						    <select id="bten_activecampaign_list" name="blossomthemes_email_newsletter_settings[activecampaign][list-id]">
					        	<?php 
					        	$activecampaign_list = isset( $blossomthemes_email_newsletter_settings['activecampaign']['list-id'] ) ? $blossomthemes_email_newsletter_settings['activecampaign']['list-id'] : '';				        	
					        	if( sizeof($data) < 1 ){ ?>
							  	<option value="-"><?php _e('No Lists Found','blossomthemes-email-newsletter');?></option>
							  	<?php } else{ ?>
								<option value=""><?php _e('Choose Campaign ID','blossomthemes-email-newsletter');?></option>
								<?php
						        foreach ($data as $key => $value) {
						 			?>
							  		<option <?php selected( $activecampaign_list, esc_attr($key) );?> value="<?php echo esc_attr($key);?>"><?php echo esc_attr($value['name']);?></option>
							  	<?php }} ?>
							</select>
						</div>
						<input type="button" rel-id="bten_activecampaign_list" class="button bten_get_activecampaign_lists" name="" value="Grab Lists">
					</div>
				</div>
				<?php
				$output = ob_get_contents();
				ob_end_clean();
	      		return $output;
				break;

			case 'aweber':
				ob_start();
				?>
				<div id="aweber" class="newsletter-settings<?php if($platform == 'aweber'){ echo ' current'; }?>">

					<?php
					$blossomthemes_email_newsletter_settings = get_option( 'blossomthemes_email_newsletter_settings', true );
					$bten_aw_auth_info = get_option('bten_aw_auth_info');

					$appId = '9c213c43'; //Place APP ID here
					$url = "https://auth.aweber.com/1.0/oauth/authorize_app/".$appId;
					$status = $this->get_status($url);

					if($status!=200)
					{
						echo '<div class="blossomthemes-email-newsletter-note">' . __( 'The APP ID does not seem to exist. Please enter a valid AWeber APP ID to connect to the mailing lists.', 'blossomthemes-email-newsletter' ).'</div>';
					}
					else
					{
						echo '<div id="bten_aweber_connect_div"'.($bten_aw_auth_info ? 'style="display:none;"': '').'>';
						echo '<label>' .__('AWeber Connection : ','blossomthemes-email-newsletter'). '</label> ';
						echo '<b>Step 1:</b> <a href="https://auth.aweber.com/1.0/oauth/authorize_app/'.$appId.'" target="_blank">Click here to get your authorization code.</a><br />';
						echo '<b>Step 2:</b> Paste in your authorization code:<br />';
						echo '<textarea id="bten_aweber_auth_code" rows="3"></textarea><br />';
						echo '<input type="button" class="button-primary bten_aweber_auth" name="" value="Connect" />';
						echo '</div>';						

						echo '<div id="bten_aweber_disconnect_div"'.($bten_aw_auth_info ? '': ' style="display:none;"').'>';
						echo '<label>' .__('AWeber Connection : ','blossomthemes-email-newsletter'). '</label> ';
						echo '<input type="button" class="button-primary bten_aweber_remove_auth" name="" value="Remove Connection" />';
						echo '</div>';
						?>
						<div class="blossomthemes-email-newsletter-wrap-field">				
							<label for="blossomthemes_email_newsletter_settings[aweber][list-id]"><?php _e('List Id : ','blossomthemes-email-newsletter');?>
								<span class="blossomthemes-email-newsletter-tooltip" title="<?php esc_html_e( 'Choose the default list. If no groups/lists are selected in the newsletter posts, users will be subscribed to the list selected above.', 'blossomthemes-email-newsletter' ); ?>">
									<i class="far fa-question-circle"></i>
								</span>
							</label>
							<div class="select-holder"> 
							    <select id="bten_aweber_list" name="blossomthemes_email_newsletter_settings[aweber][list-id]">
						        	<?php 
						        	$aw = new Blossomthemes_Email_Newsletter_AWeber;
						        	$aweber_list = isset($blossomthemes_email_newsletter_settings['aweber']['list-id']) ? $blossomthemes_email_newsletter_settings['aweber']['list-id'] : '';        	
						        	$data = $aw->bten_get_aw_lists();
						        	if( sizeof($data) < 1 ){ ?>
								  	<option value="-"><?php _e('No Lists Found','blossomthemes-email-newsletter');?></option>
								  	<?php } else{ ?>
									<option value=""><?php _e('Choose Campaign ID','blossomthemes-email-newsletter');?></option>
									<?php
							        foreach ($data as $key => $value) {
							 			?>
								  		<option <?php selected( $aweber_list, esc_attr($key) );?> value="<?php echo esc_attr($key);?>"><?php echo esc_attr($value['name']);?></option>
								  	<?php }} ?>
								</select>
							</div>
							<input type="button" rel-id="bten_aweber_list" class="button bten_get_aweber_lists" name="" value="Grab Lists">
						</div>
					<?php
					}
					
				?>
				</div>
				<?php
				$output = ob_get_contents();
				ob_end_clean();
	      		return $output;
				break;

			case 'sendinblue' :
				ob_start();
					?>
						<div id="sendinblue-settings" class="newsletter-settings <?php echo 'sendinblue' === $platform ? esc_attr( 'current' ) : ''; ?>">
							<div class="blossomthemes-email-newsletter-wrap-field">
								<label for="blossomthemes_email_newsletter_settings[sendinblue][api-key]"><?php _e('API Key : ','blossomthemes-email-newsletter');?></label> 
								<input type="text" id="bten_sendinblue_api_key" name="blossomthemes_email_newsletter_settings[sendinblue][api-key]" value="<?php echo isset($blossomthemes_email_newsletter_settings['sendinblue']['api-key']) ? esc_attr( $blossomthemes_email_newsletter_settings['sendinblue']['api-key'] ): '';?>">
								<?php echo '<div class="blossomthemes-email-newsletter-note">'.sprintf( __( 'Get your API key %1$shere%2$s', 'blossomthemes-email-newsletter' ), '<a href="https://my.sendinblue.com/advanced/apikey/" target="_blank">','</a>' ).'</div>';?>
							</div>
							<div class="blossomthemes-email-newsletter-wrap-field">
								<label for="blossomthemes_email_newsletter_settings[sendinblue][list-id]"><?php _e('List Id : ','blossomthemes-email-newsletter');?>
									<span class="blossomthemes-email-newsletter-tooltip" title="<?php esc_html_e( 'Choose the default list. If no groups/lists are selected in the newsletter posts, users will be subscribed to the list selected above.', 'blossomthemes-email-newsletter' ); ?>">
									<i class="far fa-question-circle"></i>
									</span>
								</label> 
								<?php
									$data = $this->sendinblue_lists();
								?>
									<div class="select-holder">
										<select id="bten_sendinblue_list" name="blossomthemes_email_newsletter_settings[sendinblue][list-id]">
											<?php 
												$sendinblue_list = isset( $blossomthemes_email_newsletter_settings['sendinblue']['list-id'] ) ? $blossomthemes_email_newsletter_settings['sendinblue']['list-id'] : '' ;
												if ( empty( $data ) ) { ?>
													<option value="-">
														<?php _e('No Lists Found','blossomthemes-email-newsletter');?>
													</option>
											<?php 
												} else {
													echo '<option>'. esc_html( 'Choose sendinblue list' ) .'</option>';
													foreach ( $data as $listarray => $list ) { 
														echo '<option '. selected( $sendinblue_list, $list['id'] ) .' value="'. esc_attr( $list['id'] ) .'" >'. esc_html( $list['name'] ) .'</option>';
													}
												} 
											?>
										</select>
									</div>
									<input type="button" rel-id="bten_sendinblue_list" class="button bten_get_sendinblue_lists" name="" value="<?php esc_attr_e( 'Grab Lists', 'blossomthemes-email-newsletter' ); ?>">
							</div>
						</div>
					<?php
				$output = ob_get_clean();
				return $output;
			break;

			default:
				ob_start();
				?>
				<div id="mailchimp" class="newsletter-settings current">
					<div class="blossomthemes-email-newsletter-wrap-field">
						<label for="blossomthemes_email_newsletter_settings[mailchimp][api-key]"><?php _e('API Key : ','blossomthemes-email-newsletter');?></label> 
						<input type="text" id="bten_mailchimp_api_key" name="blossomthemes_email_newsletter_settings[mailchimp][api-key]" value="<?php echo isset($blossomthemes_email_newsletter_settings['mailchimp']['api-key']) ? esc_attr( $blossomthemes_email_newsletter_settings['mailchimp']['api-key'] ): '';?>">
						<?php echo '<div class="blossomthemes-email-newsletter-note">'.sprintf( __( 'Get your API key %1$shere%2$s', 'blossomthemes-email-newsletter' ), '<a href="https://us15.admin.mailchimp.com/account/api/" target="_blank">','</a>' ).'</div>';?>
					</div>
					<div class="blossomthemes-email-newsletter-wrap-field">
						<label for="blossomthemes_email_newsletter_settings[mailchimp][list-id]"><?php _e('List Id : ','blossomthemes-email-newsletter');?>
							<span class="blossomthemes-email-newsletter-tooltip" title="<?php esc_html_e( 'Choose the default list. If no groups/lists are selected in the newsletter posts, users will be subscribed to the list selected above.', 'blossomthemes-email-newsletter' ); ?>"><i class="far fa-question-circle"></i>
							</span>
						</label> 
							<?php
							$data = $this->mailchimp_lists();
					        ?>
					        <div class="select-holder">
						        <select id="bten_mailchimp_list" name="blossomthemes_email_newsletter_settings[mailchimp][list-id]">
						        	<?php $mailchimp_list = isset($blossomthemes_email_newsletter_settings['mailchimp']['list-id']) ? $blossomthemes_email_newsletter_settings['mailchimp']['list-id'] : '' ;
						        		  if( sizeof($data['lists']) < 1 ){ ?>
								  <option value="-"><?php _e('No Lists Found','blossomthemes-email-newsletter');?></option>
								  <?php } else{
									$max = max(array_keys($data['lists']));
							        for ($i=0; $i <= $max; $i++) { ?>
								  		<option <?php selected( $mailchimp_list, esc_attr($data['lists'][$i]['id'] ) );?> value="<?php echo esc_attr($data['lists'][$i]['id']);?>"><?php echo esc_attr($data['lists'][$i]['name']);?></option>
							        
								  <?php }} ?>
								</select>
							</div>
							<input type="button" rel-id="bten_mailchimp_list" class="button bten_get_mailchimp_lists" name="" value="Grab Lists">
					</div>
					<div class="blossomthemes-email-newsletter-wrap-field"> 
						<input type="checkbox" class="enable_notif_opt" name="blossomthemes_email_newsletter_settings[mailchimp][enable_notif]" <?php $j = isset( $blossomthemes_email_newsletter_settings['mailchimp']['enable_notif'] ) ? esc_attr( $blossomthemes_email_newsletter_settings['mailchimp']['enable_notif'] ): '0';?> id="blossomthemes_email_newsletter_settings[mailchimp][enable_notif]" value="<?php echo esc_attr($j);?>" <?php if($j=='1'){ echo "checked";}?>/>

						<label for="blossomthemes_email_newsletter_settings[mailchimp][enable_notif]"><?php _e('Confirmation','blossomthemes-email-newsletter');?>
							<span class="blossomthemes-email-newsletter-tooltip" title="<?php esc_html_e( 'Check this box if you want subscribers to receive confirmation mail before they are added to list.', 'blossomthemes-email-newsletter' ); ?>">
								<i class="far fa-question-circle"></i>
							</span>
						</label>
					</div>
				</div>
				<?php
				$output = ob_get_contents();
				ob_end_clean();
	      		return $output;
				break;
		}
	}
}
new BlossomThemes_Email_Newsletter_Settings;
