<?php
/*
  Plugin Name: Authorize.net Payments
  Author: Meldin Xavier
  Author URI: https://mxtechweb.com
  Plugin URL: https://mxtechweb.com/authorizenet-simple-payment
  Description: Accept payments with Authorize.net. Easy to use.
  Version: 1.0
  License: GPLv2 or later
 */



add_shortcode('cre_payment', 'fn_cre_payment');

function fn_cre_payment() {
    $html = '';
    $auth_mode = get_option('cre_payment_mode');
    $auth_login_id = get_option('cre_payment_login_id');
    $auth_transction_id = get_option('cre_payment_transaction_key');
    if ($auth_mode == "live") {
        $post_url = "https://secure.authorize.net/gateway/transact.dll";
    } else {
        $post_url = "https://test.authorize.net/gateway/transact.dll";
    }
    if(isset($_POST['submit_this_payment'])){
        echo '<h3>Submitting details, please wait....</h3>';
    include_once 'anet_php_sdk/AuthorizeNet.php';
        $amount = $_POST['x_amount'];
	$fp_timestamp = time();
	$fp_sequence = "LAM-" . time(); 
	$fingerprint = AuthorizeNetSIM_Form::getFingerprint($auth_login_id,  $auth_transction_id,$amount, $fp_sequence, $fp_timestamp);
         $html .= '<form method="post" action="' . $post_url . '" name="paymentform">
        <input type="hidden" name="x_login" value="'.$auth_login_id.'" />
	<input type="hidden" name="x_fp_hash" value="'.$fingerprint.'" />
	<input type="hidden" name="x_fp_timestamp" value="'.$fp_timestamp.'" />
	<input type="hidden" name="x_fp_sequence" value="'.$fp_sequence.'" />
	<input type="hidden" name="x_version" value="3.1">
	<input type="hidden" name="x_show_form" value="payment_form">
	<input type="hidden" name="x_test_request" value="false" />
	<input type="hidden" name="x_method" value="cc">
        <input type="hidden" name="x_Relay_Response" value="false">
        <input type="hidden" name="x_description" value="Payment in Lamarcosystems time - '.date('M-d-Y H:i').'">
        <input type="hidden" name="x_first_name" value="'.$_POST['x_first_name'].'">
        <input type="hidden" name="x_last_name" value="'.$_POST['x_last_name'].'">
        <input type="hidden" name="x_company" value="'.$_POST['x_company'].'">
        <input type="hidden" name="x_country" value="'.$_POST['x_country'].'">
        <input type="hidden" name="x_address" value="'.$_POST['x_address'].'">
        <input type="hidden" name="x_city" value="'.$_POST['x_city'].'">
        <input type="hidden" name="x_state" value="'.$_POST['x_state'].'">
        <input type="hidden" name="x_zip" value="'.$_POST['x_zip'].'">
        <input type="hidden" name="x_phone" value="'.$_POST['x_phone'].'">
        <input type="hidden" name="x_fax" value="'.$_POST['x_fax'].'">
        <input type="hidden" name="x_email" value="'.$_POST['x_email'].'"> 
        <input type="hidden" name="x_invoice_num" value="'.$_POST['x_invoice_num'].'"> 
        <input type="hidden" name="x_amount" value="'.$_POST['x_amount'].'" >
        <input type="submit" value="Confirm Payment">
</form><script>document.paymentform.submit();</script>';
    }else{
    $html .= '<form method="post" action="">
				<p>
                                <label style="width:50%">First Name:</label> 
                                <input type="text" required="true"  style="width:50%" name="x_first_name" id="x_first_name">
                                </p>
				<p>
                                <label style="width:50%">Last Name:</label> 
                                <input type="text" required="true" style="width:50%" name="x_last_name" id="x_last_name">
                                </p>
                                <p>
                                <label style="width:50%">Company Name:</label> 
                                <input type="text" required="true" style="width:50%" name="x_company" id="x_company">
                                </p>
                                <p>
                                <label style="width:50%">Country:</label> 
                                <input type="text" required="true" style="width:50%" name="x_country" id="x_country">
                                </p>
                                <p>
                                <label style="width:50%">Address:</label> 
                                <textarea required="true" style="width:50%" name="x_address" id="x_address"></textarea>
                                </p>
                                <p>
                                <label style="width:50%">City:</label> 
                                <input type="text" required="true" style="width:50%" name="x_city" id="x_city">
                                </p>
                                <p>
                                <label style="width:50%">State/Province:</label> 
                                <input type="text" required="true" style="width:50%" name="x_state" id="x_state">
                                </p>
                                <p>
                                <label style="width:50%">Zip/Postal Code:</label> 
                                <input type="number" required="true" style="width:50%" name="x_zip" id="x_zip">
                                </p>
                                <p>
                                <label style="width:50%">Phone Number:</label> 
                                <input type="text" required="true" style="width:50%" name="x_phone" id="x_phone">
                                </p>
                                <p>
                                <label style="width:50%">Fax Number:</label> 
                                <input type="text" required="true" style="width:50%" name="x_fax" id="x_fax">
                                </p>
				<p>
                                <label style="width:50%">E-mail Address:</label>
                                <input type="email" style="width:50%" name="x_email" id="x_email" required="true"> 
                                </p>
                                <p>
                                <label style="width:50%">Invoice/Proposal Number:</label>
                                <input type="text" style="width:50%" name="x_invoice_num" id="x_invoice_num" required="true"> 
                                </p>
				<p>
                                <label style="width:50%">Amount (USD):</label>
                                <input type="number" style="width:50%" name="x_amount" id="x_amount" required="true">
                                </p>
				<p>
                                <input type="submit" name="submit_this_payment" value="Pay Now">
                                </p>
			</form>';

    }
    return $html;
}

