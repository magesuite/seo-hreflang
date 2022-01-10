<?php
declare(strict_types=1);

namespace MageSuite\SeoHreflang\Block\System\Store\Edit\Form;

class Store extends \Magento\Backend\Block\System\Store\Edit\Form\Store
{
    /**
     * @param \Magento\Framework\Data\Form $form
     * @return void
     */
    protected function _prepareStoreFieldset(\Magento\Framework\Data\Form $form): void
    {
        parent::_prepareStoreFieldset($form);

        $fieldset = $form->getForm()->getElement('store_fieldset');
        $storeModel = $this->_coreRegistry->registry('store_data');
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
