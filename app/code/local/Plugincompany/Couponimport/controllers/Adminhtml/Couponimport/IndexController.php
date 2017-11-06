<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Coupon Import, released under the MIT License.
 * See the LICENSE file in the project root.
 */
?>
<?php
class Plugincompany_Couponimport_Adminhtml_Couponimport_IndexController extends Mage_Adminhtml_Controller_Action
{

    protected function _initRule()
    {
        $this->_title($this->__('Promotions'))->_title($this->__('Shopping Cart Price Rules'));

        Mage::register('current_promo_quote_rule', Mage::getModel('salesrule/rule'));
        $id = (int)$this->getRequest()->getParam('id');

        if (!$id && $this->getRequest()->getParam('rule_id')) {
            $id = (int)$this->getRequest()->getParam('rule_id');
        }

        if ($id) {
            Mage::registry('current_promo_quote_rule')->load($id);
        }
    }

    /**
     * Process pasted coupons
     */
    public function pasteAction()
    {
        $post = Mage::app()->getRequest()->getPost();
        $ruleId = $this->getRequest()->getParam('id');
        $coupons = explode("\n", $post['paste_rule_import']);

        $couponCount = count($coupons);

        //remove duplicates
        $coupons = array_unique($coupons);

        if ($couponCount != count($coupons)) {
            $duplicates = $couponCount - count($coupons);
            Mage::getSingleton('core/session')->addNotice("$duplicates duplicate codes not added");
        }

        if (!empty($coupons)) {
            Mage::getModel('plugincompany_couponimport/importer')->importCoupons($coupons,$ruleId);
        }

    }

    /**
     * Process uploaded file
     */
    public function importfileAction()
    {
        $ruleId = $this->getRequest()->getParam('id');
        $tmpName = $_FILES['csv_rule_import']['tmp_name'];

        //if no file detected display error
        if (!$tmpName) {
            Mage::getSingleton('core/session')->addNotice("Please select a file first");
            return $this->getResponse()->setBody(
                '<script type="text/javascript">parent.update()</script>'
            );
        }

        //if not .txt file display error
        if (!$_FILES['csv_rule_import']['type'] == "text/plain" || !stristr($_FILES['csv_rule_import']['name'],'.txt')) {
            Mage::getSingleton('core/session')->addError("Uploaded file is not a .txt file");
            return $this->getResponse()->setBody(
                '<script type="text/javascript">parent.update()</script>'
            );
        }


        //process coupons
        $coupons = array();
        $fh = fopen($tmpName,'r');
        while ($line = fgets($fh)) {
            $coupons[] = str_replace("\n", '', $line);
        }

        $couponCount = count($coupons);

        //remove duplicates
        $coupons = array_unique($coupons);

        if ($couponCount != count($coupons)) {
            $duplicates = $couponCount - count($coupons);
            Mage::getSingleton('core/session')->addNotice("$duplicates duplicate codes not added");
        }

        if (!empty($coupons)) {
            Mage::getModel('plugincompany_couponimport/importer')->importCoupons($coupons,$ruleId);
        }

        return $this->getResponse()->setBody(
            '<script type="text/javascript">parent.update()</script>'
        );
    }

    public function couponsGridAction(){
        $this->_initRule();
        $this->loadLayout()->renderLayout();
    }

    /**
     * Export coupon codes as excel xml file
     *
     * @return void
     */
    public function exportCouponsXmlAction()
    {
        $this->_initRule();
        $rule = Mage::registry('current_promo_quote_rule');
        if ($rule->getId()) {
            $fileName = 'coupon_codes.xml';
            $content = $this->getLayout()
                ->createBlock('adminhtml/promo_quote_edit_tab_coupons_grid')
                ->getExcelFile($fileName);
            $this->_prepareDownloadResponse($fileName, $content);
        } else {
            $this->_redirect('*/*/detail', array('_current' => true));
            return;
        }
    }

    /**
     * Export coupon codes as CSV file
     *
     * @return void
     */
    public function exportCouponsCsvAction()
    {
        $this->_initRule();
        $rule = Mage::registry('current_promo_quote_rule');
        if ($rule->getId()) {
            $fileName = 'coupon_codes.csv';
            $content = $this->getLayout()
                ->createBlock('adminhtml/promo_quote_edit_tab_coupons_grid')
                ->getCsvFile();
            $this->_prepareDownloadResponse($fileName, $content);
        } else {
            $this->_redirect('*/*/detail', array('_current' => true));
            return;
        }
    }

    /**
     * Coupons mass delete action
     */
    public function couponsMassDeleteAction()
    {
        $this->_initRule();
        $rule = Mage::registry('current_promo_quote_rule');

        if (!$rule->getId()) {
            $this->_forward('noRoute');
        }

        $codesIds = $this->getRequest()->getParam('ids');

        if (is_array($codesIds)) {

            $couponsCollection = Mage::getResourceModel('salesrule/coupon_collection')
                ->addFieldToFilter('coupon_id', array('in' => $codesIds));

            foreach ($couponsCollection as $coupon) {
                $coupon->delete();
            }
        }
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/promo/quote');
    }

}