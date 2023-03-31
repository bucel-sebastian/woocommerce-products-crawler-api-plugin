<?php

if( !defined('ABSPATH') ){
    exit;
}
function check_if_api_token_exists(){
    global $wpdb;

    $api_token_row = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."di_crawler_api` WHERE `name` = 'API_TOKEN'");

    if(sizeof($api_token_row)>0){
        return true;
    }

    return false;

}

if( !class_exists("Di_crawler_admin") ){

    class Di_crawler_admin {
        public function __construct() {
            add_action('admin_menu',array($this, 'di_crawler_admin_add_menu_page'));
        }

        public function di_crawler_admin_add_menu_page() {
            add_menu_page('di Products Crawler','di Products Crawler','manage_options','di-crawler-api',array($this,'di_crawler_admin_homepage'),'dashicons-star-filled',8);
            
        }

        public static function di_crawler_admin_homepage() {
            

            if(check_if_api_token_exists()){
                include_once DI_CRAWLER_API_DIR . '/includes/pages/di-crawler-admin-homepage.php';
            }
            else{
                include_once DI_CRAWLER_API_DIR . '/includes/pages/di-crawler-admin-api-locker.php';
            }
        }

        
        
    }

    new Di_crawler_admin();
}

?>