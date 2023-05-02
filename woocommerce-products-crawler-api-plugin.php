<?php

/**
 * Plugin Name: Api Retailromania
 * Plugin URI: 
 * Description: 
 * Version: 1.0
 * Author: di_agency
 * Author URI: https://diagency.eu/
 */

if ( !defined('ABSPATH')){
    exit;
}

if( !defined('DI_CRAWLER_API_DIR') ){
    define('DI_CRAWLER_API_DIR', plugin_dir_path(__FILE__));
}

if( !defined('DI_CRAWLER_API_URL') ){
    define('DI_CRAWLER_API_URL', plugin_dir_path(__FILE__));
}

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

if( !class_exists('Di_Crawler_Api') ){

    class Di_Crawler_Api {

        public function __construct() {
            $this->di_crawler_api_init();
        }

        public function di_crawler_api_init() {

            global $wpdb;

            $dbTableApi = $wpdb->prefix . 'di_crawler_api';
            $dbTableApiSql = 'CREATE TABLE `' . $dbTableApi . '` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `name` varchar(255) NOT NULL,
                        `value` varchar(255) NOT NULL,
                        PRIMARY KEY (`id`)
                )';
            maybe_create_table($dbTableApi,$dbTableApiSql);

            
            require_once DI_CRAWLER_API_DIR . '/includes/di_crawler_api_functions.php';
            if( is_admin() ){            
                require_once DI_CRAWLER_API_DIR . '/includes/di_crawler_api_admin.php';
            }

        }


    }
    new Di_Crawler_Api();
}






