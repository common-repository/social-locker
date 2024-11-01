<?php

define('BIZPANDA_SOCIAL_LOCKER_DIR', dirname(__FILE__));
define('BIZPANDA_SOCIAL_LOCKER_URL', plugins_url( null, __FILE__ ));

if ( is_admin() ) require BIZPANDA_SOCIAL_LOCKER_DIR . '/admin/boot.php';
global $bizpanda;

/**
 * Registers the Email Locker item.
 * 
 * @since 1.0.0
 */
function opanda_register_social_locker( $items ) {
    global $sociallocker;
    
    $title =  __('Social Locker', 'sociallocker');
        
        $items['social-locker'] = array(
            'name' => 'social-locker',
            'type' => 'free',
            'title' => $title,
            'help' => opanda_get_help_url('sociallocker'),
            'description' => __('<p>Asks users to "pay with a like" or share to unlock content.</p><p>Perfect way to get more followers, attract social traffic and improve some social metrics.</p>', 'sociallocker'),
            'shortcode' => 'sociallocker',
            'plugin' => $sociallocker
        ); 
        
    


    return $items;
}
add_filter('opanda_items', 'opanda_register_social_locker', 1);

/**
 * Adds options to print at the frontend.
 * @param $options mixed[] Existing options that already added.
 * @param $id An ID of the locker.
 * @return mixed Updated options.
 */
