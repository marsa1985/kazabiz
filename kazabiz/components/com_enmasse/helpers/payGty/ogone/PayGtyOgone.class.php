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

class PayGtyOgone {
    public static function returnStatus() {
        $status = new JObject();
        $status->coupon = 'Free';
        $status->order = 'Unpaid';
        return $status;
    }
    public static function checkConfig($payGty) {
        $attribute_config = json_decode($payGty->attribute_config);
        if (!isset($attribute_config->pspid) || trim($attribute_config->pspid) == "") {
            return false;
        }
        return true;
    }

    public static function makePayment($amt) {
        return false;
    }

    public static function validateTxn($payClass) {
        $payGty = JModel::getInstance('payGty', 'enmasseModel')->getByClass($payClass);
        $attribute_config = json_decode($payGty->attribute_config);
        $sPassPhrase = $attribute_config->out_passphrase;
        $aData = array();
        foreach ($_REQUEST as $key => $value) {
            $aData[strtoupper($key)] = $value;
        }
        $aResult = array();
        $aResult['AMOUNT'] = $aData['AMOUNT'];
        $aResult['BRAND'] = $aData['BRAND'];
        $aResult['CARDNO'] = $aData['CARDNO'];
        $aResult['CN'] = $aData['CN'];
        $aResult['CURRENCY'] = $aData['CURRENCY'];
        $aResult['NCERROR'] = $aData['NCERROR'];
        $aResult['ORDERID'] = $aData['ORDERID'];
        $aResult['PAYID'] = $aData['PAYID'];
        $aResult['STATUS'] = $aData['STATUS'];
        $aResult['TRXDATE'] = $aData['TRXDATE'];
        $sHash = '';
        foreach ($aResult as $key => $value) {
            $sHash .= $key . '=' . $value . $sPassPhrase;
        }
        $sHash = strtoupper(sha1($sHash));
        if ($sHash == $_REQUEST['SHASIGN'])
            return true;
        else
            return false;
    }

    public static function generatePaymentDetail() {
        $aData = array();
        foreach ($_REQUEST as $key => $value) {
            $aData[strtoupper($key)] = $value;
        }
        $aResult = array();
        $aResult['AMOUNT'] = $aData['AMOUNT'];
        $aResult['BRAND'] = $aData['BRAND'];
        $aResult['CARDNO'] = $aData['CARDNO'];
        $aResult['CN'] = $aData['CN'];
        $aResult['CURRENCY'] = $aData['CURRENCY'];
        $aResult['NCERROR'] = $aData['NCERROR'];
        $aResult['ORDERID'] = $aData['ORDERID'];
        $aResult['PAYID'] = $aData['PAYID'];
        $aResult['STATUS'] = $aData['STATUS'];
        $aResult['TRXDATE'] = $aData['TRXDATE'];
        return $aResult;
    }
}
