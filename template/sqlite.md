# SQLite

[SQLite](https://sqlite.org/) is a lightweight Relational Database (RDB) engine.

### What is Relational Database (RDB)?

- RDB can store/manage data.
- RDB stores data as table(s).
 - Each row represents an instance of entity.
 - Each column (field) represents attributes of the instance.
 
### Example: `countries` table 

| id | name | capital | ccTLD | area (1000km^2) |
| ----- | ----- | ----- | ----- | ----- |
| 1 | Malaysia | Kuala Lumpur | my | 331 | 
| 2 | Japan | Tokyo | jp | 377 | 
| 3 | United States | Washington, D.C. | us | 9525 |
| 4 | United Kingdom | London | uk | 242 |
| 5 | France | Paris | fr | 551 |
| 6 | Germany | Berlin | de | 357 |

- ccTLD: country code top level domain
- The `countries` table consists of 5 fields (id, name, capital, ccTLD, area)
- Each field has a type: (id,area=INTEGER, name, capital, ccTLD=TEXT)

note: __ID__ field is usually inserted as   __primary key__ (should be unique among the rows) 

### Manipulation of table data 

Basically the data manipulation is done by __row__ unit.

- Insert row : add new instance
- Update row : replace several cell by specific rowID
- Delete row : delete the instance by specific rowID
- Read row(s)

hint: CRUD (Create, Read, Update, Delete) are four basic functions.

### SQL (Structured Query Language)

- The only way of manipulating table/DB is __SQL__
 - Sending the SQL statement to the DB (SQLite) will add/modify/delete the data
 - Getting the table data is also executed by `select` statement
 
| Manupulation | example SQL |
| ----- | ----- |
| Insert | insert into `countries` ( name, capital ) values ( 'China' , 'Beijing' ) |
| Update | update `countries` set __area = 9752__ where __id = 7__ |
| Delete | delete from `countries` where __id = 7__ |
| Select (all) | select * from `countries`  |
| Select (fields selected) | select name, area from `countries` where __area < 1000__ |
| Select (with condition by `where` clause) | select * from `countries` where __area < 1000__ |


### phpLiteAdmin

- [phpLiteAdmin](https://www.phpliteadmin.org/) is a Web-based SQLiteDB manager
- Sweetie incorporate phpLiteAdmin 
 - Open as new window : [__phpLiteAdmin__](phpliteadmin/phpliteadmin.php)
 - password is required (same as Sweetie Login password)

### Using SQLite DB

- A SQLite Database can store __multiple Tables__.
 - A SQLite Database is stored __as a file__.
 - We expect the Database file name as `*.db` (to prohibit unexpected guest access and downloading)
 
note: __DB__ > __Table__ > __field__
Table name is often plural (ex. `countries`) because the table stores multiple instances.
DB file name and field name are singular (ex. `country.db` and `capital`).

