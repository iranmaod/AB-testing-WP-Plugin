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

include_once("ab-testing-common.php");

$test = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST) && !empty($_POST)) {

        if ( empty($_POST['id']) ) {
            $wpdb->insert( $table_name, array(
                'name'          => $_POST['test_name'],
                'page_id'       => $_POST['page_id'],
                'variants'      => serialize($_POST['variants']),
                'status'        => $_POST['status']),
                array( '%s', '%s', '%s', '%s' ) 
            );
        } else {
            $data = array(
                'name'          => $_POST['test_name'],
                'page_id'       => $_POST['page_id'],
                'variants'      => serialize($_POST['variants']),
                'status'        => $_POST['status']
            );

            $wpdb->update($table_name, $data, array('id' => $_POST['id']));
        }
        
        //wp_redirect(admin_url('/admin.php?page=ab-testing-software', 'http'), 301);
		$redirect_url = "/admin.php?page=ab-testing-software";
		redirect_to_url($redirect_url);
        exit;
    }
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $result = $wpdb->get_results ("SELECT * FROM $table_name WHERE `id` = '" . $_GET['id'] . "'");
    if (isset($result[0]) && !empty($result[0])) {
        $test = $result[0];
    }
}

$license = $wpdb->get_row("SELECT * FROM $licensetable WHERE `active` = '1'");
$test_variants = 10;// assign default variants
$total_license_days = 0;
//echo "<pre>"; print_r($license); die();
if (!empty($license)) {
	$test_variants = get_test_variants($license->license);
	 $expiry_date = get_expiry_date($license->license);
	$licenseTest = get_total_tests($license->license);
	
	$start_date = $license->startdate;
//	$total_license_days = get_license_days($start_date,$expiry_date);
	$total_license_days = get_test_days($license->license);
	
	//die();
	//echo time() . " and " ;
	//echo strtotime($expiry_date);
	//echo $license->tests; die();
	//echo $test_variants ." are total variants";
    if (time() <= strtotime($expiry_date)) {
		
        $testResult = $wpdb->get_results("SELECT * FROM $table_name");
		

        if (count($testResult) >= $licenseTest && !isset($_GET['id'])) {
//            wp_redirect(admin_url('/admin.php?page=ab-testing-software', 'http'), 301);
			$redirect_url = "/admin.php?page=ab-testing-software";
			redirect_to_url($redirect_url);
            exit;
        }
    } else {
        if (!isset($_GET['id'])) {
			$redirect_url = "/admin.php?page=ab-testing-software-license";
			redirect_to_url($redirect_url);
           // wp_redirect(admin_url('/admin.php?page=ab-testing-software-license', 'http'), 301);
            exit;
        }
    }
} else {
    //wp_redirect(admin_url('/admin.php?page=ab-testing-software-license', 'http'), 301);
	$redirect_url = "/admin.php?page=ab-testing-software-license";
	redirect_to_url($redirect_url);
    exit;
}

$urlparts = wp_parse_url(home_url());
$domain = $urlparts['host'];

$q = "SELECT * FROM ".$wpdb->prefix . "posts WHERE (`post_type` = 'post' or post_type = 'page') and `post_status` = 'publish' ";
$q = $q . " order by post_type asc";
$pages =  $wpdb->get_results($q);

$q_blog = "SELECT * FROM ".$wpdb->prefix . "posts WHERE `post_type` = 'post' and `post_status` = 'publish' ";
$blog_pages =  $wpdb->get_results($q_blog);

