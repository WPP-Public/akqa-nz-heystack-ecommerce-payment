<?php

class PaymentAdmin extends ModelAdmin
{

    public static $managed_models = array(
        'PXPostPayment'
    );

    public static $url_segment = 'payments';
    public static $menu_title = 'Payments';

}
