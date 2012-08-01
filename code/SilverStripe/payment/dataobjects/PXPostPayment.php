<?php
/**
 * This file is part of the Heystack package
 *
 * @package Ecommerce-Payment
 */

use Heystack\Subsystem\Payment\DPS\Interfaces\PXPostPaymentInterface;

use Heystack\Subsystem\Payment\Traits\PaymentTrait;
use Heystack\Subsystem\Payment\DPS\Traits\DPSPaymentTrait;
use Heystack\Subsystem\Payment\DPS\Traits\PXPostPaymentTrait;

/**
 * PXPostPayment stores information about payments made with the PXPost method
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Heystack
 *
 */
class PXPostPayment extends DataObject implements PXPostPaymentInterface
{
    use PaymentTrait;
    use DPSPaymentTrait;
    use PXPostPaymentTrait;

    public static $db = array(
        'Status' => "Enum('Incomplete,Success,Failure,Pending','Incomplete')",
        'CurrencyCode' => 'Varchar(255)',
        'Message' => 'Text',
        'Amount' => 'Decimal(10,2)',
        'IP' => 'Varchar(255)',
        'TransactionType' => "Enum('Purchase,Auth,Complete,Refund,Validate', 'Purchase')",
        'MerchantReference' => 'Varchar(255)',
        'TransactionReference' => 'Varchar(255)',
        'AuthCode' => 'Varchar(255)',
        'XMLResponse' => 'Text',
        'BillingID' => 'Varchar(255)',
        'HelpText' => 'Varchar(255)',
        'ResponseCode' => 'Varchar(255)',
        'SettlementDate' => 'Date'
    );

    public static $has_one = array(
        'Transaction' => 'StoredTransaction'
    );
}
