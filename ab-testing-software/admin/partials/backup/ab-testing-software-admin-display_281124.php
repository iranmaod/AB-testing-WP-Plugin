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

$test_id = isset($_GET['id']) ? $_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';
$msg = "";
//===== delete the test
if($action == "delete"){
	
	if($test_id != 0){
		$wpdb->delete( $table_name, array( 'id' => $test_id ) );
	}
	$msg = "Test is removed successfully!";

}

$results = $wpdb->get_results ("SELECT * FROM $table_name ORDER BY `id` DESC");

$statusClass = array(
    'running'   => 'abts-new-test',
    'paused'    => 'abts-pause',
    'error'     => 'abts-error',
    'renew'     => 'abts-renew-test',
);
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<h1 class="abts-heading">Welcome to ABtestingSoftware.com</h1>
<div class="abts-container">
    <div class="abts-info-section">
        <div class="abts-info-item">
            <span>Website Name:</span>
            <span>ABtestingSoftware.com</span>
        </div>
        <div class="abts-info-item">
            <span>Account Status:</span>
            <span class="abts-status abts-active">ACTIVE</span>
        </div>
    </div>

    <!-- <div class="abts-buttons">
        <button class="abts-btn abts-pause">PAUSE ALL</button>
        <button class="abts-btn abts-error">ERROR</button>
    </div> -->

    <hr />

    <a href="admin.php?page=ab-testing-software-new" class="abts-btn abts-create-new-test">CREATE NEW TEST</a>
    <p><a href="#" class="abts-help-link">How do I run a test?</a></p>
</div>

<div class="abts-table-container">
    <table class="abts-table">
        <thead>
        <?php if($msg != ""){?>
            <tr>
            	<th colspan="5" style="text-align:center;color:red; font-size:17px;"> <?php echo strtoupper($msg)?></th>
            </tr>
            <?php }?>
            <tr>
                <th>TEST NAME</th>
                <th>ORIGINAL</th>
                <th>REDIRCT TO VARIANT</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( count($results) > 0 ) { ?>
                <?php foreach ($results as $test) { ?>
                    <tr>
                        <td><?php echo $test->name; ?></td>
                        <td><?php echo $test->current_url; ?></td>
                        <td>
                            <?php
                                $variants = unserialize($test->variants);
                                if ( count($variants) > 0 ) {
                                    foreach ($variants as $variant) {
                                        echo '<p>'.$variant.'</p>';
                                    }
                                }
                            ?>
                        </td>
                        <td><button class="abts-btn <?php echo $statusClass[$test->status]; ?>"><?php echo strtoupper($test->status); ?></button></td>
                        <td>
                            <a href="admin.php?page=ab-testing-software-new&id=<?php echo $test->id; ?>" class="abts-btn">EDIT</a> | <a href="admin.php?page=ab-testing-software&id=<?php echo $test->id; ?>&action=delete" class="abts-btn" onclick="return confirm('Are you sure?')">DELETE</a>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>
            <!--
            <tr>
                <td>Landing Page</td>
                <td>/directory/page-1.html</td>
                <td>
                    <p>/page-2.html</p>
                    <p>/page-3.html</p>
                    <p>/page-4.html</p>
                </td>
                <td><button class="abts-btn abts-new-test">Running</button></td>
            </tr>
            <tr>
                <td>Landing Page</td>
                <td>/directory/page-1.html</td>
                <td>
                    <p>/page-2.html</p>
                    <p>/page-3.html</p>
                    <p>/page-4.html</p>
                </td>
                <td><button class="abts-btn abts-pause">Paused</button></td>
            </tr>
            <tr>
                <td>Landing Page</td>
                <td>/directory/page-1.html</td>
                <td>
                    <p>/page-2.html</p>
                    <p>/page-3.html</p>
                    <p>/page-4.html</p>
                </td>
                <td><button class="abts-btn abts-error">Error</button></td>
            </tr>
            <tr>
                <td>Landing Page</td>
                <td>/directory/page-1.html</td>
                <td>
                    <p>/page-2.html</p>
                    <p>/page-3.html</p>
                    <p>/page-4.html</p>
                </td>
                <td><button class="abts-btn abts-renew-test">Renew Test</button></td>
            </tr>
            -->
        </tbody>
    </table>
</div>

<!--
<form id="abts-test-form">
    <div class="abts-form-group">
        <label for="abts-test-name">Name Your Test</label>
        <input type="text" id="abts-test-name" name="test_name" placeholder="e.g. Landing page test design">
    </div>

    <div class="abts-form-group">
        <label for="abts-current-url">What is the URL of the current page?</label>
        <input type="url" id="abts-current-url" name="current_url" placeholder="http://www.mywebsite.com/product/thispage.html">
    </div>

    <div class="abts-form-group">
        <label>What are the URLs where visitors will be redirected?</label>
        <div id="abts-variants-container">
            <div class="abts-variant-item">
                <input type="url" name="variants[]" placeholder="http://www.mywebsite.com/product/thispage V#">
                <button type="button" class="abts-remove-variant-btn">Remove</button>
            </div>
        </div>
        <button type="button" id="abts-add-variant-btn" class="abts-add-btn">+ Add new variant (max 10 allowed)</button>
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
-->
