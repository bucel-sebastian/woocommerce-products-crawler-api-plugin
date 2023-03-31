<?php

?>


<h1>Bine ai venit!</h1>
<p>Din aceasta pagina se pot vizualiza comenzile primite de la di_agency</p>

<table class="widefat fixed striped " cellspacing="0" >
        <thead>
            <tr>
                <th style='text-align:center;max-width:2.2em'>
                    Nr. Crt.
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
                                    echo $di_order_data->order_id;
                                ?>
                            </td>
                            <td>
                                <?php
                                    echo "<a href='$intern_order_url' class='order-view'><strong>#".$intern_order_number." ".$intern_order->get_billing_first_name()." " . $intern_order->get_billing_last_name() .  "</strong></a>";
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

                            </td>
                        </tr>
                    <?php
                }

            ?>
        </tbody>
    </table>

