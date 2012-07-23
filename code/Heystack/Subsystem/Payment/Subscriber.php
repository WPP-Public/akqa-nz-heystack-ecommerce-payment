<?php

namespace Heystack\Subsystem\Payment;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Heystack\Subsystem\Ecommerce\Transaction\Events as TransactionEvents;
use Heystack\Subsystem\Ecommerce\Transaction\Event\TransactionStoredEvent;


use Heystack\Subsystem\Payment\Interfaces\PaymentHandlerInterface;

class Subscriber implements EventSubscriberInterface
{
    protected $eventService;
    protected $paymentHandler;

    public function __construct(EventDispatcherInterface $eventService, PaymentHandlerInterface $paymentHandler)
    {
        $this->eventService = $eventService;
        $this->paymentHandler = $paymentHandler;
    }

    public static function getSubscribedEvents()
    {
        return array(
            TransactionEvents::STORED  => array('onTransactionStored')
        );
    }

    public function onTransactionStored(TransactionStoredEvent $event)
    {
        $this->paymentHandler->executePayment($event->getTransactionID());
    }

}