add_action('admin_menu', 'cre_create_menu');

function cre_create_menu() {

    add_menu_page('Payment Settings', 'Payment Settings', 'administrator', 'payment-settings-page', 'payment_settings_page');
    add_action('admin_init', 'payment_register_mysettings');
}

function payment_register_mysettings() {

    register_setting('cre-settings-group', 'cre_payment_login_id');
    register_setting('cre-settings-group', 'cre_payment_transaction_key');
    register_setting('cre-settings-group', 'cre_payment_mode');
}

function payment_settings_page() {
    ?>
    <div class="wrap">
        <h2>Payment Settings</h2>

        <?php
        if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true'):
            echo '<div id="setting-error-settings_updated" class="updated settings-error"> 
<p><strong>Settings saved.</strong></p></div>';
        endif;
        ?>

        <form method="post" action="options.php">
            <?php settings_fields('cre-settings-group'); ?>
            <?php do_settings_sections('cre-settings-group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Authorize.net Login ID</th>
                    <td><input type="text" style="width:50%" name="cre_payment_login_id" value="<?php echo get_option('cre_payment_login_id'); ?>" placeholder="API Login ID" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Authorize.net Transaction Key</th>
                    <td><input type="text" style="width:50%" name="cre_payment_transaction_key" value="<?php echo get_option('cre_payment_transaction_key'); ?>" placeholder="API Transaction Key" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Mode(Live/Test Sandbox)</th>
                    <td><select name="cre_payment_mode" />
                <option value="live" <?php
                if (get_option('cre_payment_mode') == "live"): echo 'selected';
                endif;
                ?> >Live</option>
                <option value="test" <?php
                        if (get_option('cre_payment_mode') == "test"): echo 'selected';
                        endif;
                        ?> >Test/Sandbox</option>
                </select></td>
                </tr>
            </table>

    <?php submit_button(); ?>

        </form>		
        <p style="font-weight:bold;">Use short code [cre_payment]</p>
    </div><?php
}
