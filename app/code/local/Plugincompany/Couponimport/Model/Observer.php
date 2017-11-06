<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Coupon Import, released under the MIT License.
 * See the LICENSE file in the project root.
 */
class Plugincompany_Couponimport_Model_Observer {

    public function coreBlockAbstractToHtmlBefore(Varien_Event_Observer $observer){
        $version = Mage::getVersionInfo();
        if($version['major'] >= 1 && $version['minor'] > 6){
            return;
        }
        $block = $observer->getEvent()->getBlock();
        if($block instanceof Mage_Adminhtml_Block_Promo_Quote_Edit_Tabs){
            $priceRule = Mage::registry('current_promo_quote_rule');
            if(!$priceRule || $priceRule->getCouponType() != "2") return;
            $block->addTab('coupon_import', array(
                'label'        => Mage::helper('plugincompany_couponimport')->__('Import Coupon Codes'),
                'title'        => Mage::helper('plugincompany_couponimport')->__('Import Coupon Codes'),
                'content'     =>
                    $block->getLayout()->createBlock('core/template')->setTemplate('plugincompany/couponimport/Importform.phtml')->toHtml()
                    . $block->getLayout()->createBlock('plugincompany_couponimport/adminhtml_promo_quote_coupons_grid')->toHtml()
            ));
        }
    }

    public function salesruleRuleSaveAfter(Varien_Event_Observer $observer){
        $rule = $observer->getEvent()->getRule();
        if(!$rule->getId()){
            return;
        }
        $table = $rule->getResource()->getTable('salesrule/coupon');
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');

        $usesPerCoupon = $rule->getUsesPerCoupon() ? $rule->getUsesPerCoupon(): 'NULL';
        $usesPerCustomer = $rule->getUsesPerCustomer() ? $rule->getUsesPerCustomer(): 'NULL';
        $toDate = $rule->getToDate() ? "'" . $rule->getToDate() . "'" : 'NULL';
        $sql = "
        UPDATE {$table}
        SET usage_limit={$usesPerCoupon}, usage_per_customer={$usesPerCustomer},expiration_date={$toDate}
        WHERE rule_id = {$rule->getId()} AND is_primary IS NULL;
        ";
        $writeConnection->query($sql);
    }

}
