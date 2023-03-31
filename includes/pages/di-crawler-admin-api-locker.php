<?php
    $error_output = '';
    if(isset($_POST['di-api-token'])){

        global $wpdb;

        $partnerURL = get_site_url();
        $apiToken = $_POST['di-api-token'];
        $response = wp_remote_get("https://retailromania.ro/wp-json/di-api/lista-furnizori");
        $response = wp_remote_retrieve_body($response);
        $response = json_decode($response);

        $is_verified = false;

        foreach ($response as $furnizor) {
            if($furnizor->url_furnizor === $partnerURL || $furnizor->url_furnizor === $partnerURL.'/'){
                if($furnizor->api_token === $apiToken){
                    $is_verified = true;
                }
            }
        }
        

        if($is_verified){
            $wpdb->insert($wpdb->prefix."di_crawler_api",array(
                "name"=>"API_TOKEN",
                "value"=>$apiToken
            ),array("%s","%s"));
            header("location: admin.php?page=di-crawler-api");
            
        }
        else{
            $error_output = '<span style="color:red;">Codul API introdus este incorect!</span>';
        }
    }

    


?>

<h1>Introdu codul API</h1>


<form action="admin.php?page=di-crawler-api" method="POST">
    <p>Va rugam sa introduceti codul API primit.</p>

    <div>
        <label for="di-api-token">
            Cod API
        </label>
        <input class="regulat-text" name="di-api-token" id="di-api-token">
        <?php echo $error_output; ?>
    </div>
    <br>
    <button id="di-api-token-submit" type="submit" class="button button-primary">Salveaza</button>
</form>