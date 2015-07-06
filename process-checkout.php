<?php
function wpbc_unocoin_handle_post_payment()
{
    //handle notification
    if(isset($_REQUEST['wpbc_unocoin_callback']) && $_REQUEST['wpbc_unocoin_callback'] == "1")  //ipn handle for unocoin
    {
        include_once("lib/Unocoin_lib.php");
        $config = WP_UnocoinBitcoin_Config::getInstance();
        $api_key =  $config->getValue('wpbc_unocoin_api_key');
        $response = unocoinVerifyNotification($api_key);
        //verify transaction
        if (isset($response['error'])){
            //verification error
            return;
        }
        //transaction verified
        $post_id = $response['posData'];
        if(empty($post_id)){
            return;
        }
        $order_status = get_post_meta($post_id, 'wpbc_order_status', true );
        if($order_status=="confirmed"){ //order has been processed once already
            return; 
        }
        //update information
        if($order_status != "In Progress"){
            $updated_wpbc_order = array(
                    'ID'             => $post_id,
                    'post_status'    => 'publish',
                    'post_type'     => 'wpbc_bitcoin_orders',
                );
            wp_update_post($updated_wpbc_order);
        }
        $trn_status = $response['status'];
        $to_address = get_post_meta( $post_id, 'wpbc_email_address', true );
        $subject = "";
        $body = "";
        $admin_email = get_bloginfo('admin_email');
        $from_address = get_bloginfo('name')." <".$admin_email.">";
        $headers = 'From: '.$from_address . "\r\n";
        switch($trn_status)
        {
            //For low and medium transaction speeds, the order status is set to "Order Received". The customer receives
            //an initial email stating that the transaction has been paid.
            case 'paid':
                update_post_meta( $post_id, 'wpbc_order_status', $trn_status );            
                $subject = "Payment Received";
                $body = "Thank you! Your payment has been received, but the transaction has not been confirmed on the bitcoin network. " .
                                   "You will receive another email when the transaction has been confirmed.";
                wp_mail($to_address, $subject, $body, $headers);  
                update_post_meta( $post_id, 'wpbc_buyer_email_sent', 'Email sent to: '.$to_address." for order status: ".$trn_status);
                break;
            //For low and medium transaction speeds, the order status will not change. For high transaction speed, the order
            //status is set to "Order Received" here. For all speeds, an email will be sent stating that the transaction has
            //been confirmed.
            case 'confirmed':
            //display initial "thank you" if transaction speed is high, as the 'paid' status is skipped on high speed
                update_post_meta( $post_id, 'wpbc_order_status', $trn_status ); 
                if ($config->getValue('eStore_unocoin_transaction_speed') == "high") {
                    $subject = "Payment Received";
                    $body = "Thank you! Your payment has been received, and the transaction has been confirmed on the bitcoin network. " .
                                       "You will receive another email when the transaction is complete.";
                } 
                else {
                    $subject = "Transaction Confirmed";
                    $body = "Your transaction has now been confirmed on the bitcoin network. " .
                                       "You will receive another email when the transaction is complete.";
                }
                wp_mail($to_address, $subject, $body, $headers);
                update_post_meta( $post_id, 'wpbc_buyer_email_sent', 'Email sent to: '.$to_address." for order status: ".$trn_status);
                break;
            //The purchase receipt email is sent upon the invoice status changing to "complete", and the order
            //status is changed to Accepted Payment
            case 'complete':           
                update_post_meta( $post_id, 'wpbc_order_status', $trn_status );
                $subject = "Transaction Complete";
                    $body = "Thank you for your payment. You have ordered the following item(s):\n\n ";
                    $body .= get_post_meta($post_id, 'wpbc_items_ordered', true );        
                wp_mail($to_address, $subject, $body, $headers);
                update_post_meta( $post_id, 'wpbc_buyer_email_sent', 'Email sent to: '.$to_address." for order status: ".$trn_status);
                break;
        }
    }    
}

function wpbc_redirect_to_url($url)
{
    if (!headers_sent())
    {
            header('Location: ' . $url);
    }
    else
    {
            echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
    }
    exit;
}
