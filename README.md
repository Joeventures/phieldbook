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

## What's New in 1.1.0

* **Breaking Change:** Now supports multiple calls in a single instance.
* **Breaking Change:** Renamed `$sheet_id` to `$sheet_title` for consistency with the API
* Support for book, sheet, and field metadata.
* Marginally better documentation.

## Methods

### get

Returns all records in a book. Or, set the following properties to return a subset of records:

* `limit` (integer): Limits the number of records returned
* `offset` (integer): Skips the first number of records before returning anything
* `record_id` (integer): If set, `get()` returns only the specified record (`limit` and `offset` properties should not be set at the same time as `record_id`)
* `include` (array of strings): Include only the fields listed in the array
* `exclude` (array of strings): Include only the fields not listed in the array

**Example: Get All Records**

```php
$fb_keys = array(
  'api_key' => 'key-1',
  'api_secret' => '2xxx_xxxxxxxx-xxxxx2',
);
$fb = new PhieldBook($fb_keys);

$fb->book_id = 'xxxxxxxxxxxxxxxxxxxxxxxx';
$fb->sheet_title = 'donations'

$all_records = $fb->get();
var_dump($all_records);
```

Result:
```
array(22) {
  [0]=>
  array(5) {
    ["id"]=>
    int(1)
    ["record_url"]=>
    string(54) "https://fieldbook.com/records/xxxxxxxxxxxxxxxxxxxxxxxx"
    ["date"]=>
    string(10) "2014-01-12"
    ["amount"]=>
    int(100)
    ["donor"]=>
    array(1) {
      [0]=>
      array(2) {
        ["id"]=>
        int(1)
        ["name"]=>
        string(11) "Loren Ipswitch"
      }
    }
  }
  ...
}
```

**Example: Paginate**
```php
$fb_keys = array(
  'api_key' => 'key-1',
  'api_secret' => '2xxx_xxxxxxxx-xxxxx2',
);
$fb = new PhieldBook($fb_keys);

$fb->book_id = 'xxxxxxxxxxxxxxxxxxxxxxxx';
$fb->sheet_title = 'donations'
$fb->limit = 10;
$fb->offset = 20;

$all_records = $fb->get();
var_dump($all_records);
```

Result:
```
array(3) {
  ["count"]=>
  int(22)
  ["offset"]=>
  int(20)
  ["items"]=>
  array(2) {
    [0]=>
    array(5) {
      ["id"]=>
      int(21)
      ["record_url"]=>
      string(54) "https://fieldbook.com/records/xxxxxxxxxxxxxxxxxxxxxxxx"
      ["date"]=>
      string(10) "2016-12-25"
      ["amount"]=>
      int(500)
      ["donor"]=>
      array(1) {
        [0]=>
        array(2) {
          ["id"]=>
          int(2)
          ["name"]=>
          string(10) "Mel Torme"
        }
      }
    }
    ...
  }
}
```

### search

Returns a set of records matching any search criteria specified in the parameters. Note: a query on a linked field is matched against the display string of the linked cells.

**Example**

```php
$fb_keys = array(
  'api_key' => 'key-1',
  'api_secret' => '2xxx_xxxxxxxx-xxxxx2',
);
$fb = new PhieldBook($fb_keys);

$fb->book_id = 'xxxxxxxxxxxxxxxxxxxxxxxx';
$fb->table = 'donations'

$query = array(
  'amount' => 100
);
$result_set = $fb->search($query);
var_dump($result_set);
```

Result:
```
array(6) {
  [0]=>
  array(5) {
    ["id"]=>
    int(1)
    ["record_url"]=>
    string(54) "https://fieldbook.com/records/xxxxxxxxxxxxxxxxxxxxxxxx"
    ["date"]=>
    string(10) "2014-01-12"
    ["amount"]=>
    int(100)
    ["donor"]=>
    array(1) {
      [0]=>
      array(2) {
        ["id"]=>
        int(1)
        ["name"]=>
        string(11) "Loren Ipswitch"
      }
    }
  }
  ...
}
```

### create

Creates a new record and returns the result.

**Example**

```php
$fb_keys = array(
  'api_key' => 'key-1',
  'api_secret' => '2xxx_xxxxxxxx-xxxxx2',
);
$fb = new PhieldBook($fb_keys);

$fb->book_id = 'xxxxxxxxxxxxxxxxxxxxxxxx';
$fb->table = 'donations'

$data = array(
  'donor' => 'Loren Ipswitch',
  'date' => '2016-12-02',
  'amount' => 100
);
$new_record = $fb->create($data);
var_dump($new_record);
```

