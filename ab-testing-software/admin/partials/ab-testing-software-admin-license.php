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
$table_name = $wpdb->prefix . "abtesting_license";
$license_exists = $wpdb->get_row( "SELECT * FROM $table_name WHERE `active` = '1'" );

include_once("ab-testing-common.php");
//echo $admin_url = admin_url('/admin.php?page=ab-testing-software', 'http');
//echo "<pre>"; print_r($license_exists);
if (!empty($license_exists)) {
	 $expiry_date = get_expiry_date($license_exists->license);
	
	if (time() <= strtotime($expiry_date)) {	
    
		$redirect_url = "/admin.php?page=ab-testing-software";
		redirect_to_url($redirect_url);

	
    //    wp_redirect(admin_url('/admin.php?page=ab-testing-software', 'http'), 301);
      //  exit;
    }
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($license_exists)) {
		$expiry_date = get_expiry_date($license_exists->license);
        if (time() <= strtotime($expiry_date)) {	
            '<div class="alert alert-success">Your license is already active.</div>'; exit;
        }
    }

    if (isset($_POST) && !empty($_POST)) {
        if(filter_var($_POST['domain'], FILTER_VALIDATE_URL)) {
            $domain = parse_url($_POST['domain'], PHP_URL_HOST);
            $domain = str_replace('www.', '', $domain);
        } else {
            $domain = str_replace('www.', '', $_POST['domain']);
        }

        $str = $domain . $_POST['email'] . $_POST['firstname'] . $_POST['lastname'];
        $code = md5($str);

        $providedCode = explode("|", $_POST['code']);
        $addionalParams = base64_decode($providedCode[1]);
        $paramsArray = explode("-", $addionalParams);
		
		//echo "<pre>"; print_r($paramsArray);
//		$paramsArray[4] = "Sun Dec 29 2024 00:00:00";
		 $expiry_date = str_replace("/","-",$paramsArray[4]);
//		echo $expiry_date = date('Y-m-d',strtotime($paramsArray[4]));

		
	//	die();

        if ($code === $providedCode[0] && $paramsArray[0] === "ABT" && (sizeof($paramsArray) == 5)) {
			
			//$sql_update = "update $table_name set active ='0' ";// reset all tests to pause if license is expired
		    //$wpdb->query($sql_update);
			
			$wpdb->query("TRUNCATE TABLE $table_name");

//===============================================            
			$wpdb->insert( $table_name, array(
                'license'   => $_POST['code'],
                'startdate' => date('Y-m-d H:i:s'),
                'days'      => $paramsArray[1],
                'data'      => serialize($_POST),
                'tests'     => $paramsArray[2],
                'active'    => 1,
                'enddate'   => date('Y-m-d H:i:s', strtotime('+' . $paramsArray[1] . ' days'))),
                array( '%s', '%s', '%s', '%s', '%s', '%s' ) 
            );
			
			$refresh_date = date('Y-m-d H:i:s');
			//$sql_update = "update wp_9t3fji_abtesting_tests set created_at='$refresh_date' , status ='running' ";// reset all tests
			//$wpdb->query($sql_update);


            //wp_redirect(admin_url('/admin.php?page=ab-testing-software', 'http'), 301);
			$redirect_url = "/admin.php?page=ab-testing-software";
			redirect_to_url($redirect_url);
            //exit;
        } else {
            $msg = '<div class="alert alert-danger" style="text-align:center;color:red;font-size:17px;">License code does not match. Please recheck the original information you used to register. Fields are case sensitive.</div>';
        }
    }
}
$urlparts = wp_parse_url(home_url());
$domain = $urlparts['host'];
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="abts-main-container abtsrequest">
    <h1 class="abts-heading">Thank You For Installing <br /> AB Testing Software<br /><p><a href="https://themartechstack.com/" style="color:#1d2327; margin-top:-10px; display:block;" target="_blank">by TheMartechStack.com</a></p></h1>
   <?php /*?> <p class="abts-heading-m">
    You may have reached the end of your license term The plugin may have updated (requiring a new license) Or something else may have caused a mismatch. 
</p>
<p class="abts-heading-s">
Don't worry, any tests you previously configured will still remain once reactivated.
</p><?php */?>
    
    <p class="abts-heading-t">Please input the same information you used during registeration.</p>

    <?php
    if (!empty($msg) && $msg != "") {
        echo $msg;
    }

    if (!empty($license_exists)) {
        if (time() <= strtotime($expiry_date)) {	
            echo '<div class="alert alert-success"><span class="abts-btn">Your license is active</span></div>';
        }
    }

$urlparts = wp_parse_url(home_url());
$domain = str_replace("www.","",$urlparts['host']);	

?>

    <div class="request-lc"><a class="abts-btn" href="https://themartechstack.com/AB-testing-software">REQUEST LICENSE CODE</a></div>

    <div class="abts-main-bgstyle">
        <form id="abts-test-license-form" method="post">
            <div class="abts-form-group">
                <label for="abts-test-domain">Domain</label>
                <input type="text" id="abts-test-domain" name="domain" placeholder="Domain" required value="<?php echo $domain?>" readonly="readonly" />
            </div>

            <div class="abts-form-group">
                <label for="abts-test-email">Email Address</label>
                <input type="email" id="abts-test-email" name="email" placeholder="Email Address" required />
            </div>

            <div class="abts-form-group">
                <label for="abts-test-firstname">First Name</label>
                <input type="text" id="abts-test-firstname" name="firstname" placeholder="First Name" required />
            </div>

            <div class="abts-form-group">
                <label for="abts-test-lastname">Last Name</label>
                <input type="text" id="abts-test-lastname" name="lastname" placeholder="Last Name" required />
            </div>

            <div class="abts-form-group">
                <label for="abts-test-code">Copy/paste the code you received in the email</label>
                <input type="text" id="abts-test-code" name="code" placeholder="Copy/paste the code you received in the email" required />
            </div>

            <div class="abts-form-group">
                <button type="submit" id="abts-save-btn" class="abts-submit-btn">Submit</button>
            </div>
        </form>
    </div>
</div>

