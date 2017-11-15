# Sweetie Framework

Sweetie Framework is a tiny web framework for quick development of prototype.

### Sample Apps

[Sample Apps](sampleapp/)

## Field name conventions (Schema rule)

- id is mandatory, primary key, autoincrement, integer
- field contains "pass" represents password.  The raw data is converted (hashed) when storing.
- dt (DATETIME) represents timestamp. if the data was empty, insert/update with current timestamp.
- (BLOB) field can contain images (or files). 
 - additional fields are expected: xxx_name (text) , xxx_size (integer) , xxx_type (text)
 - ex.  'image' BLOB NOT NULL, 'image_name' TEXT NOT NULL, 'image_type' TEXT NOT NULL, 'image_size' INTEGER NOT NULL
- `users` table for login must have the following fields
 - 'id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'name' TEXT NOT NULL, 'email' TEXT NOT NULL, 'hashpass' TEXT NOT NULL


## Usage / DB manipulation

### (0) Load library
```php
require_once("_lib.php"); // load library (functions)
```

### (1) open DB object
```php
$dbfn = "tweet.db"; // database filename
$db = dbopen($dbfn); // open DB object
```

### (2-1) execute SQL (no return value)
```php
$db->exec("create table IF NOT EXISTS 'tweets' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'mes' TEXT NOT NULL, 'dt' DATETIME NOT NULL )");
```
### (2-2) execute SQL (with return value)
```php
$ret = sql($db, "select * from tweets"); // sql(DBObject, SQL);
pr($ret); // print array recursively
```

### (3-1) get all table data as array
```php
$ret = tbl($db, "tweets"); // tbl(DBObject, tablename)
table($ret); // print array as table
```

###  (3-2) get and print in one function
```php
showtable($db, "tweets"); // showtable(DBObject, tablename)
```

###  (3-3) get and print with delete and edit link
```php
showtable_withdeledit($db, "tweets", $dbfn); // showtable_withdeledit(DBObject, tablename, database filename)
```

### (3-4) add table form using JQuery Javascript library
```php
jqaddform("tweets",$dbfn); // jqaddform(tablename, database filename)
```

### (3-5) get only one row by ID
```php
$row = getrow($db, "tweets", 3); // getrow(DBObject, tablename, ID)
pr($row); 
```

### (4-1) insert

sample1 (copy existing row)
```php
$row = getrow($db, "tweets", 3); // getrow(DBObject, tablename, ID)
pr($row); // confirm
insert($db, "tweets", $row); // $row['id'] is automatically dropped when insert
```

sample2 (insert from post data)
```php
if (isset($_POST['mes'])){
  sanitize($_POST); // reduce risks of SQL injection
  insert($db, "tweets", $_POST); 
}
```

### (4-2) update
```php
$row = getrow($db, "tweets", 3); // getrow(DBObject, tablename, ID)
$row['mes'] = "New Idea!"; // modify array data
pr($row); // confirm
update($db, "tweets", $row); // update rowdata by id = $row['id'] 
```

### (4-3) delete
```php
delete($db, "tweets", 3); // delete(DBObject, tablename, ID)
```

## Array utilities

### (1) create simple pair of array 
```php
$ret = tbl($db, "tweets"); // get all rows , fields are (id, mes, dt)
$id2dt = arytohash($row, "id", "dt"); // arytohash(Array, keyField, valueField)
pr($id2dt); 
```
note: returns new array

### (2) prepend / append string to each value in array
```php
$ret = tbl($db, "tweets"); // get all rows , fields are (id, mes, dt)
$id2dt = arytohash($ret, "id", "dt"); // arytohash(Array, keyField, valueField)
prependstrtohash($id2dt, "Tweeted at: ");
appendstrtohash($id2dt, " (in JST)");
pr($id2dt); 
```
warning: prependstrtohash and appendstrtohash modify the first argument

### (3) merge two array values
```php
$it = tbl($db,"items");
$idname = arytohash($it,"id","name");
$idprice = arytohash($it,"id","price");
$idleft = arytohash($it,"id","amount");
appendstrtohash($idname,"  (price: ");
mergehash($idname,$idprice);
appendstrtohash($idname,") (left: ");
mergehash($idname,$idleft);
appendstrtohash($idname,")");
```
![](img/16_1129_233622.png "16_1129_233622.png")

warning: mergehash modifies the first argument

### (4) insert column into array
```php
$ut = tbl($db,"photos");
aryinscol($ut, "<a href='imgdownload.php?id={\$id}&table=photos&field=image&size={\$image_size}'>DL</a>","link", 2);
// aryinscol(Array, string to be inserted, key (, position)
table($ut);
```
hint: position (integer) is optional
if position is not specified, the column is appended to the last column
if position is 0, the column is prepended to the first column

### (5) replace column data by applying function
```php
$ut = tbl($db,"users");
//arydelcol($ut,"hashpass");
arymapcol($ut,"hashpass",function($a){ return substr($a, 0, 20)."..." ; }); // truncate long string
```
note: hashpass of user table is too long, so truncated by substring function 


## HTML helpers

```php
br();  // <br>
br(4); // <br> x 4

space(5); //   x 5
nbsp(3);  //   x 3

title("Tweet WebApp"); // <title>Tweet WebApp</title>

heading("Heading 2", 2); // <h2>Heading 2</h2>

div(" inside div ", ["style"=>"font-size: large;"]); 
// <div style="font-size:large;"> inside div </div>

span(" inside span ", ["style"=>"background: #ffa;"]); 
// <span style="background: #ffa;"> inside span </span>

show_link("Click the link to open google", "http://google.com", ["target"=>"_blank"]); 
// show_link(Label, URL (, attributesArray) )

show_linkb("Click the button to open google", "http://google.com"); 
// show_linkb(Label, URL (, attributesArray) )

show_sqlite_admin_link();

showqrcode($fullurl); 

css();
jquery(); // for jqadd() and jqedit

```

## Form helpers

```php
form_start( ["style"=>"background: #ffc; border: 3px solid #cc9; padding: 10px;"] );

$ut = tbl($db,"users"); $ut = arytohash($ut,"id","name");
form_select("userid", $ut, "Select User : ","<br>"); // name, dataarray, label (,after)

$it = tbl($db,"items");
$it = arytohash($it,"id","name");
//form_select("itemid", $it, "Select Item : "); //  name, dataarray, label (,after)
form_radio("itemid", $it, "Select Item : ", "   ", "<br>"); //  name, array, label, between, after (, key of checked data) 

echo "Num : ";
form_input("num", ["type"=>"text", "size"=>10, "placeholder"=>"input num", "value"=>1]);

form_submit( ["value"=>"Buy"] );

form_end();
```

## Login function / Authentication

```php
require_login($dbfn, $table="users", $userfield="name", $passfield="hashpass"); 

require_sweetielogin(); 
```

## send email


```php
sendMail("miura@mns.kyutech.ac.jp" , // To: address
        "Test mail from my Web App", // Subject
        "Dear Guest User,\n\n{$_POST['body']}\n\n
        \n\n(Please ignore if you do not know why this email is arrived.)", // Body
        "miuramo@mns.kyutech.ac.jp", //From address
        "my Web App MailSystem"); //FromName
```