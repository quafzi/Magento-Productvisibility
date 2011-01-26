Issues
======

There are many possibilities to improve this module. So for example we don't
explicitly handle

- bundle products
- grouped products

Also some magento features are not handled, that could have influences on
product visibility, for example:

- flat catalog
- catalog events (enterprise feature)
- catalog permissions (enterprise feature)
- customer segments (enterprise feature)
- some indexes (search, ...)
- compiler
- find modules catching events ``catalog_product_is_salable_before`` and ``catalog_product_is_salable_after``

In the past, we did not need these features to be handled by this module, that's
just why we did not already implement this. You could either implement it on your
own (and send us your solution) or just be patient and hope we do it betimes.
