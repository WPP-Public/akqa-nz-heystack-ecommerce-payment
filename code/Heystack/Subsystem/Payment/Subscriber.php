<?php
/**
 * This file is part of the Ecommerce-Vouchers package
 *
 * @package Ecommerce-Payment
 */

/**
 * Payment namespace
 */
namespace Heystack\Subsystem\Payment;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Heystack\Subsystem\Ecommerce\Transaction\Events as TransactionEvents;
use Heystack\Subsystem\Ecommerce\Transaction\Event\TransactionStoredEvent;
use Heystack\Subsystem\Ecommerce\Currency\CurrencyService;

use Heystack\Subsystem\Core\State\State;


use Heystack\Subsystem\Payment\Interfaces\PaymentHandlerInterface;

/**
 * Transaction's Subscriber
 * 
 * Handles both subscribing to events and acting on those events needed for the PaymentHandler work properly
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
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
     * @var \Heystack\Subsystem\Payment\Interfaces\PaymentHandlerInterface 
     */
    protected $paymentHandler;
    
    /**
     * Holds the State service
     * @var \Heystack\Subsystem\Core\State\State 
     */
    protected $state;

    /**
     * Creates the Subscriber object
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventService
     * @param \Heystack\Subsystem\Payment\Interfaces\PaymentHandlerInterface $paymentHandler
     * @param \Heystack\Subsystem\Core\State\State $state
     */
    public function __construct(EventDispatcherInterface $eventService, PaymentHandlerInterface $paymentHandler, State $state)
    {
        $this->eventService = $eventService;
        $this->paymentHandler = $paymentHandler;
        $this->state = $state;
    }
    
    /**
     * Returns an array of events to subscribe to and the methods to call when those events are fired
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            TransactionEvents::STORED  => array('onTransactionStored'),
            Events::SUCCESSFUL => array('onPaymentSuccessful')
        );
    }

    /**
     * Called after the Transaction is stored, signals that the payment handler needs to execute the payment
     * @param \Heystack\Subsystem\Ecommerce\Transaction\Event\TransactionStoredEvent $event
     */
    public function onTransactionStored(TransactionStoredEvent $event)
    {
        $this->paymentHandler->executePayment($event->getTransactionID());
    }
    
    /**
     * Called after the payment is successfully completed.
     * Clears everything in state except the active currency.
     */
    public function onPaymentSuccessful()
    {
        $this->state->removeAll(array(CurrencyService::STATE_KEY));
    }

}
