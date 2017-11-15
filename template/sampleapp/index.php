<?php
require_once("_lib.php");
css();


heading("Sample Apps",1);
br(2);

show_link("Tweet Index","tweet_index.php", ["target"=>"_blank"]);
span("Guest can tweet!", ["style"=>"background: #cff;"] );
space(10);
show_link("Tweet Admin","tweet_admin.php", ["target"=>"_blank"]);
span("Need Sweetie Login", ["style"=>"background: #ffc;"]);
br(3);

show_link("Photo Index","photo_index.php", ["target"=>"_blank"]);
span("Guest cannot upload photo... :(", ["style"=>"background: #ccc;"] );
space(10);
show_link("Photo Admin","photo_admin.php", ["target"=>"_blank"]);
span("Need Sweetie Login to upload", ["style"=>"background: #ffc;"]);
br(3);

show_link("Shop Index","shop_index.php", ["target"=>"_blank"]);
span("Need User Login (ex. u1/u1)", ["style"=>"background: #cfc;"] );
space(10);
show_link("Shop Admin","shop_admin.php", ["target"=>"_blank"]);
span("Need Sweetie Login", ["style"=>"background: #ffc;"]);

br(3);

show_link("Enquete Index","enq_index.php", ["target"=>"_blank"]);
span("Need User Login (ex. u1/u1)", ["style"=>"background: #cfc;"] );
space(10);
show_link("Enquete Admin","enq_admin.php", ["target"=>"_blank"]);
span("Need Sweetie Login", ["style"=>"background: #ffc;"]);


