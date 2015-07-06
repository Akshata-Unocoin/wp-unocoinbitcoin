<?php

add_filter('the_title', 'wp_unocoinbitcoin_filter_action_page_title');
function wp_unocoinbitcoin_filter_action_page_title($title)
{
    if (!in_the_loop()) {
            return $title;
    }
    global $post;
    if ($post->post_name == 'wpbc-order-info')
    {
        if(isset($_REQUEST['wpbc_co']) && $_REQUEST['gateway'] == 'unocoin')
        {
            if(isset($_REQUEST['result']) && $_REQUEST['result'] == 'error'){
                $title = 'Transaction Error';
                return $title; 
            }
            //This is a checkout request via WP UnocoinBitcoin plugin. Lets customize it.
            $config = WP_UnocoinBitcoin_Config::getInstance();
            $page_title = $config->getValue('wpbc_unocoin_form_page_title');
            if(!empty($page_title))
                $title = $page_title;
            else
                $title = 'Order Information';
        }
    }
    return $title;
}

add_filter('the_content', 'unocoin_estore_filter_store_action_page');
function unocoin_estore_filter_store_action_page($content)
{
    global $post;
    if ($post->post_name == 'wpbc-order-info')
    {
        if(isset($_REQUEST['wpbc_co']) && $_REQUEST['gateway'] == 'unocoin')
        {
            if(isset($_REQUEST['result']) && $_REQUEST['result'] == 'error' && isset($_REQUEST['error_type'])){
                $content = '<p>There was an error with the transaction processing. Please see below for details</p>';
                $errortype = strip_tags($_REQUEST['error_type']);
                $content .= 'ErrorType: '.$errortype;
                return $content; 
            }
            if(isset($_REQUEST['wpbc_unocoin_gateway']) && isset($_REQUEST['order_id']))   //thank you page redirection
            {
                $order_id = base64_decode(strip_tags($_REQUEST['order_id']));
                $content = '<p>Thank you for your payment. You should receive an email with your order summary</p>';
                return $content;
            }
            if(isset($_REQUEST['wpbc_user_info_submit']) && $_REQUEST['wpbc_user_info_submit']=="yes")
            {
                $config = WP_UnocoinBitcoin_Config::getInstance();
                $api_key =  $config->getValue('wpbc_unocoin_api_key');
                if(empty($api_key)){
                    echo "You need to enter your unocoin credentials in the plugin settings";
                    return;
                }
                $transaction_speed = $config->getValue('wpbc_unocoin_transaction_speed');
                if(!$transaction_speed){
                    $transaction_speed = "low";
                }
                
                $item_name = strip_tags($_REQUEST["item_name"]);
                $currency = strip_tags($_REQUEST["currency"]);
                $cart_id = "wpbc".uniqid();
                $amount = strip_tags($_REQUEST["price"]);
                $price = number_format($amount,2,'.','');
                
                // Create AccessCode Request Object
                $first_name = (!empty($_REQUEST["fname"]))?strip_tags(str_replace("'","`",$_REQUEST["fname"])):'';
                $last_name = (!empty($_REQUEST["lname"]))?strip_tags(str_replace("'","`",$_REQUEST["lname"])):'';
                $address = (!empty($_REQUEST["address"]))?strip_tags(str_replace("'","`",$_REQUEST["address"])):'';
                $city = (!empty($_REQUEST["city"]))?strip_tags(str_replace("'","`",$_REQUEST["city"])):'';
                $state = (!empty($_REQUEST["state"]))?strip_tags(str_replace("'","`",$_REQUEST["state"])):'';
                $zip = (!empty($_REQUEST["zip"]))?strip_tags(str_replace("'","`",$_REQUEST["zip"])):'';
                $country = (!empty($_REQUEST["country"]))?strip_tags(str_replace("'","`",$_REQUEST["country"])):'';
                $email = (!empty($_REQUEST["email"]))?strip_tags(str_replace("'","`",$_REQUEST["email"])):'';
                $phone = (!empty($_REQUEST["phone"]))?strip_tags(str_replace("'","`",$_REQUEST["phone"])):'';
                
                //------------------------
                $options = array();
                $options['buyerName'] = $first_name." ".$last_name;
                $options['buyerAddress1'] = $address;
                $options['buyerCity'] = $city;
                $options['buyerState'] = $state;
                $options['buyerZip'] = $zip;
                $options['buyerCountry'] = $country;
                $options['buyerEmail'] = $email;
                if($phone != ""){
                    $options['buyerPhone'] = $phone;
                }
                
                include_once("lib/unocoin_lib.php");
                $options['itemDesc'] = $item_name;
                $options['currency'] = $currency;
                $return_url = $config->getValue('wpbc_unocoin_form_page_url');
                $order_id = base64_encode($cart_id); 
                $notification_url = add_query_arg( 
                    array(
                        'wpbc_unocoin_callback' => '1',
                        ), 
                    $return_url 
                );
                $redirect_url = add_query_arg( 
                    array(
                        'wpbc_unocoin_gateway' => '1',
                        'order_id' => $order_id,
                        ), 
                    $return_url 
                );
                $options['notificationURL'] = $notification_url;
                //pass sessionid along so that it can be used to populate the transaction results page
                $options['redirectURL'] = $redirect_url;  
                $options['transactionSpeed'] = $transaction_speed;
                $options['apiKey'] = $api_key;
                //create a new order
                $wpbc_bitcoin_orders = array(
                'post_title'    => 'WPBC Bitcoin Order',
                'post_type'     => 'wpbc_bitcoin_orders',
                'post_content'  => '',
                'post_status'   => 'trash',
                );
                // Insert the post into the database
                $post_id  = wp_insert_post($wpbc_bitcoin_orders);
                if($post_id){
                    $updated_wpbc_order = array(
                        'ID'             => $post_id,
                        'post_title'    => $cart_id,
                        'post_type'     => 'wpbc_bitcoin_orders',
                    );
                    wp_update_post($updated_wpbc_order);
                    update_post_meta($post_id, 'wpbc_order_id', $cart_id);
                    update_post_meta($post_id, 'wpbc_first_name', $first_name);
                    update_post_meta($post_id, 'wpbc_last_name', $last_name);
                    update_post_meta($post_id, 'wpbc_email_address', $email);
                    update_post_meta($post_id, 'wpbc_total_amount', $price);
                    update_post_meta($post_id, 'wpbc_address', $address);
                    update_post_meta($post_id, 'wpbc_city', $city);
                    update_post_meta($post_id, 'wpbc_state', $state);
                    update_post_meta($post_id, 'wpbc_zip', $zip);
                    update_post_meta($post_id, 'wpbc_country', $country);
                    if($phone != ""){
                        update_post_meta($post_id, 'wpbc_phone', $phone);
                    }
                    $status = "In Progress";
                    update_post_meta($post_id, 'wpbc_order_status', $status);
                    $item_details = "Item Name: ".$item_name.", Price: ".$price." ".$currency;
                    update_post_meta( $post_id, 'wpbc_items_ordered', $item_details);
                }
                //
                $options['posData'] = $post_id;
                $options['fullNotifications'] = true;
                
                $invoice = unocoinCreateInvoice($cart_id, $price, $cart_id, $options);

                if(isset($invoice['error'])){
                    $error_url = add_query_arg( 
                    array(
                            'wpbc_co' => '1',
                            'gateway' => 'unocoin',
                            'result' => 'error',
                            'error_type' => $invoice["error"],
                        ), 
                        $error_url 
                    );
                    wpbc_redirect_to_url($error_url);
                }
                else{
                    //invoice created successfully
                    wpbc_redirect_to_url($invoice['url']);
                }
            }
            else
            {
                //This is a checkout request via Google Wallet for digital goods. Lets process it.
                $atts = array();
                $content = wpbc_order_form::display_form_handler($atts);
            }
        }
        
    }
    return $content;
}

