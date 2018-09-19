<?php
namespace MageSuite\SeoHreflang\Block\System\Store\Edit\Form;

class Store extends \Magento\Backend\Block\System\Store\Edit\Form\Store
{
    /**
     * Prepare store specific fieldset
     *
     * @param \Magento\Framework\Data\Form $form
     * @return void
     */
    protected function _prepareStoreFieldset(\Magento\Framework\Data\Form $form)
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
                'required' => false
            ]
        );
    }
}
