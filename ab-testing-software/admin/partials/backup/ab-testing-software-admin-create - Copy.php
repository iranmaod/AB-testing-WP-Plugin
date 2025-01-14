<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://themartechstack.com
 * @since      1.0.0
 *
 * @package    Ab_Testing_Software
 * @subpackage Ab_Testing_Software/admin/partials
 */

global $wpdb;
$table_name = $wpdb->prefix . "abtesting_tests";

$test = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST) && !empty($_POST)) {

        if ( empty($_POST['id']) ) {
            $wpdb->insert( $table_name, array(
                'name'          => $_POST['test_name'], 
                'current_url'   => $_POST['current_url'],
                'variants'      => serialize($_POST['variants']),
                'status'        => $_POST['status']),
                array( '%s', '%s', '%s', '%s' ) 
            );
        } else {
            $data = array(
                'name'          => $_POST['test_name'], 
                'current_url'   => $_POST['current_url'],
                'variants'      => serialize($_POST['variants']),
                'status'        => $_POST['status']
            );

            $wpdb->update($table_name, $data, array('id' => $_POST['id']));
        }
        
        wp_redirect(admin_url('/admin.php?page=ab-testing-software', 'http'), 301);
        exit;
    }
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $result = $wpdb->get_results ("SELECT * FROM $table_name WHERE `id` = '" . $_GET['id'] . "'");
    if (isset($result[0]) && !empty($result[0])) {
        $test = $result[0];
    }
}
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<h1 class="abts-heading">Welcome to ABtestingSoftware.com</h1>

<form id="abts-test-form" method="post">
    <input type="hidden" name="id" <?php if (isset($test->id)) { echo 'value="'.$test->id.'"'; } ?> />
    <div class="abts-form-group">
        <label for="abts-test-name">Name Your Test</label>
        <input type="text" id="abts-test-name" name="test_name" placeholder="e.g. Landing page test design" <?php if (isset($test->name)) { echo 'value="'.$test->name.'"'; } ?> required />
    </div>

    <div class="abts-form-group">
        <label for="abts-current-url">What is the URL of the current page?</label>
        <input type="url" id="abts-current-url" name="current_url" placeholder="http://www.adevelopers.net" <?php if (isset($test->current_url)) { echo 'value="'.$test->current_url.'"'; } ?> required autocomplete="off" />
        <div id="key"></div>

    </div>

    <div class="abts-form-group">
        <label>What are the URLs where visitors will be redirected?</label>
        <div id="abts-variants-container">
            <?php
            if (isset($test->variants)) {
                $variants = unserialize($test->variants);
            }
            ?>
            <div class="abts-variant-item">
                <input type="url" name="variants[]" placeholder="http://www.adevelopers.net/product/page V#" <?php if(isset($variants) && isset($variants[0])) { echo 'value="'.$variants[0].'"'; } ?>>
                <button type="button" class="abts-remove-variant-btn">Remove</button>
            </div>
            <?php if (isset($variants)) {
                foreach ($variants as $k => $variant) {
                    if ($k > 0) { ?>
                        <div class="abts-variant-item">
                            <input type="text" name="variants[]" placeholder="Page Url" value="<?php echo $variant; ?>">
                            <button type="button" class="abts-remove-variant-btn">Remove</button>
                        </div>
                    <?php }
                }
            } ?>
        </div>
        <button type="button" id="abts-add-variant-btn" class="abts-add-btn">+ Add new variant (max 10 allowed)</button>
    </div>

    <div class="abts-form-group">
        <label for="abts-status">Status of Test</label>
        <select name="status" id="abts-status" class="" required>
            <option value="" <?php if (isset($test->status) && $test->status == '') { echo 'selected'; } ?>>Select Status</option>
            
            <option value="running" <?php if (isset($test->status) && $test->status == 'running') { echo 'selected'; } ?>>RUNNING</option>
            <option value="paused" <?php if (isset($test->status) && $test->status == 'paused') { echo 'selected'; } ?>>PAUSED</option>
            <option value="error" <?php if (isset($test->status) && $test->status == 'error') { echo 'selected'; } ?>>ERROR</option>
            <option value="renew" <?php if (isset($test->status) && $test->status == 'renew') { echo 'selected'; } ?>>RENEW</option>
        </select>
    </div>

    <div class="abts-form-group">
        <p>For data security, AB Testing Software will run for a 15-day period.</p>
        <p>At the end of this period, it will pause, and the current URL page will be shown. You will need to log in and renew the test.</p>
        <p>We will send you a notification when you are close to this limit.</p>
    </div>

    <div class="abts-form-group">
        <button type="submit" id="abts-save-btn" class="abts-submit-btn">Save</button>
    </div>
</form>

<script>
// all code will have to be moved in the js file.
jQuery(document).ready(function($) {
        jQuery('#abts-current-url').keyup(function() { 
			jQuery('#key').show();
                cid = jQuery(this).val();
				console.log(cid);
                var ajaxurl = "<?php echo admin_url( 'admin-ajax.php' ); ?>"; 
//				var ajaxurl = { ajax_url : 'adevelopers.net/wp-admin/admin-ajax.php' }

                var data ={ action: "post_tests_action",  pid:cid    };
                $.post(ajaxurl, data, function (response){
//                          alert(response);
	                       $('#key').html(response);

                        });
                });
});
function getval(value) { 
	console.log(value);
	jQuery('#abts-current-url').val(value)
	jQuery('#key').hide();
} 
</script>	