function wpbc_bitcoin_create_order_page()
{
    register_post_type( 'wpbc_bitcoin_orders',
        array(
            'labels' => array(
                'name' => "Unocoin orders",
                'singular_name' => "Unocoin Order",
                'add_new' => "Add New",
                'add_new_item' => "Add New Order",
                'edit' => "Edit",
                'edit_item' => "Edit Order",
                'new_item' => "New Order",
                'view' => "View",
                'view_item' => "View Order",
                'search_items' => "Search Order",
                'not_found' => "No order found",
                'not_found_in_trash' => "No order found in Trash",
                'parent' => "Parent Order"
            ),

            'public' => true,
            'menu_position' => 80,
            'supports' => false,
            'taxonomies' => array( '' ),
            'has_archive' => true
            )
    );
}

function wpbc_bitcoin_add_meta_boxes()
{
    add_meta_box( 'order_review_meta_box',
        "Order Review",
        'wpbc_bitcoin_order_review_meta_box',
        'wpbc_bitcoin_orders', 
        'normal', 
        'high'
    );
}

function wpbc_bitcoin_order_review_meta_box($wpbc_bitcoin_orders)
{
    $order_id = get_post_meta( $wpbc_bitcoin_orders->ID, 'wpbc_order_id', true );
    $first_name = get_post_meta( $wpbc_bitcoin_orders->ID, 'wpbc_first_name', true );
    $last_name = get_post_meta( $wpbc_bitcoin_orders->ID, 'wpbc_last_name', true );
    $email = get_post_meta( $wpbc_bitcoin_orders->ID, 'wpbc_email_address', true );
    $total_amount = get_post_meta( $wpbc_bitcoin_orders->ID, 'wpbc_total_amount', true );
    $address = get_post_meta( $wpbc_bitcoin_orders->ID, 'wpbc_address', true );
    $city = get_post_meta( $wpbc_bitcoin_orders->ID, 'wpbc_city', true );
    $state = get_post_meta( $wpbc_bitcoin_orders->ID, 'wpbc_state', true );
    $zip = get_post_meta( $wpbc_bitcoin_orders->ID, 'wpbc_zip', true );
    $country = get_post_meta( $wpbc_bitcoin_orders->ID, 'wpbc_country', true );
    $phone = get_post_meta( $wpbc_bitcoin_orders->ID, 'wpbc_phone', true );
    $email_sent_value = get_post_meta( $wpbc_bitcoin_orders->ID, 'wpbc_buyer_email_sent', true );
    
    $email_sent_field_msg = "No";
    if(!empty($email_sent_value)){
        $email_sent_field_msg = "Yes. ".$email_sent_value;
    }
    
    $items_ordered = get_post_meta( $wpbc_bitcoin_orders->ID, 'wpbc_items_ordered', true );
    ?>
    <table>
        <p>Order ID: <?php echo $order_id; ?></p>
        <tr>
            <td>First Name</td>
            <td><input type="text" size="40" name="wpbc_first_name" value="<?php echo $first_name; ?>" /></td>
        </tr>
        <tr>
            <td>Last Name</td>
            <td><input type="text" size="40" name="wpbc_last_name" value="<?php echo $last_name; ?>" /></td>
        </tr>
        <tr>
            <td>Email Address</td>
            <td><input type="text" size="40" name="wpbc_email_address" value="<?php echo $email; ?>" /></td>
        </tr>
        <tr>
            <td>Total</td>
            <td><input type="text" size="20" name="wpbc_total_amount" value="<?php echo $total_amount; ?>" /></td>
        </tr>
        <tr>
            <td>Address</td>
            <td><input type="text" size="80" name="wpbc_address" value="<?php echo $address; ?>" /></td>
        </tr>
        <tr>
            <td>City</td>
            <td><input type="text" size="40" name="wpbc_city" value="<?php echo $city; ?>" /></td>
        </tr>
        <tr>
            <td>State/Province</td>
            <td><input type="text" size="40" name="wpbc_state" value="<?php echo $state; ?>" /></td>
        </tr>
        <tr>
            <td>ZIP/Postal Code</td>
            <td><input type="text" size="40" name="wpbc_zip" value="<?php echo $zip; ?>" /></td>
        </tr>
        <tr>
            <td>Country</td>
            <td><input type="text" size="40" name="wpbc_country" value="<?php echo $country; ?>" /></td>
        </tr>
        <tr>
            <td>Phone</td>
            <td><input type="text" size="40" name="wpbc_phone" value="<?php echo $phone; ?>" /></td>
        </tr>
        <tr>
            <td>Buyer Email Sent</td>
            <td><input type="text" size="80" name="wpbc_buyer_email_sent" value="<?php echo $email_sent_field_msg; ?>" readonly /></td>
        </tr>  
        <tr>
            <td>Item(s) Ordered</td>
            <td><textarea name="wpbc_items_ordered" cols="83" rows="5"><?php echo $items_ordered;?></textarea></td>
        </tr>

    </table>
    <?php
}

