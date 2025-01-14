<?php
function redirect_to_url($redirect_url){
		//echo 202;
		//echo $redirect_url;
		if (headers_sent()) {
			$admin_url = admin_url($redirect_url, 'http');
?>
	<script type="text/javascript">
		location.href = "<?php echo $admin_url?>";
	</script>
<?php		
		die("Redirect failed.");
	}
		else{
			wp_redirect(admin_url($redirect_url, 'http'), 301);
			exit;
		}
	}
//===========

function get_test_variants($license_code){
		$providedCode = explode("|", $license_code);
    
	    $addionalParams = base64_decode($providedCode[1]);
        $paramsArray = explode("-", $addionalParams);
		
		return $paramsArray[3];
	
}

function get_expiry_date($license_code){
		$providedCode = explode("|", $license_code);
    
	    $addionalParams = base64_decode($providedCode[1]);
        $paramsArray = explode("-", $addionalParams);
		
		//echo "<pre>"; print_r($paramsArray);
		$expiry_date = str_replace(" GMT+0500 (Pakistan Standard Time)","",$paramsArray[4]);
		$expiry_date = date("Y-m-d",strtotime($expiry_date));
		
		return $expiry_date;
	
}	

function get_total_tests($license_code){
		$providedCode = explode("|", $license_code);
    
	    $addionalParams = base64_decode($providedCode[1]);
        $paramsArray = explode("-", $addionalParams);
		
		return $paramsArray[2];
	
}	

function get_test_days($license_code){
		$providedCode = explode("|", $license_code);
    
	    $addionalParams = base64_decode($providedCode[1]);
        $paramsArray = explode("-", $addionalParams);
		
		return $paramsArray[1];
	
}

function get_license_days($start_date,$expiry_date){

	
//	$date1 = "2007-03-24";
//	$date2 = "2009-06-26";
	$start_date = substr($start_date,0,10);

	echo $expiry_date . " and start date"  . $start_date;
	//echo "<br>";
	//$datediff = strtotime($expiry_date) - strtotime($start_date);
	
//	echo ($datediff / (60 * 60 * 24));*/

	
	$diff = abs(strtotime($expiry_date) - strtotime($start_date));

	$years = floor($diff / (365*60*60*24));
	$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
	$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

		return $days;
	
}	
?>