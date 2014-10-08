# Order management

REST Application to manage orders
An order is linked to products through line items.

## Requirements

- PHP 5 (Developed on PHP 5.6)
- sqlite3


## Installation

```
./INSTALL
```

After that, in config.php, change the value for the key "root" to define the path to the project.

Then you must make the www folder accessible (symlink in your /var/www, or apache conf).
For example a symlink order to www would make an url like: http://localhost/orders/.

## Call examples

### GET Calls:

```
http://localhost/orders/?service=<SERVICENAME>[&condition1=value1&condition2=value2...]
```

example:

```
// get the products where the name is "book" and the price is 20
http://localhost/orders/?service=Product&name=book&price=20
```

### POST Calls

```
http://localhost/orders/?service=<SERVICENAME>[&field1=value1&field2=value2...]
```

example:

```
// save a product with as name "dvd" and as price 20
http://localhost/orders/?service=Product&name=book&price=20
```

### PUT Calls

```
http://localhost/orders/?service=<SERVICENAME>&values={"field1":"value1","field2":"value2",...}[&conditions={"condition1":"value1","condition2":"value"}...]
```

values and condition must be valid JSON values

example:

```
// update the product having the id 2 to set its price to 10
http://localhost/orders/?service=Product&conditions={"id_product":2}&values={"price":10}
```

### DELETE Calls

```
http://localhost/orders/?service=<SERVICENAME>[&condition1=value1&condition2=value2...]
```

example:

```
// Delete the products having as price 10
http://localhost/orders/?service=Product&price=10
```

## Available Services

- product: To manage products
- order: To manage orders
- lineItem: To manage line items

## Available fields per service

### Product

- id_product (not updatable)
- name (varchar)
- price (float)

### Order

- id_order (not updatable)
- vat
- status
- cancel_reason
- date
- date_creation (not updatable)

### Line item

- id_line_item (not updatable)
- id_order (not updatable)
- id_product (not updatable)
- quantity

## TODO

- The code should be commented and unit-tested,
- Some parts of the code should be uniformised (eg. signatures with multiple arguments or arrays for the same kind of objects)
- The order's cancel reason should not be updatable if the new status is not "CANCELLED"