add_action( 'save_post', 'wpbc_bitcoin_save_orders', 10, 2 );

function wpbc_bitcoin_save_orders( $post_id, $wpbc_bitcoin_orders ) {
    // Check post type for movie reviews
    if ( $wpbc_bitcoin_orders->post_type == 'wpbc_bitcoin_orders' ) {
        // Store data in post meta table if present in post data
        if ( isset( $_POST['wpbc_first_name'] ) && $_POST['wpbc_first_name'] != '' ) {
            update_post_meta( $post_id, 'wpbc_first_name', $_POST['wpbc_first_name'] );
        }
        if ( isset( $_POST['wpbc_last_name'] ) && $_POST['wpbc_last_name'] != '' ) {
            update_post_meta( $post_id, 'wpbc_last_name', $_POST['wpbc_last_name'] );
        }
        if ( isset( $_POST['wpbc_email_address'] ) && $_POST['wpbc_email_address'] != '' ) {
            update_post_meta( $post_id, 'wpbc_email_address', $_POST['wpbc_email_address'] );
        }
        if ( isset( $_POST['wpbc_total_amount'] ) && $_POST['wpbc_total_amount'] != '' ) {
            update_post_meta( $post_id, 'wpbc_total_amount', $_POST['wpbc_total_amount'] );
        }
        if ( isset( $_POST['wpbc_address'] ) && $_POST['wpbc_address'] != '' ) {
            update_post_meta( $post_id, 'wpbc_address', $_POST['wpbc_address'] );
        }
        if ( isset( $_POST['wpbc_city'] ) && $_POST['wpbc_city'] != '' ) {
            update_post_meta( $post_id, 'wpbc_city', $_POST['wpbc_city'] );
        }
        if ( isset( $_POST['wpbc_state'] ) && $_POST['wpbc_state'] != '' ) {
            update_post_meta( $post_id, 'wpbc_state', $_POST['wpbc_state'] );
        }
        if ( isset( $_POST['wpbc_zip'] ) && $_POST['wpbc_zip'] != '' ) {
            update_post_meta( $post_id, 'wpbc_zip', $_POST['wpbc_zip'] );
        }
        if ( isset( $_POST['wpbc_country'] ) && $_POST['wpbc_country'] != '' ) {
            update_post_meta( $post_id, 'wpbc_country', $_POST['wpbc_country'] );
        }
        if ( isset( $_POST['wpbc_phone'] ) && $_POST['wpbc_phone'] != '' ) {
            update_post_meta( $post_id, 'wpbc_phone', $_POST['wpbc_phone'] );
        }
        if ( isset( $_POST['wpbc_items_ordered'] ) && $_POST['wpbc_items_ordered'] != '' ) {
            update_post_meta( $post_id, 'wpbc_items_ordered', $_POST['wpbc_items_ordered'] );
        }
    }
}

