<?php
    global $wpdb;
    if(isset($_GET['diaction'])){
        if($_GET['diaction']==='changestatus'){
            $id = $_GET['id'];
            
            $di_orders = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."di_crawler_api` WHERE `name`='order' ORDER BY `id` DESC");
            foreach ($di_orders as $order) {
                $di_order_data = json_decode($order->value);
                if(strval($di_order_data->order_id) === $id){
                    if($di_order_data->order_status === "0"){
                        $di_order_data->order_status = "1";

                        $wpdb->update($wpdb->prefix . 'di_crawler_api',array(
                            'value'=>json_encode($di_order_data)
                        ),array(
                            'id'=>$order->id
                        ));

                        
                    }
                    $intern_order_redirect = wc_get_order($di_order_data->order_id);
                    $intern_order_redirect_url = $intern_order_redirect->get_edit_order_url();  
                    header("location: ".$intern_order_redirect_url);
                }
            }
        }
        if($_GET['diaction']==='nextstatus'){
            $id = $_GET['id'];
            
            $di_orders = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."di_crawler_api` WHERE `name`='order' ORDER BY `id` DESC");
            foreach ($di_orders as $order) {
                $di_order_data = json_decode($order->value);
                if(strval($di_order_data->order_id) === $id){
                    $order_status = intval($di_order_data->order_status);
                    if($order_status + 1 <= 3){
                        $order_status += 1;
                    }
                    $di_order_data->order_status = strval($order_status);

                    $wpdb->update($wpdb->prefix . 'di_crawler_api',array(
                        'value'=>json_encode($di_order_data)
                    ),array(
                        'id'=>$order->id
                    ));
                    header("location: https://royaldrop.eu/wp-admin/admin.php?page=di-crawler-api");
                    }
                }
            }
        
    }

?>

<style>

.di-order-status{
    padding: 7px 12px;
    color:#fff;
    border-radius:3px;
}

.status-new{
    background-color:#E74C3C;
}

.status-seen{
    background-color:#E67E22;
}

.status-ready{
    background-color:#3498DB;
}

.status-sent{
    background-color:#2ECC71;
}

</style>

<h1>Retail Romania Orders</h1>
<h2>Bine ai venit!</h2>
<p>Din aceasta pagina se pot vizualiza comenzile primite de la Retailromania.ro</p>

<table class="widefat fixed striped " cellspacing="0" >
        <thead>
            <tr>
                <th style='text-align:center;max-width:2.2em'>
                    Nr. Crt.
                </th>
                <th>
                    Data
</th>
                <th>
                    ID comanda
                </th>
                <th>
                    Numar comanda
                </th>
                <th>
                    Valoare 
                </th>
                <th>
                    AWB 
                </th>
                <th>
                    Status 
                </th>
                <th>
                    Actiuni
                </th>
            </tr>
        </thead>
        <tbody>
           <?php

                global $wpdb;

                $di_orders = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."di_crawler_api` WHERE `name`='order' ORDER BY `id` DESC");
                $index = 1;
                foreach ($di_orders as $order) {

                    $di_order_data = json_decode($order->value);

                    $intern_order = wc_get_order($di_order_data->order_id);
                    $intern_order_url = $intern_order->get_edit_order_url();
                    $intern_order_number = $intern_order->get_order_number();
                    $intern_order_total = $intern_order->get_total();
                    
                    if($di_order_data->order_status === "0"){   
                        $status = '
                            <span class="di-order-status status-new">
                                Comanda noua
                            </span>
                        ';
                    }
                    else if($di_order_data->order_status === "1"){
                        $status = '
                            <span class="di-order-status status-seen">
                                Comanda vizualizata
                            </span>
                        ';
                    }
                    else if($di_order_data->order_status === "2"){
                        $status = '
                            <span class="di-order-status status-ready">
                                Comanda pregatita
                            </span>
                        ';
                    }
                    else if($di_order_data->order_status === "3"){
                        $status = '
                            <span class="di-order-status status-sent">
                                Comanda trimisa
                            </span>
                        ';
                    }


                    ?>
                        <tr>
                            <td>
                                <?php
                                    echo $index;
                                ?>
                            </td>
                            <td>
                                <?php
                                    echo $intern_order->get_date_created();
                                ?>
                            </td>
                            <td>
                                <?php
                                    echo $di_order_data->order_id;
                                ?>
                            </td>
                            <td>
                                <?php
                                    echo "<a style='cursor:pointer;' class='order-view' href='admin.php?page=di-crawler-api&diaction=changestatus&id=".$di_order_data->order_id."'><strong>#".$intern_order_number." ".$intern_order->get_billing_first_name()." " . $intern_order->get_billing_last_name() .  "</strong></a>";
                                ?>
                            </td>
                            <td>
                                <?php
                                    echo $intern_order_total
                                ?>
                            </td>
                            <td>
                                <?php
                                    echo $di_order_data->awb;
                                ?>
                            </td>
                            <td>
                                <?php
                                    echo $status;
                                ?>
                            </td>
                            <td>
                                <a href='<?php echo $intern_order_url; ?>' class='button button-secondary' title='Vezi comanda'>
                                    <span class="dashicons dashicons-visibility"></span>
                                </a>
                                <?php
                                    if($di_order_data->order_status !== "3"){
                                        echo "<a href='admin.php?page=di-crawler-api&diaction=nextstatus&id=". $di_order_data->order_id."' class='button button-secondary' title='Modifica status'>
                                        <span class='dashicons dashicons-update'></span>
                                    </a>";
                                    }
                                
                                ?>
                                
                            </td>
                        </tr>
                    <?php
                }

            ?>
        </tbody>
    </table>

    <script>


    </script>
