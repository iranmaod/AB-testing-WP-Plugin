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
$licensetable = $wpdb->prefix . "abtesting_license";

$test_id = isset($_GET['id']) ? $_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';
$remove = isset($_GET['remove']) ? $_GET['remove'] : '';
$msg = "";

include_once("ab-testing-common.php");
//===== delete the test
if($action == "reset"){
	if($test_id != 0){
		$refresh_date = date('Y-m-d H:i:s');
		
		$sql_update = "update $table_name set created_at='$refresh_date', status ='running' where id = '$test_id' ";// reset the test
		$wpdb->query($sql_update);
	}
	$msg = "Test is reset successfully!";
}


if($action == "delete"){
	if($test_id != 0){
		$wpdb->delete( $table_name, array( 'id' => $test_id ) );
	}
	$msg = "Test is removed successfully!";
}

if ($remove === "license") {
    $wpdb->query("TRUNCATE TABLE $licensetable");

// if license is removed update all tests to pause
	$sql_update = "update $table_name set status ='paused' ";// reset all tests
	$wpdb->query($sql_update);
	
	//$sql_update = "update $licensetable set active ='0' ";// reset all tests to pause if license is expired
    //$wpdb->query($sql_update);
			
    // $wpdb->query("TRUNCATE TABLE $table_name");
    // $wpdb->query("TRUNCATE TABLE " . $wpdb->prefix . "abtesting_visits");

    //wp_redirect(admin_url('/admin.php?page=ab-testing-software-license', 'http'), 301);
	$redirect_url = "/admin.php?page=ab-testing-software-license";
	redirect_to_url($redirect_url);
    exit;
}

$results = $wpdb->get_results ("SELECT * FROM $table_name ORDER BY `id` DESC");

$license = $wpdb->get_row("SELECT * FROM $licensetable WHERE `active` = '1'");

$licenseCode = "";
$licenseDate = "";
$licenseTest = "0";
$licenseDays = "0";


if (!empty($license)) {
	$expiry_date = get_expiry_date($license->license);
    if (time() <= strtotime($expiry_date)) {
        $licenseCode = $license->license;
        $startdate = $license->startdate;
        $enddate = $license->enddate;
        $licenseTest = get_total_tests($licenseCode);
		
        $testDays = get_test_days($licenseCode);
        if (count($results) >= $licenseTest) {
            $msg = "Your tests limit is reached.";
        }
    } else {
        $results = array();
		$wpdb->query("TRUNCATE TABLE $licensetable");// remove license entry since license is expired
		
		$sql_update = "update $table_name set status ='paused' ";// reset all tests to pause if license is expired
		$wpdb->query($sql_update);
	
        $msg = "<a href='admin.php?page=ab-testing-software-license'><span>YOUR LICENSE IS EXPIRED. ALL TESTS PAUSED UNTIL RENEWAL</span> Click to request a new license.</a>";
		
		$redirect_url = "/admin.php?page=ab-testing-software-license";
		//redirect_to_url($redirect_url);
    }
} else {
    //wp_redirect(admin_url('/admin.php?page=ab-testing-software-license', 'http'), 301);
	$redirect_url = "/admin.php?page=ab-testing-software-license";
	redirect_to_url($redirect_url);
		    
}

$statusClass = array(
    'running'   => 'abts-new-test',
    'paused'    => 'abts-pause',
    'error'     => 'abts-error',
    'renew'     => 'abts-renew-test',
);

