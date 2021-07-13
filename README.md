Modified version of OSTicket version 1.15.2

Installation-
1. OsTicket v 1.15.2
2. Mysql 5.5+
3. PHP 7.2.34 [Php 7.3+ will not be supported (eg. the export functionality will break)]


Changes-
* Added Auto-Assignment to Agent Only[which belongs to the concerned Department] via cron-job or Task Scheduler
* Added Ability of Ticket Close[Close State not Close status] and Reopening to Helpdesk User
* Fixed the transfer of Department when assigning a ticket to another agent of not same Department.


UI Fixes-
* File is present in /osticket/include/client/view.inc.php
Goto Line 196-198 for changing the text inside the buttons ====> "Post REPLY"    "Reset"     "CANCEL"

* Open /include/client/open.inc.phpOn line 58 change "Help Topic" to whatever you want.

* For editing the core source files focus on changing the page width etc.Make sure that you back up your site before you muck around. If you do it on the staff side it will affect all Agents. If you do it on the User side then it will affect all users.

The User side is: /assets/default/css/theme.css

The staff side is /scp/css/scp.css
Line 128 modify 940px by 90%
Line 517 added "width: 100%;"
Line 729 added "width: 100%;"
Line 1130 added "width: 100%;"
In include/staff/queue-tickets.tmpl.php
Line 234 modify 940 to 100%
Line 317 modify 940 to 100%
Line 516 modify 940 to 100%
Line 616 modify 940 to 100%
file is \js\redactor-osticket.js and you change line 378:
from 'maxWidth': el.hasClass('fullscreen') ? '950px' : false, to 'maxWidth': el.hasClass('fullscreen') ? '100%' : false,

* Edited class.ticket.php (function assign()) for making Auto-Change-Department work when assigning to agent of different department

		error_log(print_r("Performing Transfer of Department", TRUE));
        if($assignee instanceof Staff && $this->getDeptId() !== $assignee->getDeptId())
            {   
                error_log(print_r("Inside if statement", TRUE));
                $form = new TransferForm();        
                $form->_dept = $assignee->getDept();
                $this->getThread()->addNote(array('note' => 'Ticket transferred as assigned staff in different department.'));                
                $this->transfer($form,$errors,false);
            }
* Edited tickets.php for updating resolution status by client/helpdesk user
* Edited /osticket/include/client/view.inc.php for enabling user to Reopen/Close Ticket.
* Edited scp/tickets.php for changing SLA based on Status change. [Add after Reply Successfully posted thing]

				require(INCLUDE_DIR.'ost-config.php');
                $type=DBTYPE;
                $host=DBHOST;
                $dname=DBNAME;
                $user=DBUSER;
                $pass=DBPASS;
                //echo($type.':host='.$host.';dbname='.$dname);
                $conOst = new PDO($type.':host='.$host.';dbname='.$dname,$user,$pass);
                $ticket_status=$ticket->getStatusId();
                $sla_query=null;
                switch($ticket_status)
                {
                    case 10:
                        if($ticket->getSLAId()!=4)
                            $sla_query="UPDATE ost_ticket SET sla_id=4 WHERE ticket_id=:ticketid";
                    break;
                    case 6:
                        if($ticket->getSLAId()!=5)
                            $sla_query="UPDATE ost_ticket SET sla_id=5 WHERE ticket_id=:ticketid";
                    break;
                    case 9:
                        if($ticket->getSLAId()!=6)
                            $sla_query="UPDATE ost_ticket SET sla_id=6 WHERE ticket_id=:ticketid";
                    break;
                    default:
                        $sla_query=null;
                }
                if($sla_query)
                 {
                    $stmtOst = $conOst->prepare($sla_query);
                    $stmtOst->execute(array('ticketid' => (int)$ticket->getId()));
                 }
                 $conOst=null;            

* Edited /include/staff/ticket-view.inc.php for Removing Transfer Icon altogether [Line 109]