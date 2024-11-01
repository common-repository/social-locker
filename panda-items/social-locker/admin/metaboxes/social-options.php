<?php
/**
 * The file contains a class to configure the metabox Social Options.
 * 
 * Created via the Factory Metaboxes.
 * 
 * @author Paul Kashtanoff <paul@byonepress.com>
 * @copyright (c) 2013, OnePress Ltd
 * 
 * @package core 
 * @since 1.0.0
 */

/**
 * The class configure the metabox Social Options.
 * 
 * @since 1.0.0
 */
class OPanda_SocialOptionsMetaBox extends FactoryMetaboxes321_FormMetabox
{
    /**
     * A visible title of the metabox.
     * 
     * Inherited from the class FactoryMetabox.
     * @link http://codex.wordpress.org/Function_Reference/add_meta_box
     * 
     * @since 1.0.0
     * @var string
     */
    public $title;    
   
    /**
     * A prefix that will be used for names of input fields in the form.
     * 
     * Inherited from the class FactoryFormMetabox.
     * 
     * @since 1.0.0
     * @var string
     */
    public $scope = 'opanda';
    
    /**
     * The priority within the context where the boxes should show ('high', 'core', 'default' or 'low').
     * 
     * @link http://codex.wordpress.org/Function_Reference/add_meta_box
     * Inherited from the class FactoryMetabox.
     * 
     * @since 1.0.0
     * @var string
     */
    public $priority = 'core';
	
    public $cssClass = 'factory-bootstrap-331 factory-fontawesome-320';
    
    public function __construct( $plugin ) {
        parent::__construct( $plugin );
        
       $this->title = __('Social Options', 'sociallocker');
    }
    
    /**
     * Configures a metabox.
     */
    public function configure( $scripts, $styles) {
         $styles->add( BIZPANDA_SOCIAL_LOCKER_URL . '/admin/assets/css/social-options.050600.css');
        $scripts->add( BIZPANDA_SOCIAL_LOCKER_URL . '/admin/assets/js/social-options.050600.js');
    }
    
