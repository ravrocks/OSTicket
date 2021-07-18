<?php

/*
Create lists using Admin Panel, then come here and replicate code based on Helptopic
where list_id is
12 for VMS/Surveillance
13 for ATCS/ITMS
14 for Smart Elements
15 for EGOV
16 for rest
*/
      require('secure.inc.php');
      require(INCLUDE_DIR.'ost-config.php');
      $type=DBTYPE;$host=DBHOST;$dname=DBNAME;$user=DBUSER;$pass=DBPASS;
      $conOst = new PDO($type.':host='.$host.';dbname='.$dname,$user,$pass);
        if(isset($_POST['help-topic']) && !empty($_POST['help-topic'])) 
        {
         $tname=$_POST['help-topic'];
            if($tname=="Surveillance/VMS Issue")
            {

               $sql_f_subd = "SELECT * FROM ost_list_items WHERE list_id=12";
               $stmtz = $conOst->prepare($sql_f_subd);
               $stmtz->execute();
               echo '<option value="0">Select Subdomain</option>'; 
               while($row = $stmtz->fetch())
                  echo '<option value="'.$row['id'].'">'.$row['value'].'</option>';

            }
            else if($tname=="ITMS/ATCS Issue")
            {

               $sql_f_subd = "SELECT * FROM ost_list_items WHERE list_id=13";
               $stmtz = $conOst->prepare($sql_f_subd);
               $stmtz->execute();
               echo '<option value="0">Select Subdomain</option>'; 
               while($row = $stmtz->fetch())
                  echo '<option value="'.$row['id'].'">'.$row['value'].'</option>';
            }
            else if($tname=="eGov / DMS / ERP")
            {
               $sql_f_subd = "SELECT * FROM ost_list_items WHERE list_id=15";
               $stmtz = $conOst->prepare($sql_f_subd);
               $stmtz->execute();
               echo '<option value="0">Select Subdomain</option>'; 
               while($row = $stmtz->fetch())
                  echo '<option value="'.$row['id'].'">'.$row['value'].'</option>';
            }
            else if($tname=="Smart Element Issues")
            {
               $sql_f_subd = "SELECT * FROM ost_list_items WHERE list_id=14";
               $stmtz = $conOst->prepare($sql_f_subd);
               $stmtz->execute();
               echo '<option value="0">Select Subdomain</option>'; 
               while($row = $stmtz->fetch())
                  echo '<option value="'.$row['id'].'">'.$row['value'].'</option>';
            }
            else if($tname=="Network Issue")
            {
               $sql_f_subd = "SELECT * FROM ost_list_items WHERE list_id=16";
               $stmtz = $conOst->prepare($sql_f_subd);
               $stmtz->execute();
               echo '<option value="0">Select Subdomain</option>'; 
               while($row = $stmtz->fetch())
                  echo '<option value="'.$row['id'].'">'.$row['value'].'</option>';
            }
            else if($tname=="Server/Storage Issue")
            {
               $sql_f_subd = "SELECT * FROM ost_list_items WHERE list_id=17";
               $stmtz = $conOst->prepare($sql_f_subd);
               $stmtz->execute();
               echo '<option value="0">Select Subdomain</option>'; 
               while($row = $stmtz->fetch())
                  echo '<option value="'.$row['id'].'">'.$row['value'].'</option>';
            }
            else if($tname=="Database Issue")
            {
               $sql_f_subd = "SELECT * FROM ost_list_items WHERE list_id=18";
               $stmtz = $conOst->prepare($sql_f_subd);
               $stmtz->execute();
               echo '<option value="0">Select Subdomain</option>'; 
               while($row = $stmtz->fetch())
                  echo '<option value="'.$row['id'].'">'.$row['value'].'</option>';
            }
            else if($tname=="CCC Application Issue")
            {
               $sql_f_subd = "SELECT * FROM ost_list_items WHERE list_id=19";
               $stmtz = $conOst->prepare($sql_f_subd);
               $stmtz->execute();
               echo '<option value="0">Select Subdomain</option>'; 
               while($row = $stmtz->fetch())
                  echo '<option value="'.$row['id'].'">'.$row['value'].'</option>';
            }
            else
            {
               $sql_f_subd = "SELECT * FROM ost_list_items WHERE list_id=16";
               $stmtz = $conOst->prepare($sql_f_subd);
               $stmtz->execute();
               echo '<option value="0">Select Subdomain</option>'; 
               while($row = $stmtz->fetch())
                  echo '<option value="'.$row['id'].'">'.$row['value'].'</option>';
            }
      }
      $conOst=null;

?>