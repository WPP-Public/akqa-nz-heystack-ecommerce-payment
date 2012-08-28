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


use Heystack\Subsystem\Core\Storage\Storage;
use Heystack\Subsystem\Core\State\State;


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
    public function __construct(EventDispatcherInterface $eventService, State $state, Storage $storageService)
    {        
        $this->eventService = $eventService;
        $this->state = $state;
        $this->storageService = $storageService;
    }

    /**
     * Returns an array of events to subscribe to and the methods to call when 
     * those events are fired
     * @return array
     */
    public static function getSubscribedEvents()
    {

        return array(
            
        );

    }

    

   

}