add_filter( 'manage_edit-wpbc_bitcoin_orders_columns', 'wpbc_bitcoin_orders_display_columns' );
function wpbc_bitcoin_orders_display_columns( $columns ) 
{
    //unset( $columns['title'] );
    unset( $columns['comments'] );
    unset( $columns['date'] );
    //$columns['wpsc_order_id'] = 'Order ID';
    $columns['title'] = "Order ID";
    $columns['wpbc_first_name'] = "First Name";
    $columns['wpbc_last_name'] = "Last Name";
    $columns['wpbc_email_address'] = "Email";
    $columns['wpbc_total_amount'] = "Total";
    $columns['wpbc_order_status'] = "Status";
    $columns['date'] = "Date";
    return $columns;
}

add_action('manage_wpbc_bitcoin_orders_posts_custom_column', 'wpbc_bitcoin_populate_order_columns', 10, 2);
function wpbc_bitcoin_populate_order_columns($column, $post_id)
{
    if ( 'wpbc_first_name' == $column ) {
        $first_name = get_post_meta( $post_id, 'wpbc_first_name', true );
        echo $first_name;
    }
    else if ( 'wpbc_last_name' == $column ) {
        $last_name = get_post_meta( $post_id, 'wpbc_last_name', true );
        echo $last_name;
    }
    else if ( 'wpbc_email_address' == $column ) {
        $email = get_post_meta( $post_id, 'wpbc_email_address', true );
        echo $email;
    }
    else if ( 'wpbc_total_amount' == $column ) {
        $total_amount = get_post_meta( $post_id, 'wpbc_total_amount', true );
        echo $total_amount;
    }
    else if ( 'wpbc_order_status' == $column ) {
        $status = get_post_meta( $post_id, 'wpbc_order_status', true );
        echo $status;
    }
}

function wpbc_bitcoin_customize_order_link( $permalink, $post ) {
    if( $post->post_type == 'wpbc_bitcoin_orders' ) { // assuming the post type is video
        $permalink = get_admin_url().'post.php?post='.$post->ID.'&action=edit';
    }
    return $permalink;
}
add_filter('post_type_link',"wpbc_bitcoin_customize_order_link",10,2);
