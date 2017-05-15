# Phieldbook v1.1.0
## A basic PHP client for the Fieldbook API

Fieldbook is a cloud-based database with a spreadsheet-like interface. This client for the Fieldbook API is not an "official" Fieldbook client, and it is not affiliated with Fieldbook, Inc.

* [Fieldbook website](http://www.fieldbook.com)
* [Fieldbook API documentation](https://github.com/fieldbook/api-docs)

## Initializing

```php
$args = array(
  'api_key'    => 'key-1',
  'api_secret' => '2xxx_xxxxxxxx-xxxxx2'
);
$fb = new Phieldbook($args);
```

The Phieldbook object requires one parameter on instantiation. The parameter is an arguments array where the property names are keys and property settings are values. It is recommend to include the `api_key` and `api_secret` properties in the array. All other properties, listed below, can also be included in the array. 

## Properties

The public properties for the Phieldbook object include the following:

Property | Description | Context
-------- | ----------- | -------
api_key | The Fieldbook API Key Username | Always Required
api_secret | The Fieldbook API Key Password | Always Required
book_id | The unique identifier for the book | All non-meta methods, and `book_meta()`
sheet_id | The unique identifier for the sheet | `field_meta()`
sheet_title | The name of the sheet | All non-meta methods
record_id | The ID number of a record | (required) `update()`, `delete()`; (optional) `get()`
limit | Limit the number of records returned - Use in combination with `offset` for pagination | (optional) `get()` and `search()`
offset | Number of records to skip | (optional) `get()` and `search()`
include | An Array of fields to include | (optional) `get()` and `search()`
exclude | An Array of fields to exclude | (optional) `get()` and `search()`

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

## What's New in 1.1.0

* **Breaking Change:** Now supports multiple calls in a single instance.
* **Breaking Change:** Renamed `$sheet_id` to `$sheet_title` for consistency with the API
* Support for book, sheet, and field metadata.
* Marginally better documentation. Meh.