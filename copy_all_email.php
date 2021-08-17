<?php
  /**
   * Find user with less tickets and assing the new one
   */
  require('client.inc.php');
  require(INCLUDE_DIR.'ost-config.php');

  /* Runner function*/
  $type=DBTYPE;$host=DBHOST;$dname=DBNAME;$user=DBUSER;$pass=DBPASS;
  $conx = new PDO($type.':host='.$host.';dbname='.$dname,$user,$pass);

  $all_users="SELECT address from ost_user_email";
  $stmt1 = $conx->prepare($all_users);
  $stmt1->execute();

  $all_staff="SELECT email from ost_staff";
  $stmt2 = $conx->prepare($all_staff);
  $stmt2->execute();

  $copy_all=[];
  while ($rs = $stmt1->fetch()) {
      $copy_all[] = $rs['address']; 
    }

  while ($rs = $stmt2->fetch()) {
      $copy_all[] = $rs['email']; 
    }


  $empty_me="TRUNCATE TABLE okm_pwreset";
  $stmt_empty=$conx->prepare($empty_me);
  $stmt_empty->execute();


  $insert_emails="INSERT INTO okm_pwreset(email,counter) VALUES(:emailx,:countx)";
  $stmt_ins_email= $conx->prepare($insert_emails);
  foreach( $copy_all as $copy ) {
    $cinx=0;
    //error_log(print_r($copy,TRUE));
    $stmt_ins_email->execute(array(':emailx' => $copy,':countx'=>$cinx));
  }

  //error_log(print_r($copy_all,TRUE));
  $conx=null;
  //foreach( $codes as $index => $code ) {
   //$staff = assignTicketToBoredStaff($conOst, $code, $deptnames[$index],$userids[$index]);
   //error_log(print_r($code .'-'.$deptnames[$index].'-'.$userids[$index],TRUE));

?>