$options = '<option value="0" class="dis">Select By Page Name</option>';
if ($pages) {
	$blog = 1;
    foreach ($pages as $page) {
		$post_type = $page->post_type;
		$oc = "abts-blog-list";
		if($post_type == "page") $oc = "abts-pages-list";
		if($post_type == "post" && $blog == 1){
			$options .= '<option class="dis" disabled="disabled">Select Blog Pages</option>';
			$blog = 2;
			
		}
        $options .= '<option class="'.$oc.'" value="' . $page->ID . '">&emsp;' . $page->post_name . '</option>';
    }
}
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="abts-main-container">
    <h1 class="abts-heading">AB Testing Software<br /><p><a href="https://themartechstack.com/" style="color:#1d2327; margin-top:-10px; display:block;" target="_blank">by TheMartechStack.com</a></p></h1>
    <span class="domain"><?php echo $domain ?>/</span>
    <div class="abts-main-bgstyle">
        <form id="abts-test-form" method="post">
            <input type="hidden" name="id" <?php if (isset($test->id)) { echo 'value="'.$test->id.'"'; } ?> />
            <div class="abts-form-group">
                <label for="abts-test-name">Name Your Test</label>
                <input type="text" id="abts-test-name" name="test_name" placeholder="e.g. Landing page test design" <?php if (isset($test->name)) { echo 'value="'.$test->name.'"'; } ?> required />
                <?php if (isset($test->id)) { ?>
                    <a href="admin.php?page=ab-testing-software&id=<?php echo $test->id; ?>&action=delete" class="abts-btn abts-delete-btn" onclick="return confirm('Are you sure?')">Delete Test</a>
                <?php } ?>
            </div>

            <div class="abts-form-group">
                <label for="abts-current-url">What is the current page (select by page name)</label>
                <div class="abts-variant-item">
                    <select id="abts-current-url" name="page_id" require>
                        <option value="0" class="dis">Select By Page Name</option>
                        <?php
                        if ($pages) {
							$blog = 1;
                            foreach ($pages as $page) {
                                $selected = '';
								
								$post_type = $page->post_type;
								$oc = "abts-blog-list";
								if($post_type == "page") $oc = "abts-pages-list";
								if($post_type == "post" && $blog == 1){
									echo "<option class='dis' disabled='disabled'>Select Blog Pages</option>";
									$blog = 2;
									
								}
								
                                if (isset($test->page_id) && $test->page_id == $page->ID) {
                                    $selected = ' selected';
                                }
                                echo '<option class="'.$oc.'" value="' . $page->ID . '" ' . $selected . '>&emsp;' . $page->post_name . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <!-- <input type="text" id="abts-current-url" name="current_url" <?php //if (isset($test->current_url)) { echo 'value="'.$test->current_url.'"'; } ?> required autocomplete="off" onkeyup="loadResults('abts-current-url','key')"  />
                    <input type="hidden" name="page_id" id="abts-page-id" <?php //if (isset($test->page_id)) { echo 'value="'.$test->page_id.'"'; } ?> />
                    <div id="key" class="abtspageslist"></div> -->
                </div>
            </div>

            <div class="abts-form-group">
                <label>Where will the visitors be redirected?</label>
                <div id="abts-variants-container">
                    <?php
                    if (isset($test->variants)) {
                        $variants = unserialize($test->variants);
                    }
                    ?>
                    <div class="abts-variant-item">
                        <select id="variant1" name="variants[]" require>
                            <option value="0" class="dis">Select By Page Name</option>
                            <?php
							$blog = 1;
                            if ($pages) {
                                foreach ($pages as $page) {
                                    $selected = '';
									
									$post_type = $page->post_type;
									$oc = "abts-blog-list";
									if($post_type == "page") $oc = "abts-pages-list";
									if($post_type == "post" && $blog == 1){
										echo "<option class='dis' disabled='disabled'>Select Blog Pages</option>";
										$blog = 2;
										
									}
                                    if (isset($variants) && isset($variants[0]) && $variants[0] == $page->ID) {
                                        $selected = ' selected';
                                    }
                                    echo '<option class="'.$oc.'" value="' . $page->ID . '" ' . $selected . '>&emsp;' . $page->post_name . '</option>';
                                }
                            }
                            ?>
                        </select>
                        <!-- <input type="text" name="variants[]" id="variant0" <?php //if(isset($variants) && isset($variants[0])) { echo 'value="'.$variants[0]['name'].'"'; } ?> onkeyup="loadResults('variant0','key1')" required="required" />
                        <input type="hidden" name="variant_page_id[]" id="abts-page-id-key1" <?php //if(isset($variants) && isset($variants[0])) { echo 'value="'.$variants[0]['page_id'].'"'; } ?> />
                        <div id="key1" class="abtspageslist"></div> -->

                        <button type="button" class="abts-remove-variant-btn">Remove</button>
                    </div>
                    <?php if (isset($variants)) {
                        $ks = 2;
                        
                        foreach ($variants as $k => $variant) {
                            $key = "key" . $ks;
                            $v = "variant" . ($ks-1);
                        
                            if ($k > 0) { 
                            ?>
                                <div class="abts-variant-item">
                                    <select id="variant<?php echo $ks; ?>" name="variants[]">
                                        <option value="0" class="dis">Select By Page Name</option>
                                        <?php
                                        if ($pages) {
											$blog = 1;
                                            foreach ($pages as $page) {
                                                $selected = '';
												$post_type = $page->post_type;
												$oc = "abts-blog-list";
												if($post_type == "page") $oc = "abts-pages-list";
												if($post_type == "post" && $blog == 1){
													echo "<option class='dis' disabled='disabled'>Select Blog Pages</option>";
													$blog = 2;
													
												}
												
                                                if ($variant == $page->ID) {
                                                    $selected = ' selected';
                                                }
                                                echo '<option class="'.$oc.'" value="' . $page->ID . '" ' . $selected . '>&emsp;' . $page->post_name . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                    <!-- <input type="text" name="variants[]"  value="<?php //echo $variant['name']; ?>" onkeyup="loadResults('<?php //echo $v?>','<?php //echo $key?>')" id="<?php //echo $v?>"> -->
                                    <!-- <input type="hidden" name="variant_page_id[]" id="abts-page-id-<?php //echo $key?>" value="<?php //echo $variant['name']; ?>" /> -->
                                    <button type="button" class="abts-remove-variant-btn">Remove</button>
                                    <!-- <div id="<?php echo $key?>" class="abtspageslist"></div> -->
                                </div>
                                
                            <?php
                                $ks++;
                            }
                        }
                    } ?>
                </div>
                <button type="button" id="abts-add-variant-btn" class="abts-add-btn">+ Add new variant (max <?php echo $test_variants?> allowed)</button>
            </div>

            <div class="abts-form-group">
                <label for="abts-status">Status of Test</label>
                <select name="status" id="abts-status" class="" required>
                    <option value="" <?php if (isset($test->status) && $test->status == '') { echo 'selected'; } ?>>Select Status</option>
                    <option value="running" <?php if (isset($test->status) && $test->status == 'running') { echo 'selected'; } ?>>RUNNING</option>
                    <option value="paused" <?php if (isset($test->status) && $test->status == 'paused') { echo 'selected'; } ?>>PAUSED</option>                   
                </select>
            </div>
            <input name="max_test_variant" id="max_test_variant" value="<?php echo $test_variants?>" type="hidden" />

            <div class="abts-form-group">
                <p>For data security, AB Testing Software will run for a <?php echo $total_license_days?>-day period.</p>
                <p>At the end of this period, it will pause, and the current URL page will be shown. You will need to log in and renew the test.</p>
                <!-- <p>We will send you a notification when you are close to this limit.</p> -->
            </div>

            <div class="abts-form-group submitbtnMain">
                <a class="abts-submit-btn" href="/wp-admin/admin.php?page=ab-testing-software">Exit without saving</a>
                <button type="submit" id="abts-save-btn" class="abts-submit-btn">Save</button>
            </div>
        </form>
    </div>
</div>


	

<script type="text/javascript">
    const options = '<?php echo $options; ?>';

    function loadResults(fld,key){
	    var f = "#" + fld;
		cid = jQuery(f).val();
		
		console.log(cid);
		var ajaxurl = "<?php echo admin_url( 'admin-ajax.php' ); ?>"; 
		
		var k = "#" + key;

		var data = {
            action: "post_tests_action",
            pid: cid,
            fd: fld,
            kd: key
        };

		jQuery.post(ajaxurl, data, function (response){
            jQuery('.abtspageslist').hide();
            jQuery(k).show();
            jQuery(k).html(response);
		});
	}

    // all code will have to be moved in the js file.
    jQuery(document).ready(function($) {
        jQuery('.auto_complete').keyup(function() { 
			// jQuery('#key').show();
			// jQuery(this).parent().find('div').show();
			jQuery(this).parent().closest('#key').show();

            cid = jQuery(this).val();
            console.log(cid);
            var ajaxurl = "<?php echo admin_url( 'admin-ajax.php' ); ?>"; 
            // var ajaxurl = { ajax_url : 'adevelopers.net/wp-admin/admin-ajax.php' }

            var data = {
                action: "post_tests_action",
                pid: cid
            };
            $.post(ajaxurl, data, function (response){
                alert(response);
                //    $('#key').html(response);
                //    jQuery(this).parent().find('div').html(response);
                jQuery(this).parent().closest('#key').html(response);
            });
        });
    });

    function getval(value, fld, key, pid) {
        var f = "#" + fld;
        var k = "#" + key;
        console.log(value);
        console.log(fld);
        console.log(key);
        console.log(pid);

        if (key == 'key') {
            jQuery('#abts-page-id').val(pid);
        } else {
            jQuery('#abts-page-id-' + key).val(pid);
        }
        
        jQuery(f).val(value);
        jQuery(k).hide();
    } 
</script>	