function opanda_social_locker_options( $options, $id ) {
    global $post;

    $options['groups'] = array('social-buttons');
    $options['socialButtons'] = array();
        $buttonOrder = 'twitter-tweet,facebook-like';
    

     
    $actualUrls = opanda_get_option('actual_urls', false);

    $postUrl = !empty($post) ? get_permalink( $post->ID ) : null;
    $postUrl = $actualUrls ? null : $postUrl;

    $socialButtons = array(
        'display' => opanda_get_item_option($id, 'social_buttons_display', false, 'covers-native'),
        'coversSize' => opanda_get_item_option($id, 'social_buttons_size', false, 'default'),
        'counters' => opanda_get_item_option($id, 'social_buttons_counters', false, 1),
        'order' => opanda_get_item_option($id, 'buttons_order', false, $buttonOrder),

        'behaviorOnError' => get_option( 'opanda_adblock', 'show_error'),
        'behaviorError' => get_option(
            'opanda_adblock_error',
            __( 'Unable to create social buttons. Please make sure that nothing blocks loading of social scripts in your
             browser. Some browser extentions (Avast, PrivDog, AdBlock, Adguard etc.) or usage of private tabs in FireFox 
             may cause this issue. Turn them off and try again.', 'bizpanda' ))
    );

    $options['socialButtons'] = array_merge( $options['socialButtons'], $socialButtons );

    // removes buttons that are not allowed

    $allowedButtons = array('facebook-like', 'facebook-share', 'twitter-tweet', 'twitter-follow', 'youtube-subscribe', 'linkedin-share');
    $allowedButtons = apply_filters('opanda_social-locker_allowed_buttons', $allowedButtons);
    
    if ( $options['socialButtons']['order'] ) {
        $options['socialButtons']['order'] = explode( ',', $options['socialButtons']['order'] );
    }
    
    if ( empty( $options['socialButtons']['order'] ) ) {
        unset( $options['socialButtons']['order'] );
    } else {
        $filteredButtons = array();
        foreach( $options['socialButtons']['order'] as $buttonName ) {
            if ( !in_array( $buttonName, $allowedButtons ) ) continue;
            $filteredButtons[] = $buttonName;
        }
        $options['socialButtons']['order'] = $filteredButtons;
    }

    $buttons = $options['socialButtons']['order'];

    // proxy to use

    $useOwnApps = opanda_get_option('own_apps_for_permissions', false);

    $localSocialProxy = [
        'url' => opanda_local_proxy_url(),
        'paramPrefix' => 'opanda'
    ];

    $remoteSocialProxy = [
        'endpoint' => opanda_remote_social_proxy_url(),
        'paramPrefix' => null
    ];

    // Facebook

    if ( in_array( 'facebook-like', $buttons ) || in_array( 'facebook-share', $buttons ) ) {

        $defaultAppId = '117100935120196';
        $facebookAppId = opanda_get_option('facebook_app_id', $defaultAppId);

        $options['socialButtons']['facebook'] = array(
            'appId' => $facebookAppId,
            'lang' => opanda_get_option('lang', 'en_GB'),
            'version' => opanda_get_option('facebook_version', 'v7.0'),
            'like' => array(
                'url' => opanda_get_dynamic_url( $id, 'facebook_like_url', $postUrl),
                'title' => opanda_get_item_option($id, 'facebook_like_title' )
            )
        );

        if ( in_array( 'facebook-like', $buttons ) ) {

            $options['socialButtons']['facebook']['like'] = array(
                'url' => opanda_get_dynamic_url( $id, 'facebook_like_url', $postUrl),
                'title' => opanda_get_item_option($id, 'facebook_like_title' ),
                'socialProxy' => $localSocialProxy
            );
        }
    }

    // Twitter

    if ( in_array( 'twitter-tweet', $buttons ) || in_array( 'twitter-follow', $buttons )  ) {

        $options['socialButtons']['twitter'] = array(
            'lang' => opanda_get_option('short_lang', 'en')
        );

        $clientId = opanda_get_option('twitter_social_app_consumer_key', false);
        $clientSecret = opanda_get_option('twitter_social_app_consumer_secret', false);

        if ( in_array( 'twitter-tweet', $buttons ) ) {

            $tweetText = opanda_get_item_option($id, 'twitter_tweet_text' );

            $options['socialButtons']['twitter']['tweet'] = array(
                'url' => opanda_get_dynamic_url( $id, 'twitter_tweet_url', $postUrl),
                'text' => $tweetText,
                'skipCheck' => opanda_get_item_option($id, 'twitter_tweet_skip_auth'),
                'title' => opanda_get_item_option($id, 'twitter_tweet_title' ),
                'via' => opanda_get_item_option($id, 'twitter_tweet_via' )
            );

            if ( $useOwnApps && !empty( $clientId ) && !empty( $clientSecret ) ) {
                $options['socialButtons']['twitter']['tweet']['socialProxy'] = $localSocialProxy;
            } else {
                $options['socialButtons']['twitter']['tweet']['socialProxy'] = $remoteSocialProxy;
            }

            // replaces shortcodes in the locker message and twitter text

            if ( !empty( $tweetText ) ) {

                $postTitle = $post != null ? $post->post_title : '';
                $postUrl = $post != null ? get_permalink($post->ID) : '';

                $tweetText = str_replace('[post_title]', $postTitle, $tweetText );
                $options['socialButtons']['twitter']['tweet']['text'] = apply_filters('opanda_twitter_tweet_text', $tweetText, $id, $options);
            }
        }
    }

    // YouTube

    // LinkedIn

    // another languages

    // downgrades

        if ( 'blurring' === $options['overlap']['mode'] ) {
            $options['overlap']['mode'] = 'transparence';
        }

        if ( !in_array( $options['theme'] , array('starter', 'secrets')) ) {
            $options['theme'] = 'secrets';
        }
    


    return $options;
}

add_filter('opanda_social-locker_item_options', 'opanda_social_locker_options', 10, 2);

/**
 * Requests assets for email locker.
 */
function opanda_social_locker_assets( $lockerId, $options, $fromBody, $fromHeader ) {
    OPanda_AssetsManager::requestLockerAssets();

    OPanda_AssetsManager::requestTheme( isset( $options['opanda_style'] ) ? $options['opanda_style'] : false );

    // Confirm Like
    OPanda_AssetsManager::requestTextRes(array(
        'confirm_like_screen_header',
        'confirm_like_screen_message',
        'confirm_like_screen_button'
    ));
    
    // Miscellaneous
    OPanda_AssetsManager::requestTextRes(array(
        'misc_your_agree_with',
        'misc_agreement_checkbox',
        'misc_agreement_checkbox_alt',
        'misc_terms_of_use',
        'misc_privacy_policy',
        'misc_close',
        'misc_or_wait',
        'errors_not_signed_in',
        'errors_not_granted'
    ));
    
    // Errors & Notices
    OPanda_AssetsManager::requestTextRes(array(
        'errors_no_consent'
    ));

    if ( isset( $options['opanda_buttons_order'] ) && strpos( $options['opanda_buttons_order'], 'facebook-like' ) !== false ) {
        OPanda_AssetsManager::requestFacebookSDK();  
    }
}

add_action('opanda_request_assets_for_social-locker', 'opanda_social_locker_assets', 10, 4);

/**
 * A shortcode for the Social Locker
 * 
 * @since 1.0.0
 */
class OPanda_SocialLockerShortcode extends OPanda_LockerShortcode {
    
    /**
     * Shortcode name
     * @var string
     */
    public $shortcodeName = array( 
        'sociallocker', 'sociallocker-1', 'sociallocker-2', 'sociallocker-3', 'sociallocker-4'
    );
    
    protected function getDefaultId() {
        return get_option('opanda_default_social_locker_id');
    }
}

FactoryShortcodes320::register( 'OPanda_SocialLockerShortcode', $bizpanda );