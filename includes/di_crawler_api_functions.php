<?php


$api_token='';
generate_data_feed();



function generate_data_feed(){

    global $wpdb;
    global $api_token;

    $api_token_row = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."di_crawler_api` WHERE `name` = 'API_TOKEN'");

    if(sizeof($api_token_row)>0){
        $api_token = $api_token_row[0]->value;
    }

    if($api_token != '' ) {

        add_action('rest_api_init','di_crawler_generate_api');

    }
    

}



function di_crawler_generate_api(){
    global $api_token;

    register_rest_route($api_token,'/status/',array(
        'methods' => 'GET',
        'callback' => 'di_crawler_api_status_ok',
        'permission_callback' => '__return_true'
    ));

    register_rest_route($api_token,'/feed/categories/',array(
        'methods' => 'GET',
        'callback' => 'di_crawler_categories_feed',
        'permission_callback' => '__return_true'
    ));
    
    register_rest_route($api_token,'/feed/products/',array(
        'methods' => 'GET',
        'callback' => 'di_crawler_all_products_feed',
        'permission_callback' => '__return_true'
    ));

    register_rest_route($api_token,'/di-crawler-new-order/',array(
        'methods' => 'POST',
        'callback' => 'di_crawler_new_order',
        'permission_callback' => '__return_true'
    ));

    register_rest_route($api_token,'/feed/orders/',array(
        'methods' => 'GET',
        'callback' => 'di_crawler_orders_feed',
        'permission_callback' => '__return_true'
    ));
}

function di_crawler_api_status_ok() {
    return array("status"=>"1");
}




function di_crawler_categories_feed() {

    $output = array();

    $taxonomy     = 'product_cat';
    $orderby      = 'parent';  
    $show_count   = 0;     
    $pad_counts   = 0;      
    $hierarchical = 1;        
    $title        = '';  
    $empty        = 0;

    $args = array(
        'taxonomy'     => $taxonomy,
         'orderby'      => $orderby,
         'show_count'   => $show_count,
         'pad_counts'   => $pad_counts,
         'hierarchical' => $hierarchical,
         'title_li'     => $title,
         'hide_empty'   => $empty,
         'parent'     => 0
    );

    $categories = get_categories( $args );
    foreach($categories as $cat) {
        array_push($output,array("name"=>$cat->name, "id"=>$cat->term_id));

        $args = array(
            'taxonomy'     => $taxonomy,
            'orderby'      => $orderby,
            'show_count'   => $show_count,
            'pad_counts'   => $pad_counts,
            'hierarchical' => $hierarchical,
            'title_li'     => $title,
            'hide_empty'   => $empty,
            'parent'       => $cat->term_id
        );
        $subcategories = get_categories( $args );
        foreach ($subcategories as $subcat) {
            array_push($output,array("name"=>'-' . $subcat->name, "id"=>$subcat->term_id));
            $args = array(
                'taxonomy'     => $taxonomy,
                'orderby'      => $orderby,
                'show_count'   => $show_count,
                'pad_counts'   => $pad_counts,
                'hierarchical' => $hierarchical,
                'title_li'     => $title,
                'hide_empty'   => $empty,
                'parent'     => $subcat->term_id
            );

            $subsubcategories = get_categories( $args );
            foreach ($subsubcategories as $subsubcat) {
                array_push($output,array("name"=>'-' . $subsubcat->name, "id"=>$subsubcat->term_id));
            }
        }
    }

    return $output;
}

