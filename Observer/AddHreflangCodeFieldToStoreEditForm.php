<?php

namespace MageSuite\SeoHreflang\Observer;

class AddHreflangCodeFieldToStoreEditForm implements \Magento\Framework\Event\ObserverInterface
{
    protected $registry;

    public function __construct(\Magento\Framework\Registry $registry)
    {
        $this->registry = $registry;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Backend\Block\System\Store\Edit\AbstractForm $block */
        $block = $observer->getBlock();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $block->getForm();

        $fieldset = $form->getForm()->getElement('store_fieldset');
        $storeModel = $this->registry->registry('store_data');
        $fieldset->addField(
            'store_hreflang_code',
            'text',
            [
                'name' => 'store[hreflang_code]',
                'label' => __('Hreflang Code'),
                'value' => $storeModel->getHreflangCode(),
                'required' => false,
                'class' => 'cs-csfeature__logo',
                'note' => 'The value like x-default, en, de-CH. Learn more on: <a href="https://support.google.com/webmasters/answer/189077">https://support.google.com/webmasters/answer/189077</a>'
            ]
        );
    }
}
