<?php
/**
 * This file is part of the Ecommerce-Payment package
 *
 * @package Ecommerce-Payment
 */

/**
 * Payment namespace
 */
namespace Heystack\Subsystem\Payment\DPS\PXFusion;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Heystack\Subsystem\Ecommerce\Transaction\Events as TransactionEvents;
use Heystack\Subsystem\Ecommerce\Currency\CurrencyService;

use Heystack\Subsystem\Core\Storage\Storage;
use Heystack\Subsystem\Core\Storage\Event as StorageEvent;
use Heystack\Subsystem\Core\State\State;

use Heystack\Subsystem\Payment\Events\PaymentEvent;

use Heystack\Subsystem\Core\Storage\Backends\SilverStripeOrm\Backend;

/**
 * Transaction's Subscriber
 *
 * Handles both subscribing to events and acting on those events needed for the PaymentHandler work properly
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @author Stevie Mayhew <stevie@heyday.co.nz>
 * @package Ecommerce-Core
 */
class Subscriber implements EventSubscriberInterface
{
    /**
     * Holds the Event Dispatcher service
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventService;

    /**
     * Holds the PaymentHandler service
     * @var \Heystack\Subsystem\Payment\Interfaces\PaymentServiceInterface
     */
    protected $paymentHandler;

    /**
     * Holds the State service
     * @var \Heystack\Subsystem\Core\State\State
     */
    protected $state;

    /**
     * The storage service which will be used in cases where storage is needed.
     * @var object
     */
    protected $storageService;

    /**
     * Creates the Subscriber object
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface    $eventService
     * @param \Heystack\Subsystem\Payment\Interfaces\PaymentServiceInterface $paymentHandler
     * @param \Heystack\Subsystem\Core\State\State                           $state
     */
    public function __construct(EventDispatcherInterface $eventService, PaymentServiceInterface $paymentHandler, State $state, Storage $storageService)
    {
        $this->eventService = $eventService;
        $this->paymentHandler = $paymentHandler;
        $this->state = $state;
        $this->storageService = $storageService;
    }

    /**
     * Returns an array of events to subscribe to and the methods to call when those events are fired
     * @return array
     */
    public static function getSubscribedEvents()
    {

        return array(
            Backend::IDENTIFIER . '.' . TransactionEvents::STORED  => array('onTransactionStored'),
            Events::SUCCESSFUL => array('onPaymentSuccessful'),
            Events::FAILED => array('onPaymentFailed'),
        );

    }

    /**
     * Called after the Transaction is stored, signals that the payment handler needs to execute the payment
     * @param \Heystack\Subsystem\Core\Storage\Event $event
     */
    public function onTransactionStored(StorageEvent $event)
    {
        $payment = $this->paymentHandler->executePayment($event->getParentReference());

        $payment->setParentReference($event->getParentReference());

        $this->storageService->process($payment);

    }

    /**
     * Called after the payment is successfully completed.
     * Clears everything in state except the active currency.
     */
    public function onPaymentSuccessful(PaymentEvent $event)
    {

        $this->setStoredTransactionStatus($event, 'Successful');

        $this->state->removeAll(array(CurrencyService::IDENTIFIER));

    }

    public function onPaymentFailed(PaymentEvent $event)
    {

        $this->setStoredTransactionStatus($event, 'Failed');

    }

    protected function setStoredTransactionStatus(PaymentEvent $event, $status)
    {

        $transaction =  \DataObject::get_by_id('StoredTransaction', $event->getPayment()->getTransactionID());

        if ($transaction instanceof \StoredTransaction) {
            $transaction->Status = $status;
            $transaction->write();
        }

    }

}
