.. sectnum::

.. contents:: Contents

Intention
=========

It is a common case: You create a product in the backend of Magento and want it
to be shown in the shop. But in a little bit of hurry you miss to set a property
which prevents its visibility.

That's why this module exists. Its intention is to show you, which properties
could be responsible for hiding the product. It is ready to handle multiple
stores and so it gives you the chance to determine in which store the product
should_ be visible.

.. _should: Extensibility_

You'll find the output of this module as a new tab of the
product edit page in the backend of Magento.

Netresearch_Productvisibility has `not a finished state`_, so much work has to
be done to improve it.

.. _`not a finished state`: Issues_

Features
========

We provide a set of default checkpoints which include

- if product is enabled
- if product is visible in catalog
- if product has a website
- if product has a category
- if product is in stock
- if product is up to date in price index
- if product is up to date in stock index

For configurable products a warning will be shown additionally, if its simple
products are visible individually.

If a checkpoint fails, there will be a howto which gives you a hint.
For some checkpoints there will be also a description where to find the product
in the shop (categories, websites).

These checkpoints are exclusively visible after you selected a store view. Otherwise
there will appear a list of store views with the information where the product
should be visible.

Installation
============

To install this extension, you should use modman_:

::

    modman clone https://github.com/quafzi/Magento-Productvisibility.git

.. _modman: https://github.com/colinmollenhour/modman

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

Extensibility
=============

There are many modules available for Magento and some of them are able to hide
products. We can't know them all and that's why this module is extensible.
If you want to add a new checkpoint, just catch the event like this:

::

    <config>
        <adminhtml>
            <events>
                <netresearch_product_visibility_checkpoints_load>
                    <observers>
                        <my_module>
                            <type>singleton</type>
                            <class>my_module/observer</class>
                            <method>addCheckpoint</method>
                        </my_module>
                    </observers>
                </netresearch_product_visibility_checkpoints_load>
            </events>
        </adminhtml>
    </config>

Now you can add a new checkpoint in the observer:

::

    /**
     * add checkpoint for My_Module
     *
     * @param Varien_Event_Observer $observer Observer
     *
     * @return void
     */
    public function addCheckpoint(Varien_Event_Observer $observer)
    {
        /**
         * @var Netresearch_Productvisibility_Block_Adminhtml_Catalog_Product_Edit_Tab_Visibility
         */
        $block = $observer->getEvent()->getVisibilityBlock();
        $block->addCheckpoint(
            Mage::helper('productvisibility')->createCheckpoint(
                /* name of the checkpoint */
                'My Module allows product to be shown',

                /* if product is visible */
                Mage::helper('my_module')->isProductVisible($block->getProduct()),

                /* how to change visibility */
                Mage::helper('productvisibility')
                    ->__('...'),

                /* optional: details of visibility */
                Mage::helper('productvisibility')
                    ->__('...'),

                /* optional: dependencies */
                array('is in stock')
            )
        );

The third parameter of the method ``createCheckpoint()`` defines, if the product
is visible according to this checkpoint. The following return values are
supported:

- ``true`` means that the product is visible
- ``false`` means that the product is not visible
- ``null`` (which creates a warning message)

Modules can also rewrite existing checkpoints. To do so, you must only use the
name of the existing checkpoint.
