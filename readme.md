# One23 Helpers

## Install

```shell
composer require one23/helpers
```

---

## Helpers

### Integer

```shell
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