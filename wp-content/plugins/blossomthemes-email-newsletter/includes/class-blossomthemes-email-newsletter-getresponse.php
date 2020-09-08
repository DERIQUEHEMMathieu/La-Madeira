<?php
/**
 * GetResponse handler of the plugin.
 *
 * @package    Blossomthemes_Email_Newsletter
 * @subpackage Blossomthemes_Email_Newsletter/includes
 * @author    blossomthemes
 */
class Blossomthemes_Email_Newsletter_GetResponse {

    function bten_getresponse_action( $email,$sid,$fname)
    {
        if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL) === false)
        {
            $blossomthemes_email_newsletter_settings = get_option( 'blossomthemes_email_newsletter_settings', true );
            
            $api_key = $blossomthemes_email_newsletter_settings['getresponse']['api-key']; //Place API key here
            $list = array();
            
            try{

                if( ! empty( $api_key ))
                {
                    $gs_api = new GetResponse( $api_key );
                    
                    // Test GS connection
                    $account = $gs_api->accounts();

                    if( isset( $account->accountId ) && '' !== $account->accountId ){
                        $listids = get_post_meta($sid,'blossomthemes_email_newsletter_setting',true);                  

                        if(!isset($listids['getresponse']['list-id']))
                        {
                            $listids = $blossomthemes_email_newsletter_settings['getresponse']['list-id'];
                            $contact_arr =  array (
                                'campaign'   => (object) array( 'campaignId' => $listids ),
                                'email'      => $email,
                                'dayOfCycle' => 0
                            );
                            if ( ! empty( $fname ) ) {
                                $contact_arr['name'] = $fname;
                            }
                            $result_contact = $gs_api->addContact( $contact_arr );
                            $list['response'] = '200' ;
                        }
                        else
                        {
                            foreach ($listids['getresponse']['list-id'] as $key => $value) {
                                $contact_array = array (
                                    'campaign'   => (object) array( 'campaignId' => $key ),
                                    'email'      => $email,
                                    'dayOfCycle' => 0
                                );
                                if ( ! empty( $fname ) ) {
                                    $contact_array['name'] = $fname;
                                }
                                $result_contact = $gs_api->addContact( $contact_array );
                            }
                            $list['response'] = '200' ;
                        }
                    }
                }
            }

            catch (Exception $e) {
                $list['log']['errorMessage'] = $e->getMessage();
            }      
        }
        return $list;
    }
}
new Blossomthemes_Email_Newsletter_GetResponse;