function di_crawler_all_products_feed() {

    $wc_products_id = wc_get_products(array(
        'limit' => -1,
        'return' => 'ids'
    ));
    
    $products_data = array();

    foreach ($wc_products_id as $product_id ) {
        $product= wc_get_product($product_id);
        
        $product_status = $product -> get_status();
        if($product_status === "publish"){
            $id = $product -> get_id();
            $product_type = $product -> get_type();
            $product_name = $product -> get_name();
            $product_slug = $product -> get_slug();
            $product_date_created = $product -> get_date_created();
            $product_date_modified = $product -> get_date_modified();
            $product_description = $product -> get_description();
            $product_short_description = $product -> get_short_description();
            $product_sku = $product -> get_sku();
            $product_permalink = get_permalink( $id );

            $product_price = $product -> get_price();
            $product_regular_price = $product -> get_regular_price();
            $product_sale_price = $product -> get_sale_price();
            
            $product_manage_stock = $product -> get_manage_stock();
            $product_stock_quantity = $product -> get_stock_quantity();
            $product_stock_status = $product -> get_stock_status();

            $product_variations = $product -> get_children();
            $product_attributes = $product -> get_attributes();
            $product_default_attributes = $product -> get_default_attributes();

            $product_categories = $product -> get_categories();
            $product_category_ids = $product -> get_category_ids();
            $product_tag_ids = $product -> get_tag_ids();

            $product_image_id = $product -> get_image_id();
            $product_image = $product -> get_image();
            $product_gallery_image_ids = $product -> get_gallery_image_ids();

            $product_image_url = wp_get_attachment_image_url($product_image_id,'full');
            $product_gallery_image_urls = array();
            foreach ($product_gallery_image_ids as $gallery_image) {
                array_push($product_gallery_image_urls,wp_get_attachment_image_url($gallery_image,'full'));
            }

            array_push($products_data, array(

                "product_id" => $id,
                "product_status" => $product_status,
                "product_type" => $product_type,
                "product_name" => $product_name,
                "product_slug" => $product_slug,
                "product_date_created" => $product_date_created,
                "product_date_modified" => $product_date_modified,
                "product_description" => $product_description,
                "product_short_description" => $product_short_description,
                "product_sku" =>$product_sku,
                "product_permalink" => $product_permalink,
                "product_price" => $product_price,
                "product_regular_price" => $product_regular_price,
                "product_sale_price" => $product_sale_price,
                "product_manage_stock" => $product_manage_stock,
                "product_stock_quantity" => $product_stock_quantity,
                "product_stock_status" => $product_stock_status,
                "product_variations" => $product_variations,
                "product_attributes" => $product_attributes,
                "product_default_attributes" => $product_default_attributes,
                "product_categories" => $product_categories,
                "product_category_ids" => $product_category_ids,
                "product_tag_ids" => $product_tag_ids,
                "product_image_id" => $product_image_id,
                "product_image" => $product_image,
                "product_gallery_image_ids" => $product_gallery_image_ids,
                "product_image_url" => $product_image_url,
                "product_gallery_image_urls" => $product_gallery_image_urls

            ));
        }
    }

    return $products_data;
}

function di_crawler_orders_feed() {
    global $wpdb;

    $orders = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."di_crawler_api` WHERE `name`='order' ORDER BY `id` ASC");

    $feed_export = array();
    foreach ($orders as $order) {
        array_push($feed_export,array("id"=>$order->id,"value"=>json_decode($order->value)));
    }

    return $feed_export;
}

function di_crawler_new_order() {
    global $wpdb;

    $products = $_POST['products'];
    $new_order = wc_create_order();

    $wpdb->insert($wpdb->prefix . 'di_crawler_api',
    array(
        "name"=>"order-data",
        "value"=>json_encode($products)
    ),
    array(
        "%s","%s"
    ));

    foreach ($products as $product) {
        $product_data = $product['product_data'];
        $product_quantity = $product['quantity'];

        $new_order->add_product( wc_get_product($product_data['furnizor_product_id']), $product_quantity );
    }

    $order_data = $_POST['order_data'];


    $address = array(
        'first_name' => $order_data['shipping_first_name'],
        'last_name'  => $order_data['shipping_last_name'],
        'company'    => $order_data['shipping_company'],
        'email'      => '',
        'phone'      => '',
        'address_1'  => $order_data['shipping_address_1'],
        'address_2'  => $order_data['shipping_address_2'],
        'city'       => $order_data['shipping_city'],
        'state'      => $order_data['shipping_state'],
        'postcode'   => $order_data['shipping_postcode'],
        'country'    => $order_data['shipping_country']
    );

    $new_order->set_address( $address, 'billing' );

    $address = array(
        'first_name' => $order_data['shipping_first_name'],
        'last_name'  => $order_data['shipping_last_name'],
        'company'    => $order_data['shipping_company'],
        'email'      => '',
        'phone'      => '',
        'address_1'  => $order_data['shipping_address_1'],
        'address_2'  => $order_data['shipping_address_2'],
        'city'       => $order_data['shipping_city'],
        'state'      => $order_data['shipping_state'],
        'postcode'   => $order_data['shipping_postcode'],
        'country'    => $order_data['shipping_country']
    );
    $new_order->set_address( $address, 'shipping' );

    $new_order->calculate_totals();
    $new_order->save();

    $wpdb->insert($wpdb->prefix . 'di_crawler_api',
    array(
        "name"=>"order",
        "value"=>json_encode(array(
                "order_id"=>$new_order->get_id(),
                "order_status"=>"1",
                "awb"=>"",
                "order_date"=>date("Y-m-d H:i:s")
            ))
    ),
    array(
        "%s","%s"
    ));
    

    return array("status"=>"success","furnizor_order_id"=>$new_order->get_id());
}