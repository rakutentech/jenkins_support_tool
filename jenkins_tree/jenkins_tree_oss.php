<?php
	
	// smarty
	require_once 'smarty/libs/Smarty.class.php';
	$smarty = new Smarty();
	$smarty->template_dir = 'templates/';
	$smarty->compile_dir  = 'templates_c/';

	// http timeout
	$WEB_TIMEOUT = 1;

	// get GET parameter
	$selected_server_id = "none"; if (!empty($_GET["selected_server_id"])) {$selected_server_id = $_GET["selected_server_id"];}
	$selected_path = ""; if (!empty($_GET["selected_path"])) {$selected_path = $_GET["selected_path"];}

	$foldersArray = explode("job/",$selected_path);
	array_shift ($foldersArray);
	
	$smarty->assign('selected_server_id', $selected_server_id);
	$smarty->assign('selected_path', $selected_path);
	$smarty->assign('foldersArray', $foldersArray);

	// configulation
	$ini_array = parse_ini_file("conf_oss.ini",true);

	// curl setting
	$context = stream_context_create(array(
	  'http' => array('ignore_errors' => true , 'timeout' => $WEB_TIMEOUT)
	));

	// params
	$JENKINS_DOMAIN = $ini_array["JENKINS_DOMAIN"];

	// summary
	$summaryArray = array();

	foreach ($JENKINS_DOMAIN as $server_id => $domain) {
		$summaryArray[$server_id]['domain'] = $domain;
	}

	// get jenkins tree by API
	$jobsArray = array();
	if ($selected_server_id !== "none") {
		$top_url = "http://" . $summaryArray[$selected_server_id]['domain'] . "/";
		$api_url =  $top_url . $selected_path . "/api/json?depth=2";
		try {
			$response = @file_get_contents($api_url , false, $context);
			if ($response != "") {
				$result = json_decode($response,true);
				$jobs = $result['jobs'];
				foreach ($jobs as $jobHash) {
				
					$job_name = $jobHash['name'];
					$_class = $jobHash['_class'];
					$url = $jobHash['url'];
					$color = "";
					if (!empty($jobHash['color'])) {
						$color = $jobHash['color'];
					}
					
					$last_success_timestamp = "";
					$last_failure_timestamp = "";
					$last_id = "";
					if (!empty($jobHash['builds'])) {
						$builds = $jobHash['builds'];
						foreach ($builds as $buildHash) {
							$id = $buildHash['id'];
							$result = $buildHash['result'];
							$timestamp = $buildHash['timestamp'];
							if ($last_id === "") {
								$last_id = $id;
							}
							if ($result === "SUCCESS" && $last_success_timestamp === "") {
								$last_success_timestamp = date('Y/m/d H:i',(int)($timestamp/1000));
							} else if ($result === "FAILURE" && $last_failure_timestamp === "") {
								$last_failure_timestamp = date('Y/m/d H:i',(int)($timestamp/1000));
							}
						}
					}
				
					$jobsArray[$job_name]['class'] = $_class;
					$jobsArray[$job_name]['url'] = $url;
					$jobsArray[$job_name]['color'] = $color;
					$jobsArray[$job_name]['path'] = str_replace ( $top_url, "" , $url );
					$jobsArray[$job_name]['last_success_timestamp'] = $last_success_timestamp;
					$jobsArray[$job_name]['last_failure_timestamp'] = $last_failure_timestamp;
					$jobsArray[$job_name]['last_id'] = $last_id;
				}

			} else {
				
				$errMsg .= "can not get info $all_url \n";
				echo $api_url . " has no response  ";
				exit;
			}
		} catch (Exception $e) {
			echo $e->getMessage;
			exit;
		}


	}

	$smarty->assign('summaryArray', $summaryArray);
	$smarty->assign('jobsArray', $jobsArray);
	$smarty->display('jenkins_tree_oss.html');


?>
	