<?php
     // Enter the status_id of Resolved and Closed by referring the ost_status table 

		require('secure.inc.php');

		require(INCLUDE_DIR.'ost-config.php');
        $type=DBTYPE;$host=DBHOST;$dname=DBNAME;$user=DBUSER;$pass=DBPASS;
         //echo($type.':host='.$host.';dbname='.$dname);
        $conOst = new PDO($type.':host='.$host.';dbname='.$dname,$user,$pass);
		
        if(isset($_POST['help-topic']) && !empty($_POST['help-topic'])) {
        	$tname=$_POST['help-topic'];
        	$variable="Success!";
            //error_log(print_r($tname,TRUE));
            if($tname=="Surveillance/VMS Issue")
            {

            	//delete code
            	$sqld = 'DELETE FROM ost_list_items '
                . 'WHERE id > :project_id';

        		$stmt = $conOst->prepare($sqld);

        		$stmt->execute([':project_id' => 100]);
            	/////////////////////////////

            	$array_text=array("Video Jittering","Unable to add channel on recorder","Login issue with Client Application","Recorder Server moving to failover intermittently ","Application Services Stop Automatically ","Video Jump issue in surveillance application","Not getting live feeding in Application Launcher","Steaming Blur/Ghost Video Issue in Control Applications","Video Footage Extraction","Video Player Hang / not working");
            	$array_extras=array(1,2,3,4,5,6,7,8,9,10);
            	$array_ids=array(121,122,123,124,125,126,127,128,129,130);
            	$properties='\'[]\'';
            	$insert_sql = array(); 
            	for($x = 0; $x < count($array_text); $x++) {
  					$insert_sql[]='('.$array_ids[$x].',11,1,\''.$array_text[$x].'\','.$array_extras[$x].',1,'.$properties.')';
				}
				
    			$vals=array();
    			for($x = 0; $x < count($insert_sql); $x++)
    				$vals = array_merge($vals, (array)$insert_sql[$x]);

    			$final_stmt="INSERT INTO ost_list_items(id,list_id,status,value,extra,sort,properties) VALUES ".implode(",",$vals)."";
    			//error_log(print_r($final_stmt,TRUE));
    			//error_log(print_r($vals,TRUE));	
				$final_stmt = $conOst->prepare($final_stmt);
				$final_stmt = $final_stmt->execute();

            }
            else if($tname=="ITMS/ATCS Issue")
            {

            	//delete code
            	$sqld = 'DELETE FROM ost_list_items '
                . 'WHERE id > :project_id';

        		$stmt = $conOst->prepare($sqld);

        		$stmt->execute([':project_id' => 100]);
        		/////////////////////////////////////////////////////////

            	$array_text=array("ATCS Junctions are not visible at application",
"Phase Timing Increase/decrease",
"Vehicle data is not coming from the Vehicle detector",
"Signal Junction is live but unable to push any data",
"Junctions Sync / Green Corridor or Hurry Call Configuration",
"Evidence Time stamp is showing wrong");
            	$array_extras=array(1,2,3,4,5,6);
            	$array_ids=array(221,222,223,224,225,226);
            	$properties='\'[]\'';
            	$insert_sql = array(); 
            	for($x = 0; $x < count($array_text); $x++) {
  					$insert_sql[]='('.$array_ids[$x].',11,1,\''.$array_text[$x].'\','.$array_extras[$x].',1,'.$properties.')';
				}
				
    			$vals=array();
    			for($x = 0; $x < count($insert_sql); $x++)
    				$vals = array_merge($vals, (array)$insert_sql[$x]);

    			$final_stmt="INSERT INTO ost_list_items(id,list_id,status,value,extra,sort,properties) VALUES ".implode(",",$vals)."";
    			//error_log(print_r($final_stmt,TRUE));
    			//error_log(print_r($vals,TRUE));	
				$final_stmt = $conOst->prepare($final_stmt);
				$final_stmt = $final_stmt->execute();
            }
        	echo json_encode(array("imp"=>$variable));
    	}
    	else
    	{
    		$variable="Empty fields";
    		echo json_encode(array("imp"=>$variable));
    	}
    	$conOst=null;
?>