# One23 Helpers

## Install

```shell
composer require one23/helpers
```

---

## Helpers

### Arr

```php
use One23\Helpers\Arr;

Arr::randomValue(['a', 'b', 'c']); // a or b or c
Arr::randomValues(['a', 'b', 'c'], 2); // [a, b] or [b, c] or [c, a]
Arr::randomValues(['a', 'b', 'c'], 3); // shuffle array

```

### Url

```php
use One23\Helpers\Url;

$newObj = Url::object('http://aliexpress.ru/fsd')->clone(); // clone



```

### String

```php
use One23\Helpers\Str;

Str::hasEntityCharters('abc &a abc'); // false
Str::hasEntityCharters('abc &nbsp; abc'); // true

```

### Random  

```php
use One23\Helpers\Random;

Random::alpha(10); // abcdefghij (a-z)
Random::alpha(10, null); // aBcDeFgHiJ (a-zA-Z)
Random::alpha(10, true); // ABCDEFGHIJ (A-Z)

Random::base58(6); // 1a2b3c (without 0, O, I, l)

Random::base64(6); // 1a2b3c (a-zA-Z0-9)) 

Random::byte(6); // 1a2b3c (random bytes)

Random::hex(6); // 1a2b3c (a-f0-9)

Random::digit(6); // 123456 (0-9)

Random::alphaDigital(6); // 1a2b3c (a-z0-9)
Random::alphaDigital(6, null); // 1a2b3c (a-zA-Z0-9)
Random::alphaDigital(6, true); // 1a2b3c (A-Z0-9)

Random::digitalAlpha(6); // alias of alphaDigital

(new Random('charters'))->generate(8); // sretrahc ([charters]+)
```

### Integer

```php
use One23\Helpers\Integer;

Integer::val('123'); // 123
Integer::val('123.45'); // 123
Integer::val('abc'); // null

//

Integer::first(1, 2, 3, 4); // 1
Integer::first('a', 'b', 'c'); // null
Integer::first('a', 'b', 'c', 1); // 1

//

Integer::last(1, 2, 3, 4); // 4
Integer::last('a', 'b', 'c'); // null
Integer::last(1, 'a', 'b', 'c'); // 1

// get (value, default, min, max)

Integer::get(0, null, 1, 5); // null
Integer::get(1, null, 1, 5); // 1
Integer::get(6, null, 1, 5); // null
Integer::get('6', 1, 1, 5); // 1

// getOrNull - alias of `val`

Integer::getOrNull(0); // 0
Integer::getOrNull('abc'); // null

// getOrZero

Integer::getOrZero(0); // 0
Integer::getOrZero('abc'); // 0

// all

Integer::all(1, 2, 3, 4); // [1, 2, 3, 4]
Integer::all(1, 'a', 2, 'b'); // [1, 2]
Integer::all(1, 'a', 2, 'b', 3, 'c'); // [1, 2, 3]

// uniq

Integer::uniq(1, 2, 3, 4); // [1, 2, 3, 4]
Integer::uniq(1, 2, 3, 4, 1, 2, 3, 4); // [1, 2, 3, 4]  
Integer::uniq(1, 'a', 2, 'b', 3, 'c'); // [1, 2, 3]

// min

Integer::min(1, 2, 3, 4); // 1
Integer::min(3, 4, 2, 1); // 1
Integer::min(1, 'a', 2, 'b', 3, 'c'); // 1

// max

Integer::max(1, 2, 3, 4); // 4
Integer::max(3, 4, 2, 1); // 4
Integer::max(1, 'a', 2, 'b', 3, 'c'); // 3

```

- 
