<?php
require_once("_lib.php");
css();

if (isset($_POST['to'])){
  sanitize($_POST);
  if (filter_var($_POST['to'], FILTER_VALIDATE_EMAIL)) { //check if the address is valid
    sendMail($_POST['to'] , // TO
             "Test mail from my Web App", // Subject
             "Dear Guest User,
       \n\n{$_POST['body']}\n\n\n\n(Please ignore if you do not know why this email is arrived.)", // Body
             "miuramo@mns.kyutech.ac.jp", //From
             "my Web App MailSystem"); //FromName
    
    heading("Sent Email to {$_POST['to']}!!",1); // show sent message
  } else {
    heading("Error: Address (To:) was not valid !!",1);
  }
}

heading("Email Send Test");

// HTML Form
form_start( ["style"=>"background: #cfc; border: 3px solid #9c9; padding: 10px;"] );
echo "To: ";
form_input("to", ["type"=>"text", "size"=>40, "placeholder"=>"input your VALID email address " /*, "value"=>"miura@moto.qee.jp" */  ]);
br(2);
echo "Body: ";
form_input("body", ["type"=>"textarea", "cols"=>80, "rows"=>10,  "value"=>"Write message\n\nHere!!"]);
br(2);
form_submit( ["value"=>"Send Email"] );
form_end();

show_linkb("Reload",$fullurl);