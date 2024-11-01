<?php

if ( defined('BIZPANDA_SIGNIN_LOCKER_ACTIVE') ) return;
define('BIZPANDA_SIGNIN_LOCKER_ACTIVE', true);

define('BIZPANDA_SIGNIN_LOCKER_DIR', dirname(__FILE__));
define('BIZPANDA_SIGNIN_LOCKER_URL', plugins_url( null, __FILE__ ));

if ( is_admin() ) require BIZPANDA_SIGNIN_LOCKER_DIR . '/admin/boot.php';

if ( !function_exists('opanda_register_signin_locker') ) {

    global $bizpanda;
    
    BizPanda::enableFeature('signin-locker');
            
    /**
     * Registers the Sign-In Locker item.
     * 
     * @since 1.0.0
     */
    function opanda_register_signin_locker( $items ) {
        
        $plugin = null;
        if ( BizPanda::hasPlugin('optinpanda') ) {
             global $optinpanda;
             $plugin = $optinpanda;
        } else {
             global $sociallocker;
             $plugin = $sociallocker;
        }
        
        global $optinpanda;
        
            $items['signin-locker'] = array(
                'name' => 'signin-locker',
                'type' => 'free',
                'title' => __('Sign-In Locker', 'signinlocker'),
                'help' => opanda_get_help_url('what-is-signin-locker'),
                'description' => __('<p>Locks the content until the user signs in through social networks.</p><p>You can set up various actions to be performed to sign in (e.g. subscribe, follow, share).</p>', 'signinlocker'),
                'shortcode' => 'signinlocker',
                'plugin' => $plugin
            );
        
        


        return $items;
    }
    add_filter('opanda_items', 'opanda_register_signin_locker', 2);
    
    /**
     * Adds options to print at the frontend.
     * 
     * @since 1.0.0
     */
    function opanda_signin_locker_options( $options, $id ) {
        
        $options['terms'] = opanda_terms_url();
        $options['privacyPolicy'] = opanda_privacy_policy_url(); 
        
        if ( !get_option('opanda_terms_use_pages', false) ) {
            $options['termsPopup'] = array(
                'width' => 570,
                'height' => 400
            );
        }
            $options['theme'] = 'great-attractor';
        


        $actions = explode( ',', opanda_get_item_option($id, 'connect_buttons') );
        $hasEmailForm = in_array( 'email', $actions );

        if ( $hasEmailForm ) {
            $emailFormIndex = array_search ('email', $actions);
            unset( $actions[$emailFormIndex] );  
        }

        $options['groups'] = $hasEmailForm
            ? array('connect-buttons', 'subscription')
            : array('connect-buttons');

        // connect buttons

        $useOwnApps = opanda_get_option('own_apps_to_signin', false);

        $localSocialProxy = [
            'url' => opanda_local_proxy_url(),
            'paramPrefix' => 'opanda'
        ];

        $remoteSocialProxy = [
            'endpoint' => opanda_remote_social_proxy_url(),
            'paramPrefix' => null
        ];

        $options['connectButtons'] = array();
        $options['connectButtons']['order'] = $actions;
        
        if ( in_array( 'facebook', $actions ) ) {
            
            $options['connectButtons']['facebook'] = array(
                'actions'=> opanda_signin_locker_get_actions( $id, 'facebook_actions' )
            );

            $clientId = opanda_get_option('facebook_app_id', false);
            $clientSecret = opanda_get_option('facebook_app_secret', false);

            if ( $useOwnApps && !empty( $clientId ) && !empty( $clientSecret ) ) {
                $options['connectButtons']['facebook']['socialProxy'] = $localSocialProxy;
            } else {
                $options['connectButtons']['facebook']['socialProxy'] = $remoteSocialProxy;
            }
        }

        if ( in_array( 'twitter', $actions ) ) {
            
            $options['connectButtons']['twitter'] = array(
                'actions' => opanda_signin_locker_get_actions( $id, 'twitter_actions' ),
                'follow' => array(
                    'user' => opanda_get_item_option($id, 'twitter_follow_user'),
                    'notifications' => opanda_get_item_option($id, 'twitter_follow_notifications'),
                ),
                'tweet'=> array(
                    'message' => opanda_get_item_option($id, 'twitter_tweet_message'),
                    'paramPrefix' => 'opanda'
                )
            );

            $clientId = opanda_get_option('twitter_signin_app_consumer_key', false);
            $clientSecret = opanda_get_option('twitter_signin_app_consumer_secret', false);

            if ( $useOwnApps && !empty( $clientId ) && !empty( $clientSecret ) ) {
                $options['connectButtons']['twitter']['socialProxy'] = $localSocialProxy;
            } else {
                $options['connectButtons']['twitter']['socialProxy'] = $remoteSocialProxy;
            }
        }
        
        if ( in_array( 'google', $actions ) ) {
            
            $options['connectButtons']['google'] = array(
                'actions' => opanda_signin_locker_get_actions( $id, 'google_actions' ),
                'clientId' => opanda_get_option('google_client_id'),

                'youtubeSubscribe' => array(
                    'channelId' => opanda_get_item_option($id, 'google_youtube_channel_id')
                )
            );

            $clientId = opanda_get_option('google_client_id', false);
            $clientSecret = opanda_get_option('google_client_secret', false);

            if ( $useOwnApps && !empty( $clientId ) && !empty( $clientSecret ) ) {
                $options['connectButtons']['google']['socialProxy'] = $localSocialProxy;
            } else {
                $options['connectButtons']['google']['socialProxy'] = $remoteSocialProxy;
            }
        }
        
        if ( in_array( 'linkedin', $actions ) ) {
            
            $linkedInActions = opanda_signin_locker_get_actions( $id, 'linkedin_actions' );
            if( ($key = array_search('follow', $linkedInActions)) !== false ) {
                unset($linkedInActions[$key]);
            }

            $options['connectButtons']['linkedin'] = array(
                'actions' => $linkedInActions,
                'clientId' => opanda_get_option('linkedin_client_id')
            );

            $clientId = opanda_get_option('linkedin_client_id', false);
            $clientSecret = opanda_get_option('linkedin_client_secret', false);

            if ( $useOwnApps && !empty( $clientId ) && !empty( $clientSecret ) ) {
                $options['connectButtons']['linkedin']['socialProxy'] = $localSocialProxy;
            } else {
                $options['connectButtons']['linkedin']['socialProxy'] = $remoteSocialProxy;
            }
        }

        // subscription options

        if ( $hasEmailForm ) { 

            $options['subscription'] = array();

            $res_signin_email_form_text = BizPanda::hasPlugin('optinpanda') ? opanda_get_item_option($id, 'subscribe_before_form', false) : null;
            if ( empty( $res_signin_email_form_text ) ) $res_signin_email_form_text = get_option('opanda_res_signin_email_form_text', '');

            $res_signin_email_button = BizPanda::hasPlugin('optinpanda') ? opanda_get_item_option($id, 'subscribe_button_text', false) : null;
            if ( empty( $res_signin_email_button ) ) $res_signin_email_button = get_option('opanda_res_signin_email_button', '');

            $res_signin_after_email_button = BizPanda::hasPlugin('optinpanda') ? opanda_get_item_option($id, 'subscribe_after_button', false) : null;
            if ( empty( $res_signin_after_email_button ) ) $res_signin_after_email_button = get_option('opanda_res_signin_after_email_button', '');

            $options['subscription']['text'] = $res_signin_email_form_text;
            $options['subscription']['form'] = array(
                'actions'       => opanda_signin_locker_get_actions( $id, 'email_actions' ),
                'buttonText'    => $res_signin_email_button,
                'noSpamText'    => $res_signin_after_email_button,
                'type'          => opanda_get_item_option($id, 'subscribe_name') ? 'name-email-form' : 'email-form'
            );
        }

        $optinMode = opanda_get_item_option($id, 'subscribe_mode');

        $service = opanda_get_option('subscription_service', 'database');
        $listId = ( 'database' === $service ) ? 'default' : opanda_get_item_option($id, 'subscribe_list', false);

        $options['subscribeActionOptions'] = array(
            'listId' => $listId,
            'service' => $service,
            'doubleOptin' => in_array( $optinMode, array('quick-double-optin', 'double-optin') ),
            'confirm' => in_array( $optinMode, array('double-optin') ),
        );
        
        return $options;
    }

    add_filter('opanda_signin-locker_item_options', 'opanda_signin_locker_options', 10, 2);
    
    /**
     * Adds the action 'lead' if the actions 'subscribe' and 'create account' are not selected.
     */
    function opanda_signin_locker_get_actions( $id, $optionName ) {

        $actions = explode( ',', opanda_get_item_option($id, $optionName ) );   
        if ( empty( $actions ) || empty($actions[0]) ) $actions = array();
        
        if ( !BizPanda::hasPlugin('optinpanda') ) {
            
            $temp = $actions; $actions = array();
            foreach( $temp as $actionName ) {
                if ( 'subscribe' === $actionName ) continue;
                $actions[] = $actionName;
            }
        }

        $catchLeads = opanda_get_item_option($id, 'catch_leads', false);
        
        if ( !$catchLeads ) return $actions;
        if ( in_array( 'signup', $actions) ) return $actions;
        if ( in_array( 'subscribe', $actions) ) return $actions;
        
        $actions[] = 'lead';
        return $actions;
    }
    
    /**
     * Requests assets for email locker.
     */
    function opanda_signin_locker_assets( $lockerId, $options, $fromBody, $fromHeader ) {
        OPanda_AssetsManager::requestLockerAssets( $lockerId );

        OPanda_AssetsManager::requestTheme( isset( $options['opanda_style'] ) ? $options['opanda_style'] : false );

        // The screen "Please Confirm Your Email"
        OPanda_AssetsManager::requestTextRes(array(
            'confirm_screen_title',
            'confirm_screen_instructiont',
            'confirm_screen_note1',
            'confirm_screen_note2',  
            'confirm_screen_cancel',
            'confirm_screen_open',
        ));

        // Miscellaneous
        OPanda_AssetsManager::requestTextRes(array(
            'misc_data_processing',
            'misc_or_enter_email',
            'misc_enter_your_email',
            'misc_enter_your_name',
            'misc_your_agree_with',
            'misc_agreement_checkbox',
            'misc_agreement_checkbox_alt',
            'misc_terms_of_use',
            'misc_privacy_policy',
            'misc_or_wait',
            'misc_close',
            'misc_or'
        ));

        // Errors & Notices
        OPanda_AssetsManager::requestTextRes(array(
            'errors_no_consent',
            'errors_empty_field',
            'errors_empty_checkbox',
            'errors_empty_email',
            'errors_inorrect_email',
            'errors_empty_name',
            'errors_subscription_canceled',
            'misc_close',
            'misc_or'
        ));

        // The screen "One Step To Complete" | Errors & Notices
        OPanda_AssetsManager::requestTextRes(array(
            'onestep_screen_title',
            'onestep_screen_instructiont',
            'onestep_screen_button',
            'errors_not_signed_in',
            'errors_not_granted'
        ));

        // Sign-In Buttons
        OPanda_AssetsManager::requestTextRes(array(
            'signin_long',
            'signin_short',
            'signin_facebook_name',
            'signin_twitter_name',
            'signin_google_name',
            'signin_linkedin_name'
        ));
    }
    
    add_action('opanda_request_assets_for_signin-locker', 'opanda_signin_locker_assets', 10, 4);

    /**
     * A shortcode for the Sign-In Locker
     * 
     * @since 1.0.0
     */
    class OPanda_SignInLockerShortcode extends OPanda_LockerShortcode {

        /**
         * Shortcode name
         * @var string
         */
        public $shortcodeName = array( 
            'signinlocker', 'signinlocker-1', 'signinlocker-2', 'signinlocker-3', 'signinlocker-4', 'signinlocker-bulk'
        );

        protected function getDefaultId() {
            return get_option('opanda_default_signin_locker_id');
        }
    }

    FactoryShortcodes320::register( 'OPanda_SignInLockerShortcode', $bizpanda );
}