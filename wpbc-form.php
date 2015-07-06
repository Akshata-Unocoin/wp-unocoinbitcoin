<?php
class wpbc_order_form{
    static function display_form_handler($atts)
    {
        echo '<link type="text/css" rel="stylesheet" href="'.WP_UNOCOINBITCOIN_PLUGIN_URL.'/style.php" />'."\n";
        $output .= wpbc_order_form::show_order_form($atts);
        return $output;
    }

    static function show_order_form($atts)
    {
        ob_start();
        wpbc_order_form::order_form_body_content($atts);
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    static function order_form_body_content($atts)
    {
        echo '<script type="text/javascript" src="'.WP_UNOCOINBITCOIN_PLUGIN_URL.'/jquery.validate.js"></script>';
        $validate_output = <<<EOT
             <script type="text/javascript">
             /* <![CDATA[ */
            jQuery.noConflict();
            jQuery(document).ready(function($){
                    $("#wp_unocoinbitcoin_order_form").validate();
                            $("input#copyaddress").click(function()
                            {
                                    if ($("input#copyaddress").is(':checked'))
                                    {
                                            // Checked, copy values
                                            $("input#shipping_fname").val($("input#fname").val());
                                            $("input#shipping_lname").val($("input#lname").val());
                                            $("input#shipping_address").val($("input#address").val());
                                            $("input#shipping_city").val($("input#city").val());
                                            $("input#shipping_state").val($("input#state").val());
                                            $("input#shipping_zip").val($("input#zip").val());
                                            var bcountry = $("select#country").val();
                            $('select#shipping_country option[value=' + bcountry + ']').attr('selected', 'selected');
                                            $("input#shipping_email").val($("input#email").val());
                                            $("input#shipping_phone").val($("input#phone").val());
                                    }
                                    else
                                    {
                                            // Clear on uncheck
                                            $("input#shipping_fname").val("");
                                            $("input#shipping_lname").val("");
                                            $("input#shipping_address").val("");
                                            $("input#shipping_city").val("");
                                            $("input#shipping_state").val("");
                                            $("input#shipping_zip").val("");
                                            $('select#shipping_country option[value=""]').attr('selected', 'selected');
                                            $("input#shipping_email").val("");
                                            $("input#shipping_phone").val("");
                                    }
                            });
            });
      /* ]]> */
      </script>
EOT;
        echo $validate_output;

        $show_cart = true;
        $show_billing_details = 1;
        $show_shipping_details = 0;
        $show_credit_card_details = 0;

        //Common stripping
        $_REQUEST["fname"] = strip_tags($_REQUEST["fname"]);
        $_REQUEST["lname"] = strip_tags($_REQUEST["lname"]);
        $_REQUEST["address"] = strip_tags($_REQUEST["address"]);
        $_REQUEST["city"] = strip_tags($_REQUEST["city"]);
        $_REQUEST["state"] = strip_tags($_REQUEST["state"]);
        $_REQUEST["zip"] = strip_tags($_REQUEST["zip"]);
        $_REQUEST["country"] = strip_tags($_REQUEST["country"]);
        $_REQUEST["email"] = strip_tags($_REQUEST["email"]);
        $_REQUEST["phone"] = strip_tags($_REQUEST["phone"]);
        ?>
    <div align="center" class="wrapper">

    <div class="wp_unocoinbitcoin_order_form_container">
    <form id="wp_unocoinbitcoin_order_form" name="wp_unocoinbitcoin_order_form" method="post" action="" enctype="multipart/form-data" onsubmit="" class="wp_unocoinbitcoin_order_form">

    <div id="wp_unocoinbitcoin_order_form_content">

        <?php
        if(!empty($_SESSION['estore_unocoin_form_submission_error'])){
            echo '<div class="estore_unocoin_form_submission_error">'.$_SESSION['estore_unocoin_form_submission_error'].'</div>';
        }
        $item_name = strip_tags($_REQUEST["item_name"]);
        $item_price = strip_tags($_REQUEST["price"]);
        $currency = strip_tags($_REQUEST["currency"]);
        if($show_cart)
        {
            echo "Item Name: ".$item_name."<br />";
            echo "Price: ".$item_price." ".$currency."<br />";
        }
        ?>
        <input type="hidden" name="item_name" value="<?php echo $item_name; ?>" />
        <input type="hidden" name="price" value="<?php echo $item_price; ?>" />
        <input type="hidden" name="currency" value="<?php echo $currency; ?>" />
        <?php
        $text = WP_unocoinBitcoin::get_text_message();
        $countries = wpbc_order_form::get_country_list();
        if($show_billing_details){
            wpbc_order_form::billing_block($countries, $text);
        }
        if($show_shipping_details){
            wpbc_order_form::shipping_block($countries, $text);
        }
        if($show_credit_card_details){
            wpbc_order_form::credit_card_block($text);
        }
        ?>
    <div class="estore_unocoin_clr"></div>
    <div class="submit-btn"><input src="<?php echo WP_UNOCOINBITCOIN_PLUGIN_URL; ?>/images/submit_button.png" type="image" name="submit" /></div>
    <input type="hidden" name="wpbc_user_info_submit" value="yes" />

    </div>
    </form>
    </div>
    </div>
    <?php
    }
    
    static function billing_block($countries, $text)
    {
    ?>
    <h2><?php echo $text['BILLING_INFORMATION'];?></h2>
    <div class="pane">
        <label><?php echo $text['BILLING_FIRST_NAME'];?></label>
        <input name="fname" id="fname" type="text" class="long-field required"  value="<?php echo $_REQUEST["fname"];?>" />
        <div class="wp_unocoinbitcoin_clr"></div>

        <label><?php echo $text['BILLING_LAST_NAME'];?></label>
        <input name="lname" id="lname" type="text" class="long-field required"  value="<?php echo $_REQUEST["lname"];?>" />
        <div class="wp_unocoinbitcoin_clr"></div>

        <label><?php echo $text['BILLING_ADDRESS'];?></label>
        <input name="address" id="address" type="text" class="long-field required"  value="<?php echo $_REQUEST["address"];?>" />
        <div class="wp_unocoinbitcoin_clr"></div>

        <label><?php echo $text['BILLING_CITY'];?></label>
        <input name="city" id="city" type="text" class="long-field required"  value="<?php echo $_REQUEST["city"];?>" />
        <div class="wp_unocoinbitcoin_clr"></div>

        <label><?php echo $text['BILLING_STATE_PROVINCE'];?></label>
        <input name="state" id="state" type="text" class="long-field required"  value="<?php echo $_REQUEST["state"];?>" />

        <div class="wp_unocoinbitcoin_clr"></div>
        <label><?php echo $text['BILLING_ZIP_POSTAL_CODE'];?></label>
        <input name="zip" id="zip" type="text" class="small-field required"  value="<?php echo $_REQUEST["zip"];?>" />
        <div class="wp_unocoinbitcoin_clr"></div>

        <label><?php echo $text['BILLING_COUNTRY'];?></label>
        <select name="country" id="country" class="long-field required" >
            <option value=""><?php echo $text['SELECT_BILLING_COUNTRY'];?></option>
            <?php foreach($countries as $code => $name){ ?>
            <option value="<?php echo $code;?>" <?php echo $_REQUEST["country"]=="$code"?"selected":""?>><?php echo $name;?></option>
            <?php } ?>
        </select>
        <div class="wp_unocoinbitcoin_clr"></div>

        <label><?php echo $text['BILLING_EMAIL'];?></label>
        <input name="email" id="email" type="text" class="long-field required"  value="<?php echo $_REQUEST["email"];?>" />
        <div class="wp_unocoinbitcoin_clr"></div>

        <label><?php echo $text['BILLING_PHONE'];?></label>
        <input name="phone" id="phone" type="text" class="long-field"  value="<?php echo $_REQUEST["phone"];?>" />
        <div class="wp_unocoinbitcoin_clr"></div>

        <input type="hidden" name="estore_unocoin_billing_details_submitted" value="" />

    </div>
    <?php
    }

    static function shipping_block($countries, $text)
    {
    ?>
    <h2><?php echo $text['SHIPPING_INFORMATION'];?></h2>
    <div class="pane">
        <label><?php echo $text['SAME_AS_BILLING_INFORMATION'];?></label>
        <input type="checkbox" id="copyaddress" />
        <div class="wp_unocoinbitcoin_clr"></div>

        <label><?php echo $text['SHIPPING_FIRST_NAME'];?></label>
        <input name="shipping_fname" id="shipping_fname" type="text" class="long-field required"  value="<?php echo $_REQUEST["shipping_fname"];?>" />
        <div class="wp_unocoinbitcoin_clr"></div>

        <label><?php echo $text['SHIPPING_LAST_NAME'];?></label>
        <input name="shipping_lname" id="shipping_lname" type="text" class="long-field required"  value="<?php echo $_REQUEST["shipping_lname"];?>" />
        <div class="wp_unocoinbitcoin_clr"></div>

        <label><?php echo $text['SHIPPING_ADDRESS'];?></label>
        <input name="shipping_address" id="shipping_address" type="text" class="long-field required"  value="<?php echo $_REQUEST["shipping_address"];?>" />
        <div class="wp_unocoinbitcoin_clr"></div>

        <label><?php echo $text['SHIPPING_CITY'];?></label>
        <input name="shipping_city" id="shipping_city" type="text" class="long-field required"  value="<?php echo $_REQUEST["shipping_city"];?>" />
        <div class="wp_unocoinbitcoin_clr"></div>

        <label><?php echo $text['SHIPPING_STATE_PROVINCE'];?></label>
        <input name="shipping_state" id="shipping_state" type="text" class="long-field required"  value="<?php echo $_REQUEST["shipping_state"];?>" />

        <div class="wp_unocoinbitcoin_clr"></div>
        <label><?php echo $text['SHIPPING_ZIP_POSTAL_CODE'];?></label>
        <input name="shipping_zip" id="shipping_zip" type="text" class="small-field required"  value="<?php echo $_REQUEST["shipping_zip"];?>" />
        <div class="wp_unocoinbitcoin_clr"></div>

        <label><?php echo $text['SHIPPING_COUNTRY'];?></label>
        <select name="shipping_country" id="shipping_country" class="long-field required" >
            <option value=""><?php echo $text['SELECT_SHIPPING_COUNTRY'];?></option>
            <?php foreach($countries as $code => $name){ ?>
            <option value="<?php echo $code;?>" <?php echo $_REQUEST["shipping_country"]=="$code"?"selected":""?>><?php echo $name;?></option>
            <?php } ?>
        </select>
        <div class="wp_unocoinbitcoin_clr"></div>

        <label><?php echo $text['SHIPPING_EMAIL'];?></label>
        <input name="shipping_email" id="shipping_email" type="text" class="long-field required"  value="<?php echo $_REQUEST["shipping_email"];?>" />
        <div class="wp_unocoinbitcoin_clr"></div>

        <label><?php echo $text['SHIPPING_PHONE'];?></label>
        <input name="shipping_phone" id="shipping_phone" type="text" class="long-field"  value="<?php echo $_REQUEST["shipping_phone"];?>" />
        <div class="wp_unocoinbitcoin_clr"></div>

        <input type="hidden" name="estore_unocoin_shipping_details_submitted" value="" />

    </div>
    <?php
    }

    static function credit_card_block($text)
    {
    $year = date("y");
    ?>
    <h2><?php echo $text['CREDIT_CARD_INFORMATION'];?></h2>
    <div class="pane">
    <label><?php echo $text['CREDIT_CARD_NUMBER'];?></label>
    <input name="cardnumber" id="ccn" type="text" class="long-field required" value="" maxlength="16" />
    <div class="wp_unocoinbitcoin_clr"></div>

    <label><?php echo $text['NAME_ON_CREDIT_CARD'];?></label>
    <input name="cardname" id="ccname" type="text" class="long-field required"   />
    <div class="wp_unocoinbitcoin_clr"></div>
    
    <label><?php echo $text['CREDIT_CARD_EXPIRATION_DATE'];?></label>
    <select name="cardexpirymonth" id="exp1" class="small-field required" >
        <option value="01">01</option>
        <option value="02">02</option>
        <option value="03">03</option>
        <option value="04">04</option>
        <option value="05">05</option>
        <option value="06">06</option>
        <option value="07">07</option>
        <option value="08">08</option>
        <option value="09">09</option>
        <option value="10">10</option>
        <option value="11">11</option>
        <option value="12">12</option>
    </select>
    <select name="cardexpiryyear" id="exp2" class="small-field required" >
        <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
        <option value="<?php echo $year+1; ?>"><?php echo $year+1; ?></option>
        <option value="<?php echo $year+2; ?>"><?php echo $year+2; ?></option>
        <option value="<?php echo $year+3; ?>"><?php echo $year+3; ?></option>
        <option value="<?php echo $year+4; ?>"><?php echo $year+4; ?></option>
        <option value="<?php echo $year+5; ?>"><?php echo $year+5; ?></option>
        <option value="<?php echo $year+6; ?>"><?php echo $year+6; ?></option>
        <option value="<?php echo $year+7; ?>"><?php echo $year+7; ?></option>
        <option value="<?php echo $year+8; ?>"><?php echo $year+8; ?></option>
        <option value="<?php echo $year+9; ?>"><?php echo $year+9; ?></option>
        <option value="<?php echo $year+10; ?>"><?php echo $year+10; ?></option>
    </select>
    <div class="wp_unocoinbitcoin_clr"></div>
    
    <label><?php echo $text['CREDIT_CARD_CVV'];?></label>
    <input name="cardcvv" id="cvv" type="text" maxlength="5" class="small-field required" />
    <span class="tooltip_green" data-text="<?php echo $text['CREDIT_CARD_CVV_HELP_TEXT']; ?>"></span>
    <input type="hidden" name="estore_unocoin_credit_card_details_submitted" value="" />
    
    </div>
    <?php
    }
    
    static function get_country_list()
    {
        $countries = array(
            
            'IN' => 'India',
            
        );

        return $countries;
    }
}

