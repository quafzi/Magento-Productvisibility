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