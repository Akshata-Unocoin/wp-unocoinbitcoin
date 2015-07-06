<?php
function wp_unocoinbitcoin_display_general_settings_menu()
{
    $config = WP_UnocoinBitcoin_Config::getInstance();
    if(isset($_POST['wp_unocoinbitcoin_settings_update'])){
        $config->setValue('wpbc_unocoin_enabled',($_POST["wpbc_unocoin_enabled"]=='1')?1:'');
        $config->setValue('wpbc_unocoin_api_key',trim($_POST["wpbc_unocoin_api_key"]));
        $config->setValue('wpbc_unocoin_transaction_speed',trim($_POST["wpbc_unocoin_transaction_speed"]));
        $config->setValue('wpbc_unocoin_form_page_url',trim($_POST["wpbc_unocoin_form_page_url"]));
        $config->setValue('wpbc_unocoin_form_page_title',trim($_POST["wpbc_unocoin_form_page_title"]));				
        echo '<div class="updated"><p>Settings updated!</p></div>'; 
        $config->saveConfig();
            
    }

?>

    <div class="postbox">
    <h3><label for="title">Usage Instruction</label></h3>
    <div class="inside">
    <p>You have successfully installed the WordPress Unocoin-Bitcoin plugin.</p>
    <p><strong>Step 1)</strong> Enter your unocoim credentials in the "Settings" section</p>
    <p><strong>Step 1)</strong> Insert a shortcode for your product to create a Buy Now button:</p>
    <p><code>[wpbc_buy_now item_name="test product" price="5.00" currency="INR"]</code></p>
    <p>Check the <a href="https://www.unocoin.com/merchants/overview" target="_blank">WP Unocoin-Bitcoin plugin page</a> for more information</p>
    </div></div>

    <div class="postbox">
    <h3><label for="title">unocoin Settings</label></h3>
    <div class="inside">

    <form method="post" action="">
    <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">

    <tr valign="top"><td width="25%" align="left">
    Enable unocoin
    </td><td align="left">
    <input name="wpbc_unocoin_enabled" type="checkbox"<?php if($config->getValue('wpbc_unocoin_enabled')!='') echo ' checked="checked"'; ?> value="1"/>
    <p class="description">Check this to enable unocoin gateway.</p>
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    API Key
    </td><td align="left">
    <input name="wpbc_unocoin_api_key" type="text" size="80" value="<?php echo $config->getValue('wpbc_unocoin_api_key'); ?>"/>
    <p class="description">Your unocoin merchant api key</p>
    </td></tr>
    
    <tr valign="top"><td width="25%" align="left">
    Transaction Speed
    </td><td align="left">
    <?php $txn_speed = $config->getValue('wpbc_unocoin_transaction_speed');?>
    <select name="wpbc_unocoin_transaction_speed" >
        <option <?php echo ($txn_speed==='low')?'selected="selected"':'';?> value="low">Low</option>
        <option <?php echo ($txn_speed==='medium')?'selected="selected"':'';?> value="medium">Medium</option>
        <option <?php echo ($txn_speed==='high')?'selected="selected"':'';?> value="high">High</option>
    </select>
        <br /><i>Speed at which the bitcoin transaction registers as "confirmed" to the store. This overrides your merchant settings on the unocoin website</i><br /><br />
    </td></tr>
    
    <tr valign="top"><td width="25%" align="left">
    Order Information Page
    </td><td align="left">
    <input name="wpbc_unocoin_form_page_url" type="text" size="80" value="<?php echo $config->getValue('wpbc_unocoin_form_page_url'); ?>"/>
    <br /><i>URL of the page where order information will be collected from your users.</i>
    </td></tr>
    
    <tr valign="top"><td width="25%" align="left">
    Order Information Page Title
    </td><td align="left">
    <input name="wpbc_unocoin_form_page_title" type="text" size="80" value="<?php echo $config->getValue('wpbc_unocoin_form_page_title'); ?>"/>
    <br /><i>Title of the page where order information will be collected from your users.</i>
    </td></tr>
    
    </table>
    
    <div class="submit">
        <input type="submit" name="wp_unocoinbitcoin_settings_update" class="button-primary" value="Update" />
    </div>
     
    </form>
    </div></div>

    <div style="background: none repeat scroll 0 0 #FFF6D5;border: 1px solid #D1B655;color: #3F2502;margin: 10px 0;padding: 5px 5px 5px 10px;text-shadow: 1px 1px #FFFFFF;">	
    <p>If you need a feature rich plugin to sell your digital and physical products using bitcoin then checkout our <a target="_blank" href="http://unocoin.com">WP eStore Plugin</a>
    </p>
    </div>
	
<?php
}
