
Code to refactor
=================
1) app/Http/Controllers/BookingController.php
2) app/Repository/BookingRepository.php

----------------------------

What makes it amazing code
BaseRepository.php

Refactor Changes

use strict_types
use request()->only() instead of all(), because all() is not secure
functions return type should be added
use api resrouces (if we are working on apis)
use open api docs for writing apis documentation (if we are working on apis)
function named arguments
remove extra code which isn't used

Suggestions: BaseRepository.php if we convert into facade, will be more effecient according to me.
Note: BookingRepository > getAll() function can be refactor with better way. using when() will be better for reading and refactoring as well. Thanks

