<?php

namespace Heystack\Subsystem\Payment;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Heystack\Subsystem\Ecommerce\Transaction\Events as TransactionEvents;
use Heystack\Subsystem\Ecommerce\Transaction\Event\TransactionStoredEvent;

use Heystack\Subsystem\Core\State\State;


use Heystack\Subsystem\Payment\Interfaces\PaymentHandlerInterface;

class Subscriber implements EventSubscriberInterface
{
    protected $eventService;
    protected $paymentHandler;
    protected $state;

    public function __construct(EventDispatcherInterface $eventService, PaymentHandlerInterface $paymentHandler, State $state)
    {
        $this->eventService = $eventService;
        $this->paymentHandler = $paymentHandler;
        $this->state = $state;
    }

    public static function getSubscribedEvents()
    {
        return array(
            TransactionEvents::STORED  => array('onTransactionStored'),
            Events::SUCCESSFUL => array('onPaymentSuccessful')
        );
    }

    public function onTransactionStored(TransactionStoredEvent $event)
    {
        $this->paymentHandler->executePayment($event->getTransactionID());
    }
    
    public function onPaymentSuccessful()
    {
        $this->state->removeAll(array(\Heystack\Subsystem\Ecommerce\Currency\CurrencyService::STATE_KEY));
    }

}
