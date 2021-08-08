<?php
  /**
   * Find user with less tickets and assing the new one
   */
  require('client.inc.php');
  require(INCLUDE_DIR.'ost-config.php');

  function assignTicketToBoredStaff($conOst, $ticket, $deptid,$userid) {
    $sqlOkm='';
    //error_log(print_r("Dept is is-> ".$deptid,TRUE));
    //error_log(print_r("Userid is-> ".$userid,TRUE));
    
    // If condition only triggered for EMS/NMS type of Department
    if($deptid==14)
    {
         $fetch_proj = "SELECT value FROM ost_list_items WHERE id IN (SELECT projectlinked FROM ost_user__cdata WHERE user_id=:userid)";
         $stmt_fetch_proj = $conOst->prepare($fetch_proj);
         $stmt_fetch_proj->execute(array('userid' => $userid));
         if ($rs_fetch_proj = $stmt_fetch_proj->fetch()) {
         //switch condition to match Project name and Assign tickets to specific project based SME 
         switch($rs_fetch_proj['value'])
         {
            case "Pune Smart City":
            //manually selecting userid based on Project
            $sqlOkm = "SELECT id,dept_id,mail FROM okm_assigner WHERE active=1 AND dept_id=".$deptid." AND id=41 GROUP BY tickets HAVING tickets=MIN(tickets) LIMIT 1";
            break;
            default:
            $sqlOkm = "SELECT id,dept_id,mail FROM okm_assigner WHERE active=1 AND dept_id=".$deptid." GROUP BY tickets HAVING tickets=MIN(tickets) LIMIT 1";
         }
       }
       else
            $sqlOkm = "SELECT id,dept_id,mail FROM okm_assigner WHERE active=1 AND dept_id=".$deptid." GROUP BY tickets HAVING tickets=MIN(tickets) LIMIT 1";

    }
    else
    $sqlOkm = "SELECT id,dept_id,mail FROM okm_assigner WHERE active=1 AND dept_id=".$deptid." GROUP BY tickets HAVING tickets=MIN(tickets) LIMIT 1";

    $stmtOkm = $conOst->prepare($sqlOkm);
    $stmtOkm->execute();

    if ($rsOkm = $stmtOkm->fetch()) {
      $staffId = $rsOkm['id'];
      $staffMail = $rsOkm['mail'];
      //echo "Assign ticket ".$ticket." to staff ".$staffId." (".$staffMail.")\n";

      // Assign the ticket
      $sqlOst = "UPDATE ost_ticket SET staff_id=:staff WHERE ticket_id=:ticket";
      $stmtOst = $conOst->prepare($sqlOst);
      $stmtOst->execute(array('staff' => $staffId, 'ticket' => $ticket));

      // Increase ticket count
      $sqlOkm = "UPDATE okm_assigner SET tickets=tickets+1 WHERE id=:id";
      $stmtOkm = $conOst->prepare($sqlOkm);
      $stmtOkm->execute(array('id' => $staffId));

      /*
      // Send mail
      $from = "<contact@yourdomain.com>"; $to = $staffMail;
      $host = "mail.yourdomain.com"; $username = "mail-user@yourdomain.com"; $password = "mail-password";
      $headers['From'] = $from; $headers['To'] = $to;
      $headers['Subject'] = 'New ticket assigned';
      $headers['MIME-Version'] = '1.0'; $headers['Content-Type'] = 'text/html; charset=UTF-8';
      $smtp = Mail::factory('smtp', array ('host' => $host, 'auth' => true, 'username' => $username, 'password' => $password));
      $body = '<html><body>';
      $body .= 'New ticket assigned';
      $body .= ' <a href="http://support.yourdomain.com/scp/tickets.php?id='.$ticket.'">Ticket #'.$ticket.'</a>';
      $body .= '</body></html>';
      $mail = $smtp->send($to, $headers, $body);

      if (PEAR::isError($mail)) {
        die("Mail send error: ".$mail->getMessage());
      }
      */
    } else {
      die("Query did no return any staff member");
    }
    
  }

  /**
   * Assinging a ticket to an user
   */
  function assignTicket($con, $ticket, $staff) {
    $sql = "UPDATE ost_ticket SET staff_id=:staff WHERE ticket_id=:ticket";
    $stmt = $con->prepare($sql);
    $stmt->execute(array('staff' => $staff, 'ticket' => $ticket));
  }

  /**
   * Obtain unassigned new created tickets output is array of ids and array of dept_ids
   */
  function getUnassignedTickets($con) {

    $sql = "SELECT ticket_id, number, dept_id, staff_id, status_id, user_id FROM ost_ticket WHERE status_id=1 and staff_id=0";
    $ret = array();
    $ret2 = array();
    $stmt = $con->prepare($sql);
    $stmt->execute();

    while ($rs = $stmt->fetch()) {
      $ret[] = $rs['ticket_id'];
      $ret2[] = $rs['dept_id'];
      $ret3[] = $rs['user_id'];  
    }
    return [$ret,$ret2,$ret3];
  }

  /**
    Update okm_assigner table if any new users are added to the system with only Roleid=2[Expanded Access]
    If Roleid=3[Full Access] the algorithm will not take into consideration those users.
    Taken into the consideration, the newly created users are mapped accordingly to the Departments
  */
  function updateOKM_Assigner($con){
    $con2=$con;
    $sql = "SELECT staff_id,dept_id,role_id,email FROM ost_staff WHERE role_id=2 and isactive=1";
    $rs= array();
    $stmt = $con->prepare($sql);
    $stmt->execute();
    while ($rss = $stmt->fetch()) {
      $rs[] = $rss;
    }
    for ($x = 0; $x < sizeof($rs); $x++) {
    //echo $rs[$x]['email'];
    //echo "<br>";
    $ins_sql = "INSERT IGNORE INTO okm_assigner(id,dept_id,mail,active) VALUES (".$rs[$x]['staff_id'].",".$rs[$x]['dept_id'].",'".$rs[$x]['email']."',1)";
    $stmt = $con2->prepare($ins_sql);
    $stmt->execute();
    }   

  }

  /* Runner function*/
  $type=DBTYPE;$host=DBHOST;$dname=DBNAME;$user=DBUSER;$pass=DBPASS;
  $conOst = new PDO($type.':host='.$host.';dbname='.$dname,$user,$pass);

  //error_log($conOst);
  updateOKM_Assigner($conOst);
  $received_array=getUnassignedTickets($conOst);

  $codes=$received_array[0];
  $deptnames=$received_array[1];
  $userids=$received_array[2];

  foreach( $codes as $index => $code ) {
   $staff = assignTicketToBoredStaff($conOst, $code, $deptnames[$index],$userids[$index]);
   //error_log(print_r($code .'-'.$deptnames[$index].'-'.$userids[$index],TRUE));
}

?>