Result:
```
array(5) {
  ["id"]=>
  int(23)
  ["record_url"]=>
  string(54) "https://fieldbook.com/records/xxxxxxxxxxxxxxxxxxxxxxxx"
  ["date"]=>
  string(10) "2016-12-02"
  ["amount"]=>
  int(100)
  ["donor"]=>
  array(1) {
    [0]=>
    array(2) {
      ["id"]=>
      int(1)
      ["name"]=>
      string(11) "Loren Ipswitch"
    }
  }
}
```

### update

Update an existing record. Ensure the `record_id` property is set.

**Example**

```php
$fb_keys = array(
  'api_key' => 'key-1',
  'api_secret' => '2xxx_xxxxxxxx-xxxxx2',
);
$fb = new PhieldBook($fb_keys);

$fb->book_id = 'xxxxxxxxxxxxxxxxxxxxxxxx';
$fb->table = 'donations'
$fb->record_id = 23;

$data = array(
  'amount' => 200
);
$new_record = $fb->create($data);
var_dump($new_record);
```

Result:
```
array(5) {
  ["id"]=>
  int(23)
  ["record_url"]=>
  string(54) "https://fieldbook.com/records/xxxxxxxxxxxxxxxxxxxxxxxx"
  ["date"]=>
  string(10) "2016-12-02"
  ["amount"]=>
  int(200)
  ["donor"]=>
  array(1) {
    [0]=>
    array(2) {
      ["id"]=>
      int(1)
      ["name"]=>
      string(11) "Loren Ipswitch"
    }
  }
}
```

### delete

Delete an existing record.

Update an existing record. Ensure the `record_id` property is set.

**Example**

```php
$fb_keys = array(
  'api_key' => 'key-1',
  'api_secret' => '2xxx_xxxxxxxx-xxxxx2',
);
$fb = new PhieldBook($fb_keys);

$fb->book_id = 'xxxxxxxxxxxxxxxxxxxxxxxx';
$fb->table = 'donations'
$fb->record_id = 23;

$new_record = $fb->delete();
var_dump($new_record);
```

Result:
```
int(204)
```

### book_meta

Retrieve information about a book.

**Example**

```php
$fb_keys = array(
  'api_key' => 'key-1',
  'api_secret' => '2xxx_xxxxxxxx-xxxxx2',
);
$fb = new PhieldBook($fb_keys);

$fb->book_id = 'xxxxxxxxxxxxxxxxxxxxxxxx';

$my_book = $fb->book_meta();
var_dump($my_book);
```

Result:
```
array(3) {
  ["id"]=>
  string(24) "xxxxxxxxxxxxxxxxxxxxxxxx"
  ["title"]=>
  string(14) "Donor tracking"
  ["url"]=>
  string(52) "https://fieldbook.com/books/xxxxxxxxxxxxxxxxxxxxxxxx"
}
```

### sheet_meta

Retrieve information about the sheets in a book.

**Example**
```php
$fb_keys = array(
  'api_key' => 'key-1',
  'api_secret' => '2xxx_xxxxxxxx-xxxxx2',
);
$fb = new PhieldBook($fb_keys);

$fb->book_id = 'xxxxxxxxxxxxxxxxxxxxxxxx';

$my_book = $fb->sheet_meta();
var_dump($my_book);
```

Result:
```
array(2) {
  [0]=>
  array(4) {
    ["id"]=>
    string(24) "xxxxxxxxxxxxxxxxxxxxxxxx"
    ["title"]=>
    string(6) "Donors"
    ["slug"]=>
    string(6) "donors"
    ["url"]=>
    string(53) "https://fieldbook.com/sheets/xxxxxxxxxxxxxxxxxxxxxxxx"
  }
  [1]=>
  array(4) {
    ["id"]=>
    string(24) "xxxxxxxxxxxxxxxxxxxxxxxx"
    ["title"]=>
    string(13) "Contributions"
    ["slug"]=>
    string(13) "contributions"
    ["url"]=>
    string(53) "https://fieldbook.com/sheets/xxxxxxxxxxxxxxxxxxxxxxxx"
  }
}
```

### field_meta

Retrieve information about the fields in a sheet.

**Example**
```php
$fb_keys = array(
  'api_key' => 'key-1',
  'api_secret' => '2xxx_xxxxxxxx-xxxxx2',
);
$fb = new PhieldBook($fb_keys);

$fb->book_id = 'xxxxxxxxxxxxxxxxxxxxxxxx';
$fb->sheet_id = 'xxxxxxxxxxxxxxxxxxxxxxxx';

$my_book = $fb->field_meta();
var_dump($my_book);
```

Result:
```
array(4) {
  [0]=>
  array(5) {
    ["key"]=>
    string(2) "f4"
    ["name"]=>
    string(15) "Contribution ID"
    ["required"]=>
    bool(false)
    ["slug"]=>
    string(15) "contribution_id"
    ["fieldType"]=>
    string(7) "formula"
  }
  ...
}
```