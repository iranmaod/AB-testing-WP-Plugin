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


$fld = $_POST['fd'];
$key = $_POST['kd'];

 global $wpdb;
// echo "<pre>"; print_r($_POST);
            $url = $_POST['pid'];
			$q = "select * from ".$wpdb->prefix . "posts  where post_type='page' and post_status ='publish' ";
			
			if($url != ''){
					$q = $q . " and post_title like '%$url%'";
				}
//			echo $q;	
//			    $result =   $mytables=$wpdb->get_results("select * from ".$wpdb->prefix . "mycity where city like '%".$city."'" );   
			
            $result =  $wpdb->get_results($q);   
            $data = "";
            echo '<ul>';
            foreach($result as $dis)
            {
//              echo '<li>'.$dis->post_title.'</li>';
?>
<li>
			  <a href="javascript:;" onclick ="getval('<?php echo $dis->post_title?>','<?php echo $fld?>','<?php echo $key?>')" value="<?php echo $dis->ID?>"> <?php echo $dis->post_title;?></a></li>
<?php			                
            }
            echo '</ul>';    
            die();

//add_action( 'wp_ajax_post_tests_action', 'tests_action_callback' );
//add_action( 'wp_ajax_nopriv_post_tests_action', 'tests_action_callback' );

?>
		