$urlparts = wp_parse_url(home_url());
$domain = $urlparts['host'];
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="abts-main-container cloud-style-1">
    <div class="abtsMenuMain">
        <div class="abtsmenContainer">
                <span>Menu</span>
                <ul>
                    <li><a href="https://themartechstack.com/AB-testing-software" target="_blank">Request New License</a></li>
                    <li><a href="https://themartechstack.com/AB-testing-software/how-to-guide" target="_blank">Contact Support</a></li>
                    <li><a href="admin.php?page=ab-testing-software&remove=license" onclick="return confirm('Are you sure?')">Remove License</a></li>
                    <li><a href="https://themartechstack.com/request-specialist/" target="_blank">Request Marketing Specialist</a></li>
                    <li><a href="https://themartechstack.com/AB-testing-software/terms-conditions" target="_blank">Terms & Conditions</a></li>
                    <li class="exp-abts-data">
                        <a href="javascript:void(0)">
                            <p class="license_code"><?php echo $licenseCode; ?></p>
                            <p class="startdate"><?php echo $startdate; ?></p>
                            <p class="enddate"><?php echo $expiry_date; ?></p>
                            <p class="licenseTest"><?php echo $licenseTest; ?> Tests</p>
                            <p class="licenseDays"><?php echo $testDays; ?> Days</p>
                        </a>
                    </li>
                </ul>
        </div>
    </div>
    <div class="abtsMainheader">
        <h1 class="abts-heading">A/B Testing Software <p><a href="https://themartechstack.com/" style="color:#1d2327; margin-top:-10px; display:block;" target="_blank">by TheMartechStack.com</a></p> 
            <span class="domain">
                <a href="https://themartechstack.com/AB-testing-software/how-to-guide/" class="abts-help-link" target="_blank">How do I run a test?</a>
            </span>
        </h1>
    </div>
    
    <div class="abts-main-bgstyle">
        <div class="abts-container">
            <div class="abts-info-section">
                <div class="abts-info-item">
                    <span>Website Name:</span>
                    <span><?php echo $domain ?></span>
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
            <?php  if (count($results) >= $licenseTest) { ?>
                    <a href="javascript:void(0)" class="abts-btn abts-pause">CREATE NEW TEST</a>
                <?php }else{ ?>
                    <a href="admin.php?page=ab-testing-software-new" class="abts-btn abts-create-new-test">CREATE NEW TEST</a>
                <?php } ?>
        </div>

        <div class="abts-table-container">
            <table class="abts-table">
                <thead>
                <?php if($msg != ""){?>
                    <tr>
                        <th colspan="6" style="text-align:center;color:red; font-size:17px;" class="redtext"> <?php echo $msg?></th>
                    </tr>
                    <?php }?>
                    <tr>
                        <th colspan="6" style="text-align:center;">You have created <?php echo count($results); ?> out of <?php echo $licenseTest; ?></th>
                    </tr>
                    <tr>
                        <th>TEST NAME</th>
                        <th>ORIGINAL</th>
                        <th>REDIRCT TO VARIANT</th>
                        <th>Status</th>
                        <th>Days Remaining</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( count($results) > 0 ) { ?>
                        <?php foreach ($results as $test) {
                            $page = $wpdb->get_results ("SELECT * FROM `" . $wpdb->prefix . "posts` WHERE `ID` = '".$test->page_id."'");
                            ?>
                            <tr>
                                <td><?php echo $test->name; ?></td>
                                <td><a href="/<?php echo $page[0]->post_name; ?>" target="_blank">/<?php echo $page[0]->post_name; ?></a></td>
                                <td>
                                    <?php
                                        $variants = unserialize($test->variants);
                                        if ( count($variants) > 0 ) {
                                            foreach ($variants as $variant) {
                                                $page = $wpdb->get_results ("SELECT * FROM `" . $wpdb->prefix . "posts` WHERE `ID` = '".$variant."'");
                                                echo '<p><a href="/'.$page[0]->post_name.'" target="_blank">/'.$page[0]->post_name.'</a></p>';
                                            }
                                        }
										
										$creation_date = $test->created_at;
										$today_date = date("Y-m-d h:i:s");
										 $datediff = time() - strtotime($creation_date);
										$created_days =  round($datediff / (60 * 60 * 24));

	                                    $remaining_days = ($testDays - $created_days);
                                    ?>
                                </td>
                                <td>
                                
                                <?php if($remaining_days > 0){?>
                                <button class="abts-btn <?php echo $statusClass[$test->status]; ?>"><?php echo strtoupper($test->status); ?></button>
                                <?php }else{?>
                                <a href="?page=ab-testing-software&id=<?php echo $test->id?>&action=reset" class="abts-btn abts-btn-ex">
								Completed<br/>CLICK TO RESTART
                                </a>
                                
                                </button>
                                
                                <?php }?>
                                
                                </td>
                                <td>
                                
                                	<?php
										
										$test_id = $test->id;
										//echo $sql_pause = "update $table_name set status ='paused' where id = '$test_id' ";// set status to puase
										if($remaining_days == 0){
											$sql_pause = "update $table_name set status ='paused' where id = '$test_id' ";// set status to puase
											$wpdb->query($sql_pause);
										}
										
										if($remaining_days >0){										
											echo $remaining_days;
										}else{
												echo 0;
											}
									?>
                                </td>
                                <td class="action">
                                    <a href="admin.php?page=ab-testing-software-new&id=<?php echo $test->id; ?>" class="abts-btn">Edit</a>
                                    <!-- <a href="admin.php?page=ab-testing-software&id=<?php echo $test->id; ?>&action=delete" class="abts-btn" onclick="return confirm('Are you sure?')">
                                        <div class="dashicons dashicons-trash"></div>
                                    </a> -->
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                    <tr>
                    	<td colspan="6">
                        	
                        <strong>Caution:</strong><br />
                        Avoid creating multiple tests with the same original page.<br />
                        Avoid creating a variant destination as the original page for a test.<br />                        
                        These can create infinite redirect load loops resulting in using excessive server resources and a poor page visitor experience.  
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
