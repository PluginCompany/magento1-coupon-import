<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Coupon Import, released under the MIT License.
 * See the LICENSE file in the project root.
 */
?>
<?php
class Plugincompany_Couponimport_Model_Importer {


    /**
     * Processes coupons based on array input
     *
     * @param array $coupons
     * @param $ruleId
     * @return $this
     */
    public function importCoupons(array $coupons,$ruleId)
    {
        $rule = Mage::getModel('salesrule/rule')->load($ruleId);
        $now = Mage::getModel('core/date')->date('Y-m-d H:i:s');
        $success = 0;$error = 0;

        $table = $rule->getResource()->getTable('salesrule/coupon');
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');

        if (version_compare(Mage::getVersion(), '1.7', '>=')){
            $sql = "INSERT IGNORE INTO $table (rule_id,usage_limit,usage_per_customer,expiration_date,created_at,type,code) ";
        }
        else {
            $sql = "INSERT IGNORE INTO $table (rule_id,usage_limit,usage_per_customer,expiration_date,code) ";
        }
        $couponSql = array();
        foreach ($coupons as $couponText) {
            //trim spaces
            $couponText =  trim($couponText);

            //if empty string don't add
            if ($couponText == '' || !$couponText) {
                continue;
            }

            if (version_compare(Mage::getVersion(), '1.7', '>=')){
                $couponSql[]= "({$rule->getRuleId()},'{$rule->getUsesPerCoupon()}','{$rule->getUsesPerCustomer()}','{$rule->getToDate()}','{$now}',1,'{$couponText}')";
            }
            else {
                $couponSql[]= "({$rule->getRuleId()},'{$rule->getUsesPerCoupon()}','{$rule->getUsesPerCustomer()}','{$rule->getToDate()}','{$couponText}')";
            }
        }
        $totalCoupons = count($couponSql);
        $values = implode(', ',$couponSql);
        try
        {
            //set coupon data and save
            $sql .= " VALUES " . $values . ";";
            $result = $writeConnection->query($sql);
            $success = $result->rowCount();
        }
        catch(Exception $e)
        {
            Mage::log($sql, null, 'plugincompany_couponimport.log',true);
            Mage::log($e->getMessage(), null, 'plugincompany_couponimport.log',true);
            Mage::getSingleton('core/session')->addError("An error occured while importing");
            return;
        }
        
        if($success > 0)
            Mage::getSingleton('core/session')->addSuccess("Successfully imported $success coupon codes");

        $error = $totalCoupons - $success;
        if ($error > 0) {
            Mage::getSingleton('core/session')->addError("Unable to import $error coupon codes");
        }

        return $this;
    }

}
