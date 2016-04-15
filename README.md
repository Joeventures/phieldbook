# Phieldbook
## A basic PHP Class for the Fieldbook API

This class offers basic CRUD capabilities for using the [Fieldbook](https://fieldbook.com/) API with PHP.

## Examples

Creating a new record:
```php
require_once 'phieldbook.php';

$fb_connect = array(
  'api_key' => 'key-1',
  'api_secret' => '2xxx_xxxxxxxx-xxxxx2',
  'book_id' => 'xxxxxxxxxxxxxxxxxxxxxxxx',
  'table' => 'goblins'
);
$fb = new PhieldBook($fb_connect);

$data = array(
  "first_name" => "Test",
  "last_name" => "Goblin",
  "email" => "test@goblin.com"
  );
$new_record = $fb->create($data);
var_dump($new_record);
```

Result:
```
array(6) {
  ["id"]=>
  int(7)
  ["record_url"]=>
  string(54) "https://fieldbook.com/records/xxxxxxxxxxxxxxxxxxxxxxxx"
  ["artist_id"]=>
  string(8) "Goblin 7"
  ["first_name"]=>
  string(4) "Test"
  ["last_name"]=>
  string(6) "Goblin"
  ["email"]=>
  string(15) "test@goblin.com"
}
```