    /**
     * Configures a form that will be inside the metabox.
     * 
     * @see FactoryMetaboxes321_FormMetabox
     * @since 1.0.0
     * 
     * @param FactoryForms328_Form $form A form object to configure.
     * @return void
     */ 
    public function form( $form ) {
        require_once OPANDA_BIZPANDA_DIR . '/admin/includes/plugins.php';
        $sociallockerUrl = OPanda_Plugins::getPremiumUrl('sociallocker');
            
        $tabs =  array(
            'type'      => 'tab',
            'align'     => 'vertical',
            'class'     => 'social-settings-tab',
            'items'     => array()
        );
            $facebookIsActiveByDefault = true;
            $twitterActiveByDefault = true;
            $googleIsActiveByDefault = false;
            $vkIsActiveByDefault = false; 
        


        // if the user has not updated the facebook app id, show a notice
        $facebookAppId = get_option('opanda_facebook_app_id', '117100935120196');
 
        // - Facebook Like Tab
        
        $tabs['items'][] = array(
            'type'      => 'tab-item',
            'name'      => 'facebook-like',
            'items'     => array(
                array(
                    'type'  => 'checkbox',
                    'way'   => 'buttons',
                    'title' => __('Available', 'sociallocker'),
                    'hint'  => __('Set On, to activate the button.', 'sociallocker'),
                    'name'  => 'facebook-like_available',
                    'default' => $facebookIsActiveByDefault
                ),        
                array(
                    'type'  => 'url',
                    'title' => __('Facebook Page', 'sociallocker'),
                    'hint'  => __('Set an URL of your facebook page which the user has to like in order to unlock your content.', 'sociallocker'),
                    'name'  => 'facebook_like_url'
                ),
                array(
                    'type'  => 'textbox',
                    'title' => __('Button Title', 'sociallocker'),
                    'hint'  => __('Optional. A title of the button that is situated on the covers in the themes "Secrets" and "Flat".', 'sociallocker'),
                    'name'  => 'facebook_like_title',
                    'default' => __('like', 'sociallocker')
                )
            )
        );
        
        
        
        // - Twitter Tweet Tab
        
        $tabs['items'][] = array(
            'type'      => 'tab-item',
            'title'     => '',
            'name'      => 'twitter-tweet',
            'items'     => array(

                array(
                    'type'  => 'checkbox',
                    'way'   => 'buttons',
                    'title' => __('Available', 'sociallocker'),
                    'hint'  => __('Set On, to activate the button.', 'sociallocker'),
                    'name'  => 'twitter-tweet_available',
                    'default' => $twitterActiveByDefault
                ),        
                array(
                    'type'  => 'url',
                    'title' => __('URL to tweet', 'sociallocker'),
                    'hint'  => __('Set an URL which the user has to tweet in order to unlock your content. Leave this field empty to use an URL of the page where the locker will be located.', 'sociallocker'),
                    'name'  => 'twitter_tweet_url'
                ),
                array(
                    'type'  => 'textarea',
                    'title' => __('Tweet', 'sociallocker'),
                    'hint'  => __('Type a message to tweet. Leave this field empty to use default tweet (page title + URL). Also you can use the shortcode [post_title] in order to insert automatically a post title into the tweet.', 'sociallocker'),
                    'name'  => 'twitter_tweet_text'
                ),
                array(
                    'type'  => 'checkbox',
                    'way'   => 'buttons',
                    'title' => __('Skip Check', 'sociallocker'),
                    'hint'  =>  __('Optional. Skip checking whether the user actually has tweeted or not. The locker will not ask for permissions to read tweets.', 'sociallocker'),
                    'name'  => 'twitter_tweet_skip_auth'
                ),
                array(
                    'type'  => 'textbox',
                    'title' => __('Via', 'sociallocker'),
                    'hint'  => __('Optional. Screen name of the user to attribute the Tweet to (without @).', 'sociallocker'),
                    'name'  => 'twitter_tweet_via'
                ),
                array(
                    'type'  => 'textbox',
                    'title' => __('Button Title', 'sociallocker'),
                    'hint'  => __('Optional. A title of the button that is situated on the covers in the themes "Secrets" and "Flat".', 'sociallocker'),
                    'name'  => 'twitter_tweet_title',
                    'default' => __('tweet', 'sociallocker')
                ),
                
            )
        );
            
           // - Facebook Share Tab

            $tabs['items'][] = array(
                'type'      => 'tab-item',
                'name'      => 'facebook-share',
                'items'     => array(
                    array(
                        'type'  => 'checkbox',
                        'way'   => 'buttons',
                        'title' => __('Available', 'sociallocker'),
                        'hint'  => __('Set On, to activate the button.', 'sociallocker'),
                        'name'  => 'facebook-share_available',
                        'default' => false
                    ),        
                    array(
                        'type'  => 'url',
                        'title' => __('URL to share', 'sociallocker'),
                        'hint'  => __('Set an URL which the user has to share in order to unlock your content. Leave this field empty to use an URL of the page where the locker will be located.', 'sociallocker'),
                        'name'  => 'facebook_fake_field_1'
                    ),
                    array(
                        'type'  => 'textbox',
                        'title' => __('Button Title', 'sociallocker'),
                        'hint'  => __('Optional. A title of the button that is situated on the covers in the themes "Secrets" and "Flat".', 'sociallocker'),
                        'name'  => 'facebook_fake_field_2',
                        'default' => __('share', 'sociallocker')
                    )
                )
            );
            
            // - Twitter Follow Tab

            $tabs['items'][] = array(
                'type'      => 'tab-item',
                'name'      => 'twitter-follow',
                'items'     => array(

                    array(
                        'type'  => 'checkbox',
                        'way'   => 'buttons',
                        'title' => __('Available', 'sociallocker'),
                        'hint'  => __('Set On, to activate the button.', 'sociallocker'),
                        'name'  => 'twitter-follow_available',
                        'default' => false
                    ),        
                    array(
                        'type'  => 'url',
                        'title' => __('User to follow', 'sociallocker'),
                        'hint'  => __('Set an URL of your Twitter profile (for example, <a href="https://twitter.com/byonepress" target="_blank">https://twitter.com/byonepress</a>).', 'sociallocker'),
                        'name'  => 'twiiter_fake_field_1'
                    ),
                    array(
                        'type'  => 'checkbox',
                        'way'   => 'buttons',
                        'title' => __('Hide Username', 'sociallocker'),
                        'hint'  => __('Set On to hide your username on the button (makes the button shorter).', 'sociallocker'),
                        'name'  => 'twiiter_fake_field_2'
                    ), 
                    array(
                        'type'  => 'textbox',
                        'title' => __('Button Title', 'sociallocker'),
                        'hint'  => __('Optional. A title of the button that is situated on the covers in the themes "Secrets" and "Flat".', 'sociallocker'),
                        'name'  => 'twiiter_fake_field_3',
                        'default' => __('follow us', 'sociallocker')
                    )
                )
            );
            
            // - Youtube Subscribe
 
            $tabs['items'][] = array(
                'type'      => 'tab-item',
                'name'      => 'youtube-subscribe',
                'items'     => array(
                    array(
                        'type'  => 'checkbox',
                        'way'   => 'buttons',
                        'title' => __('Available', 'sociallocker'),
                        'hint'  => __('Set On, to activate the button.', 'sociallocker'),
                        'name'  => 'youtube-subscribe_available',
                        'default' => false
                    ),
                    array(
                        'type'  => 'textbox',
                        'title' => __('Channel ID', 'sociallocker'),
                        'hint'  => __('Set a channel ID to subscribe (for example, <a href="http://www.youtube.com/channel/UCANLZYMidaCbLQFWXBC95Jg" target="_blank">UCANLZYMidaCbLQFWXBC95Jg</a>).', 'sociallocker'),
                        'name'  => 'youtube_fake_field_2'
                    ),                             
                    array(
                        'type'  => 'textbox',
                        'title' => __('Button Title', 'sociallocker'),
                        'hint'  => __('Optional. A visible title of the buttons that is used in some themes (by default only in the Secrets theme).', 'sociallocker'),
                        'name'  => 'youtube_fake_field_3',
                        'default' => __('subscribe', 'sociallocker')
                    )
               )
            );

            // - LinkedIn Share Tab

            $tabs['items'][] = array(
                'type'      => 'tab-item',
                'name'      => 'linkedin-share',
                'items'     => array(

                    array(
                        'type'  => 'checkbox',
                        'way'   => 'buttons',
                        'title' => __('Available', 'sociallocker'),
                        'hint'  => __('Set On, to activate the button.', 'sociallocker'),
                        'name'  => 'linkedin-share_available',
                        'default' => false
                    ),      
                    array(
                        'type'  => 'url',
                        'title' => __('URL to share', 'sociallocker'),
                        'hint'  => __('Set an URL which the user has to share in order to unlock your content. Leave this field empty to use an URL of the page where the locker will be located.', 'sociallocker'),
                        'name'  => 'linkedin_fake_field_1'
                    ),  
                    array(
                        'type'  => 'textbox',
                        'title' => __('Button Title', 'sociallocker'),
                        'hint'  => __('Optional. A title of the button that is situated on the covers in the themes "Secrets" and "Flat".', 'sociallocker'),
                        'name'  => 'linkedin_fake_field_2',
                        'default' => __('share', 'sociallocker')
                    )
                )
            );
            
            $allowed = array('facebook-like', 'twitter-tweet', 'google-plus');
            
            foreach( $tabs['items'] as $index => $tabItem ) {
                if ( in_array( $tabItem['name'], $allowed ) ) continue;
                $tabs['items'][$index]['items'][0]['value'] = false;
                $tabs['items'][$index]['items'][1]['value'] = false;
                $tabs['items'][$index]['cssClass'] = 'opanda-not-available';
                        
                $tabs['items'][$index]['items'][] = array(
                    'type'      => 'html',
                    'html'      => opanda_get_premium_note( true, 'social-buttons' )
                );
            }

        


        $tabs = apply_filters('onp_sl_social_options', $tabs);
        
        $defaultOrder = array();
        if ( $vkIsActiveByDefault ) $defaultOrder[] = 'vk-like';
        if ( $facebookIsActiveByDefault ) $defaultOrder[] = 'facebook-like';
        if ( $twitterActiveByDefault ) $defaultOrder[] = 'twitter-tweet';
        if ( $googleIsActiveByDefault ) $defaultOrder[] = 'google-plus';

        $form->add(array(
            array(
                'type'      => 'html',
                'html' => array( $this, 'showSocialButtonsStyleSelector' )
            ),
            array(
                'type'      => 'html',
                'html'      => '<div class="onp-sl-metabox-hint">
                                <strong>'.__('Hint', 'sociallocker').'</strong>: '. 
                                __('Drag and drop the tabs to change the order of the buttons.', 'sociallocker').
                                '</div>'
            ),
            array(
                'type'      => 'hidden',
                'name'      => 'buttons_order',
                'default'   => implode(',', $defaultOrder)
            ),
            array(
                'type'      => 'hidden',
                'name'      => 'social_buttons_display'
            ),
            array(
                'type'      => 'hidden',
                'name'      => 'social_buttons_size',
                'default'   => 'default'
            ),
            array(
                'type'      => 'hidden',
                'name'      => 'social_buttons_counters',
                'default'   => 1
            ),
            $tabs
        ));
    }

