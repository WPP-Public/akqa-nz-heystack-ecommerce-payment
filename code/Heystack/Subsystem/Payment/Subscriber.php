<?php

namespace Heystack\Subsystem\Payment;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Heystack\Subsystem\Ecommerce\Transaction\Events as TransactionEvents;

use Heystack\Subsystem\Core\Storage\Event as StorageEvent;

use Heystack\Subsystem\Payment\Interfaces\PaymentHandlerInterface;

use Heystack\Subsystem\Core\Storage\Backends\SilverStripeOrm\Backend;

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
            Backend::IDENTIFIER . '.' . TransactionEvents::STORED  => array('onTransactionStored')
        );
		
    }

    public function onTransactionStored(StorageEvent $event)
    {
		
        $this->paymentHandler->executePayment($event->getParentReference());
		
    }

}
