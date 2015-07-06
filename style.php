<?php
    header("Content-type: text/css; charset: UTF-8");   
   $class_prefix = "wp_unocoinbitcoin";
?>
.<?php echo $class_prefix?>_clr { clear:both;}
.<?php echo $class_prefix?>_form_submission_error{text-align:left;padding:10px;}

/**** Form Content STYLING ****/
#<?php echo $class_prefix?>_order_form_content {width: 85%;border:1px solid #EEE; color:#000; background-color:#FFF;	}

#<?php echo $class_prefix?>_order_form_content h2 {background:#e4e4e4;margin:0;text-align:left;	height:30px;padding:10px 0 0 15px;font-size:18px;font-weight:bold;color:#000;border:1px solid #fff;	border-bottom:1px solid #ddd;text-shadow: 1px 1px 0 white;}

#<?php echo $class_prefix?>_order_form_content .pane {border:1px solid #fff; border-width:0 2px;  padding:15px;	 color:#000; font-size:12px; text-align:left;}
.ui-widget { max-width:400px;margin:20px;}
.ui-state-error{color: red;}
.ui-widget .ui-state-error, .ui-widget .ui-state-error p, .ui-widget .ui-state-highlight, .ui-widget .ui-state-highlight p { font-size:13px; text-align:left;}

/**** FORM Field STYLING ****/
.<?php echo $class_prefix?>_order_form label { float:left; width:30%; font-size:13px; font-weight:bold; text-align:right; padding:4px 5px 0 0;}
.<?php $class_prefix?>_order_form .long-field { float:left; border-radius: 3px; width:50%;padding-left:5px;border:1px solid #666666;
    background-color:#F8F8F8;margin-bottom:8px;}
.<?php echo $class_prefix?>_order_form .long-field:focus { border:1px solid #CCC;}
.<?php echo $class_prefix?>_order_form input[type=text]{/*height: 20px;*/}

.<?php echo $class_prefix?>_order_form .small-field { float:left; -webkit-border-radius: 3px;-khtml-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;width:15%;/*height:20px;*/padding-left:5px;/*border:1px solid #666666*/;
    background-color:#F8F8F8;margin-bottom:8px; margin-right:10px;}
.<?php echo $class_prefix?>_order_form .small-field:focus { border:1px solid #CCC;}

.<?php echo $class_prefix?>_order_form .lft-field { float:left; margin-right:5px; margin-bottom:8px;}
.<?php echo $class_prefix?>_order_form .submit-btn { margin:0 auto; width:119px; margin-top:15px;}
.<?php echo $class_prefix?>_order_form .submit-btn input[type=image]{border: none;}

.<?php echo $class_prefix?>_order_form label.error{color: red;width:300px !important;padding:0px !important;margin:0px 0px 5px 0px !important;text-align:right;}