    public function showSocialButtonsStyleSelector() {

        $styles = OPanda_ThemeManager::getSocialButtonsDisplayModes();
        $sizes = OPanda_ThemeManager::getSocialButtonsSizes();

        $this->printPreviewVars();
        ?>
        <div id="opanda-button-styles-box" class="opanda-inline-form">

            <div class="opanda-inline-group opanda-social-buttons-display-wrap">
                <label for="opanda_social_buttons_display_select" class="control-label">
                    <i class="fa fa-bookmark-o" style="margin-right: 4px"></i>
                    <?php _e('Style of Buttons', 'opanda') ?>
                </label>
                <div class="control-group">

                    <select id="opanda_social_buttons_display_select" name="opanda_social_buttons_display_select" class="factory-dropdown factory-from-control-dropdown form-control" data-way="default">
                        <?php foreach ( $styles as $style ) { ?>
                            <option value="<?php echo $style['value'] ?>"><?php echo $style['title'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="opanda-inline-group opanda-social-buttons-size-wrap">
                <label for="opanda_social_buttons_size_select" class="control-label">
                    <i class="fa fa-arrows-v" style="margin-right: 3px"></i>
                    <?php _e('Size', 'opanda') ?>
                </label>
                <div class="control-group">

                    <select id="opanda_social_buttons_size_select" name="opanda_social_buttons_size_select" class="factory-dropdown factory-from-control-dropdown form-control" data-way="default">
                        <?php foreach ( $sizes as $size ) { ?>
                            <option value="<?php echo $size['value'] ?>"><?php echo $size['title'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="opanda-inline-group opanda-social-buttons-counters-wrap">
                <label for="opanda_social_buttons_counters_select" class="control-label">
                    <i class="fa fa-calculator" style="margin-right: 3px"></i>
                    <?php _e('Counters', 'opanda') ?>
                </label>
                <div class="control-group">

                    <select id="opanda_social_buttons_counters_select" name="opanda_social_buttons_counters_select" class="factory-dropdown factory-from-control-dropdown form-control" data-way="default">
                        <option value="1"><?php _e('Show Counters', 'opanda') ?></option>
                        <option value="0"><?php _e('Hide Counters', 'opanda') ?></option>
                    </select>
                </div>
            </div>

        </div>
        <?php
    }

    /**
     * Prints variables needed for preview.
     */
    public function printPreviewVars() {

        $useOwnApps = opanda_get_option('own_apps_for_permissions', false);

        $defaultAppId = '117100935120196';
        $facebookAppId = get_option('opanda_facebook_app_id', $defaultAppId);

        $socialProxyItems = [
            'facebook_like' => [
                'clientId' => true,
                'clientSecret' => true
            ],
            'facebook_share' => [
                'clientId' => $facebookAppId && $facebookAppId !== $defaultAppId,
                'clientSecret' => true
            ],
            'google' => [
                'clientId' => opanda_get_option('google_client_id', false),
                'clientSecret' => opanda_get_option('google_client_secret', false)
            ],
            'twitter' => [
                'clientId' => opanda_get_option('twitter_social_app_consumer_key', false),
                'clientSecret' => opanda_get_option('twitter_social_app_consumer_secret', false)
            ],
        ];

        $facebookAppId = get_option('opanda_facebook_app_id', '117100935120196');
        $facebookSocialProxyAppId = $useOwnApps && $facebookAppId && $facebookAppId !== $defaultAppId ? $facebookAppId : '';
        ?>
        <script>
            window.opanda_lang = '<?php echo get_option('opanda_lang', 'US_en') ?>';
            window.opanda_short_lang = '<?php echo get_option('opanda_short_lang', 'en') ?>';
            window.opanda_facebook_app_id = '<?php echo $facebookAppId ?>';
            window.opanda_facebook_version = '<?php echo get_option('opanda_facebook_version', 'v7.0') ?>';
            window.facebook_share_social_proxy_app_id = '<?php echo $facebookSocialProxyAppId ?>';

            <?php foreach( $socialProxyItems as $itemName => $itemValues ) { ?>
            window.<?php echo $itemName ?>_social_proxy = <?php if ( $useOwnApps && !empty($itemValues['clientId'] ) && !empty( $itemValues['clientSecret']) ) { ?>{
                'url': '<?php echo opanda_local_proxy_url() ?>',
                'paramPrefix': 'opanda'
            }<?php } else { ?>{
                'endpoint': '<?php echo opanda_remote_social_proxy_url() ?>',
                    'paramPrefix': null
            }<?php } ?>;
            <?php } ?>
        </script>
    <?php
    }
}

FactoryMetaboxes321::register('OPanda_SocialOptionsMetaBox', $bizpanda);
