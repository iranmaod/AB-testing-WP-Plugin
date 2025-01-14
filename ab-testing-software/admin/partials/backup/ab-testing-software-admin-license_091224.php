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

if (!empty($license_exists)) {
    if (time() >= strtotime($license_exists->startdate) && time() <= strtotime($license_exists->enddate)) {
		$redirect_url = "/admin.php?page=ab-testing-software";
		redirect_to_url($redirect_url);

	
    //    wp_redirect(admin_url('/admin.php?page=ab-testing-software', 'http'), 301);
      //  exit;
    }
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($license_exists)) {
        if (time() >= strtotime($license_exists->startdate) && time() <= strtotime($license_exists->enddate)) {
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

        if ($code === $providedCode[0] && $paramsArray[0] === "ABT") {
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
    <h1 class="abts-heading">Thank you for installing<br />AB Testing Software</h1>
    <p>Please input the same information you used during registeration.</p>

    <?php
    if (!empty($msg) && $msg != "") {
        echo $msg;
    }

    if (!empty($license_exists)) {
        if (time() >= strtotime($license_exists->startdate) && time() <= strtotime($license_exists->enddate)) {
            echo '<div class="alert alert-success"><span class="abts-btn">Your license is active</span></div>';
        }
    }
    ?>

    <div class="request-lc"><a class="abts-btn" href="#" class>REQUEST LICENSE CODE</a></div>

    <div class="abts-main-bgstyle">
        <form id="abts-test-license-form" method="post">
            <div class="abts-form-group">
                <label for="abts-test-domain">Domain</label>
                <input type="text" id="abts-test-domain" name="domain" placeholder="Domain" required />
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

