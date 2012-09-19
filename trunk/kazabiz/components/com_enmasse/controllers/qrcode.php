<?php

jimport('joomla.application.component.controller');

class EnmasseControllerQrcode extends JController {
    
    const QR_LEVEL = 'L';
    const QR_SIZE = '2';
    
    function __construct() {
        parent::__construct();
    }
    
    function generateQrcode() {
        /*
            Coupon Serial: 2-1-1
            Deal description: desc
            Buyer name: Phuoc Nguyen
            Purchase Date: 03-16-2012
            Order Comment: comments
        */
        
        header("Content-type: image/png");

        include ('components/com_enmasse/helpers/phpqrcode/qrlib.php');
        
        $val = $_REQUEST['val'];
        
        QRcode::png($val);
        
        die;
    }
    
}

?>