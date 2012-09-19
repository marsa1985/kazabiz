<?php

/*------------------------------------------------------------------------
# En Masse - Social Buying Extension 2010
# ------------------------------------------------------------------------
# By Matamko.com
# Copyright (C) 2010 Matamko.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.matamko.com
# Technical Support:  Visit our forum at www.matamko.com
-------------------------------------------------------------------------*/

//------------------------------------------------------------------------
// Class for providing the encapsulation of the Payment Gateway
header('Content-Type: text/html; charset=ISO-8859-1');

class PayGtyWorldpay {

    public static function returnStatus() {
        $status->coupon = 'Free';
        $status->order = 'Unpaid';
        return $status;
    }

    public static function checkConfig($payGty) {
        $attribute_config = json_decode($payGty->attribute_config);
        
        if (!isset($attribute_config->transId) || trim($attribute_config->transId) == "") {
            return false;
        }
        if (!isset($attribute_config->currency) || trim($attribute_config->currency) == "") {
            return false;
        }
        
        return true;
    }

    public static function validateTxn($payClass) {
        return true;
    }

    public static function generatePaymentDetail() {
        $paymentDta = array();
        foreach ($_POST as $key => $value) {
            $paymentDta[$key] = $_POST[$key];

        }
        return $paymentDta;
    }
}

?>