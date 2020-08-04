<?php
	
	$WEB_TIMEOUT = 1;

	// configulation
	$ini_array = parse_ini_file("conf_oss.ini",true);

	// curl setting for GET
	$context = stream_context_create(array(
	  'http' => array('ignore_errors' => true , 'method'=> 'GET' , 'timeout' => $WEB_TIMEOUT)
	));

	$selected_server_id = "none"; if (!empty($_POST["selected_server_id"])) {$selected_server_id = $_POST["selected_server_id"];}
	$selected_path = ""; if (!empty($_POST["selected_path"])) {$selected_path = $_POST["selected_path"];}
	$ableArray = ""; if (!empty($_POST["able"])) {$ableArray = $_POST["able"];}

	$API_USER = $ini_array["jenkins"]["API_USER"];
	$JENKINS_TOKEN = $ini_array["JENKINS_TOKEN"][$selected_server_id];

	if (empty($ableArray)) {
		$ableArray = array();
	}
	
	// Step1 get jenkins tree by API
	$jobsArray = array();
	$domain = $ini_array["JENKINS_DOMAIN"][$selected_server_id];
	$top_url = "http://" . $domain . "/";
	$api_url =  $top_url . $selected_path . "/api/json";
	try {
		$response = @file_get_contents($api_url , false, $context);
		if ($response !== "") {
			$result = json_decode($response,true);
			$jobs = $result['jobs'];
			foreach ($jobs as $hash) {
			
				$job_name = $hash['name'];
				$_class = $hash['_class'];
				
				if ($_class === "hudson.model.FreeStyleProject" || 
				    $_class === "org.jenkinsci.plugins.workflow.job.WorkflowJob") {
				
					$url = $hash['url'];
					$color = $hash['color'];
					
					$jobsArray[$job_name]['class'] = $_class;
					$jobsArray[$job_name]['url'] = $url;
					$jobsArray[$job_name]['color'] = $color;
					$jobsArray[$job_name]['path'] = str_replace ( $top_url, "" , $url );
				}
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

	$context = stream_context_create(array(
	  'http' => array('ignore_errors' => true , 'method'=> 'POST' , 'timeout' => $WEB_TIMEOUT)
	));

	// Step2 update able-disable 
	foreach ($jobsArray as $job_name => $hash) {

		$flag = in_array ($job_name , $ableArray, false);
		$color = $hash['color'];
		$url = $hash['url'];
		$project_status = "";
		if ($flag) {
			// should be able
			if ($color === "disabled") {
				$project_status = "enable";
			}
		} else {
			// should be disable
			if ($color !== "disabled") {
				$project_status = "disable";
			}
		}
		
		if ($project_status !== "") {
			// update status by API
			$api_url = "http://" . $API_USER . ":" . $JENKINS_TOKEN . "@" . str_replace ("http://" , "" , $url) . "/".$project_status;
			$result = file_get_contents($api_url, false, $context);
		}
	}

	// back to tree page
	$redirect_url = "jenkins_tree_oss.php?selected_server_id=" . $selected_server_id . "&selected_path=" . $selected_path ;
	
	header("Location: $redirect_url");

	exit;



?>
	