# PHP (PHP: Hypertext Preprocessor)

PHP is a programming language for authoring __dynamic__ webpage.

Create `test.php` and put the following lines.

```php
<?php
echo date(DATE_RSS);
?>
```

The above code outputs such as 
```
Thu, 1 Dec 2016 10:17:03 +0900
```

because `date()` __function__ returns current date/time as a `string`.

`echo` outputs the `string`.

note: You can write any HTML syntax in `*.php` file.
 The PHP part `<?php ... ?>` is preprocessed and replaced before outputting the file content.

### output text (echo function)

```php
<?php
echo date(DATE_RSS);
echo "<br>";
echo rand(1, 100); // returns a 1~100 integer
?>
```

Text surrounded by double-quotes `"..."` generates a fixed `string`.

`rand(1,100)` returns integer value from 1 to 100 __randomly__.

parameters specified in `(` `)` are called __arguments__.


### variable 

```php
<?php
$date = date(DATE_RSS);
echo strtoupper($date); // convert string to upper case
  
echo "<br>";

$num = rand(1, 100); // returns a 1~100 integer
echo $num;

echo "<br>";

echo "The square of {$num} is ". ($num*$num) ;

?>
```
note: __A variable can stock a value (integer or string).__

`"{$num}"` embeds the value of `$num` at that position inside the string.

`.` (dot) concatenates two values, and generates a string.

warning: The values stored in the variables are valid within an URL request (The lifetime is very short!). 
Therefore, the values cannot be used for another requests/connections.
To save the values, the data should be stored in a file on the web server.

### array

```php
$capitals = array("Japan"=>"Tokyo", 
                  "Malaysia"=>"Kuala Lumpur",
                  "United States"=>"Washington, D.C.",
                  "United Kingdom"=>"London",
                  "France"=>"Paris",
                  "Germany"=>"Berlin" );

foreach($capitals as $key=>$value){ // pick up a pair and bind to $key and $value
  echo "[{$key}] = {$value} <br>";
}

echo "<br>";

$city2country = array_flip($capitals); // swap key and value for each pair

foreach($city2country as $key=>$value){ // pick up a pair and bind to $key and $value
  echo "[{$key}] = {$value} <br>";
}
```
output is as follows.

![](img/16_1129_124930.png "16_1129_124930.png")


hint: __Array stocks pairs of values. __
A pair consists of __KEY__ and __VALUE__. 
The __VALUE__ is associated with the __KEY__.

`$ary = array( "Key1"=>"Value1" , "Key2"=>"Value2" )` generates __an array__ which stocks two pairs of data. (or, multiple values with __Keys__)

`$ary["Key1"]` represents `Value1`, and  `$ary["Key2"]` represents `Value2`

Assignment statement like `$ary["Key3"] = "Value3";` adds the 3rd value as (Key,Value)=(`Key3`,`Value3`).

Assignment statement __with same exist key__ like `$ary["Key1"] = "NewValue";` overwrites the value associated with the `Key1`.

To get a value, use `$ary[ KEY ]`.

To get all values with keys, use `foreach` loop statement. (shown in the above example)


# References

- [PHP Manual](https://secure.php.net/manual/en/index.php)
 - [Variable](https://secure.php.net/manual/en/language.variables.basics.php)
 - [Array](https://secure.php.net/manual/en/language.types.array.php)
