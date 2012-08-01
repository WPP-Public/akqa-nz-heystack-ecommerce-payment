<?php
/**
 * This file is part of the Heystack package
 *
 * @package Ecommerce-Payment
 */

/**
 * Payment ModelAdmin
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Heystack
 *
 */
class PaymentAdmin extends ModelAdmin
{

    public static $managed_models = array(
        'PXPostPayment'
    );

    public static $url_segment = 'payments';
    public static $menu_title = 'Payments';

}
