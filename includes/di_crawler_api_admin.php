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

            global $wpdb;

            $orders_list = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."di_crawler_api` WHERE `name`='order'");

            $unseen_orders = 0;
            $seen_orders = 0;
            $ready_orders = 0;
            foreach ($orders_list as $order) {
                $order_details = json_decode($order->value);
                if($order_details->order_status==="0"){
                    $unseen_orders++;
                }
                else if($order_details->order_status==="1"){
                    $seen_orders++;
                }
                else if($order_details->order_status==="2"){
                    $ready_orders++;
                }
            }

            add_menu_page(
                'Api Retailromania',
                'Api Retailromania <span class="awaiting-mod">' . $unseen_orders . '</span><span class="awaiting-mod" style="background:#E67E22;">' . $seen_orders . '</span><span class="awaiting-mod" style="background:#3498DB;">' . $ready_orders . '</span>',
                'manage_options',
                'di-crawler-api',
                array($this,'di_crawler_admin_homepage'),
                'dashicons-cart',
                8
            );
            
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