<?php

/*
Plugin Name: Wordpress Tags Cloud
Plugin URI: http://zamani.dev/wp/plugins/wp-tags-cloud
Description: Generate custom tags cloud
Version: 0.9.1
Author: Reza Zamani
Author URI: http://zamani.dev
*/

defined('ABSPATH') or die('Goodby');

class WpTagsCloud
{
    public $shorcodeArgs = array();

    public function __construct()
    {

    }

    public function initialize()
    {
        define ( 'MY_PLUGIN_VERSION', '0.9.1');
        define('WTC_PATH', plugin_dir_path( __FILE__ ));
        define('WTC_URL', plugin_dir_url(__FILE__));
        define('WP_URL', get_bloginfo('wpurl'));
        define('WTC_TG_NAME', 'wptagscloud');

        load_plugin_textdomain( WTC_TG_NAME, false, dirname( plugin_basename( __FILE__ ) ). '/languages/'  );
        include_once( WTC_PATH . 'includes/wtc-functions.php');

        $this->registerActions();

    }

    function registerSettingMenu()
    {
        add_options_page( __('WP Tags Cloud',WTC_TG_NAME), __('WP Tags Cloud',WTC_TG_NAME), 'manage_options', 'wtc-settings', array($this, 'toc_add_page'));
    }

    function toc_add_page(){
        //
    }

    function registerSettingLink($links)
    {
        $settingsLink = '<a href="'.WP_URL.'/wp-admin/options-general.php?page=wtc-settings">'.__('Plugin Details',WTC_TG_NAME).'</a>';
        array_unshift($links, $settingsLink);

        return $links;
    }

    public function registerAdminScripts()
    {
        wtcStyle( 'wtc-main', 'assets/css/wtc-main.css' );
    }

    public function registerClientScripts()
    {
        wtcScript('awesomeCloud','assets/js/jquery.awesomeCloud-0.2.js', array('jquery'));
    }

    public function registerShortCode($atts = array())
    {
        $args = shortcode_atts( array(
            'taxonomy' => 'post_tag',
            'limit' => 0,
            'hide_empty' => false,
            'orderby' => 'count',
            'link' => false,
            'class' => 'wordcloud',
            'rotation' => 0,
            'startcolor' => '#c33',
            'endcolor' => '#369',
            'shape' => 'circle',
            'height' => '70vh',
            'width'  => '100%'
        ), $atts );

        $terms = wtcTags($args);
        $this->shorcodeArgs = $args;

        add_action( 'wp_footer', array($this, 'registerClientScripts'));
        add_action('wp_footer', array($this, 'registerFooterScript'), 3);

        return wtcPrintTags($terms, $args['link'], $args['class']);
    }

    public function registerFooterScript()
    {
        echo wtcThemeScript(
                $this->shorcodeArgs['rotation'],
                $this->shorcodeArgs['startcolor'],
                $this->shorcodeArgs['endcolor'],
                $this->shorcodeArgs['shape'],
                $this->shorcodeArgs['height'],
                $this->shorcodeArgs['width'],
                WTC_URL
        );
    }

    public function registerActions()
    {
        add_action('admin_menu', array($this, 'registerSettingMenu'));
        add_filter('plugin_action_links', array($this, 'registerSettingLink'), 10, 2);
        add_action( 'admin_enqueue_scripts', array($this, 'registerAdminScripts'));

        add_shortcode('wptagscloud', array($this, 'registerShortCode'));
    }

}


function wpTagsCloud(){
    global $wtc;

    if(class_exists('WpTagsCloud')) {

        $wtc = new WpTagsCloud();
        $wtc->initialize();

    }

    return $wtc;
}
wpTagsCloud();

function sample_admin_notice__success() {
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php echo uniqid ('khjkg'); ?></p>
    </div>
    <?php
}
//add_action( 'admin_notices', 'sample_admin_notice__success' );
