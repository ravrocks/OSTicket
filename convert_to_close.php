<?php
     // Enter the status_id of Resolved and Closed by referring the ost_status table 

		$closed_status=3;
		$resolved_status=9;
		$ticket=null;

		$conOst = new PDO('mysql:host=localhost;dbname=osTicket', 'root', 'ravrocks$321');
		
        if(isset($_POST['ticketid']) && !empty($_POST['ticketid'])) {
        	$ticket_id=$_POST['ticketid'];
        	$variable="Success!";
            //echo json_encode(array("imp"=>$variable)); //return response
            require('secure.inc.php');
            require_once(INCLUDE_DIR.'class.ticket.php');
			require_once(INCLUDE_DIR.'class.json.php');
        	
        	if($_POST['ticketid']) {
    			if (!($ticket = Ticket::lookup($_POST['ticketid']))) {
        		$errors['err']=__('Unknown or invalid ticket ID.');
    			} elseif(!$ticket->checkUserAccess($thisclient)) {
        		$errors['err']=__('Unknown or invalid ticket ID.'); //Using generic message on purpose!
        		$ticket=null;
    			}
			}
        	
        	//time according to IST
                        $datetime = new DateTime();
                        $timezone = new DateTimeZone('Asia/Calcutta');
                        $datetime->setTimezone($timezone);
                        $temp_time = $datetime->format('Y-m-d H:i:s');

    		###########################################################
    		// Code for changing automatically changing status
            
            $sqlOst = "UPDATE ost_ticket SET status_id=:closed_status,closed=:closed_time,updated=:updated_time WHERE ticket_id=:ticket and status_id=:resolved_status";
      		$stmtOst = $conOst->prepare($sqlOst);
      		$stmtOst->execute(array('closed_status' => (int)$closed_status,'closed_time'=> $temp_time,'updated_time'=> $temp_time ,'ticket' => (int)$ticket_id, 'resolved_status' => (int)$resolved_status));

      		############################################################
    		#########################update thread_event table###################################################
    					$temp_eventid=2; //for closure of tickets
                        $temp_stat=$ticket->getStatus();
                        $temp_id=$ticket->getId();
                        $temp_Staffid=$ticket->getStaffId();
                        $temp_teamid=$ticket->getTeamId();
                        $temp_deptid=$ticket->getDeptId();
                        $temp_topicid=$ticket->getTopicId();
                        $temp_owner=$ticket->getOwner();
                        $temp_ownid=$ticket->getOwnerId();
                        
                        ////////////////////////
                        $temp_data='{"status":'.$closed_status.'}';
                        $temp_ttype='T';
                        $temp_uid_type='M';

                        $insert_thread="INSERT INTO ost_thread_event(thread_id,thread_type,event_id,staff_id,team_id,dept_id,topic_id,data,username,uid,uid_type,timestamp) VALUES (:thread_id,:thread_type,:event_id,:staff_id,:team_id,:dept_id,:topic_id,:data,:username,:uid,:uid_type,:timestamp_now)";
                        $insertOst = $conOst->prepare($insert_thread);
                        $insertOst->execute(array('thread_id' => (int)$temp_id,'thread_type' => $temp_ttype,'event_id' => (int)$temp_eventid,'staff_id' => $temp_Staffid,'team_id' => (int)$temp_teamid,'dept_id' => (int)$temp_deptid,'topic_id' => (int)$temp_topicid,'data' => $temp_data,'username' => $temp_owner,'uid' => (int)$temp_ownid,'uid_type'=> $temp_uid_type,'timestamp_now' => $temp_time));

                        ###################################################################################
      		
      		$testvar=($ticket->getDeptId());
      		echo json_encode(array("imp"=>$testvar));
      		
      		    // Cleanup drafts for the ticket. If not closed, only clean
                
				$tform = TicketForm::objects()->one()->getForm();
				$messageField = $tform->getField('message');
				$attachments = $messageField->getWidget()->getAttachments();


                // for this staff. Else clean all drafts for the ticket.
                Draft::deleteForNamespace('ticket.client.' . $ticket->getId());
                // Drop attachments
                $attachments->reset();
                $attachments->getForm()->setSource(array());

            //Clean connection
      		$conOst=null;

    	}
    	else
    	{
    		$variable="Empty fields";
    		echo json_encode(array("imp"=>$variable));
    	}
    	$conOst=null;
?>