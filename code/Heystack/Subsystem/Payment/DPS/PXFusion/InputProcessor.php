<?php

namespace Heystack\Subsystem\Payment\DPS\PXFusion;

use Heystack\Subsystem\Core\Input\ProcessorInterface;

use Heystack\Subsystem\Core\Storage\Storage;
use Heystack\Subsystem\Core\Storage\Backends\SilverStripeOrm\Backend;

use Heystack\Subsystem\Core\State\State;

use Heystack\Subsystem\Ecommerce\Transaction\Interfaces\TransactionInterface;

class InputProcessor implements ProcessorInterface
{

    const IDENTIFIER = 'dps_fusion';

    /**
     * Holds the payment handler
     * @var \Heystack\Subsystem\Payment\Interfaces\PaymentServiceInterface
     */
    protected $paymentService;

    /**
     * @var \Heystack\Subsystem\Core\State\State
     */
    protected $state;

    /**
     * @var \Heystack\Subsystem\Core\Storage\Storage
     */
    protected $storage;
    
    /**
     * Holds the transaction
     * @var Heystack\Subsystem\Ecommerce\Transaction\Interfaces\TransactionInterface
     */
    protected $transaction;

    public function __construct(
        PaymentServiceInterface $paymentService,
        Storage $storage,
        State $state,
        TransactionInterface $transaction
    ) {
        $this->paymentService = $paymentService;
        $this->storage = $storage;
        $this->state = $state;
        $this->transaction = $transaction;
    }

    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    public function process(\SS_HTTPRequest $request)
    {
        $httpMethod = $request->httpMethod();

        if ($httpMethod == 'POST' && $request->param('ID') == 'complete') {

            //get the payment dps txnref from the database

            //1. Get session id for transaction from state

            //2. Get payment from storage

            //3. Use txn ref from storage to complete the payment

            $sessionId = $this->state->getByKey(self::IDENTIFIER . '.sessionid');

            if ($sessionId) {

                // get the PXFusion payment associated with this sessionid
                
                $payment = \DataObject::get_one('StoredPXFusionPayment', "SessionId = '{$sessionId}'");

                if ($payment instanceof \StoredPXFusionPayment && $payment->DpsTxnRef) {

                    $paymentResponse = $this->paymentService->completeTransaction($payment->DpsTxnRef);
                    
                    $results = $this->storage->process($paymentResponse);
                    
                    // store the transaction
                    $transactionResults = $this->storage->process($this->transaction);
                    
                    if (isset($results[Backend::IDENTIFIER]) && isset($transactionResults[Backend::IDENTIFIER])) {

                        // get the actual transaction and payment
                        $storedTransaction = $transactionResults[Backend::IDENTIFIER];
                        $pxPostPayment = $results[Backend::IDENTIFIER];
                                 
                        // set the parents of each object
                        $payment->ParentID = $storedTransaction->ID;
                        $payment->PXPostPaymentID = $pxPostPayment->ID;
                        $payment->write();

                        $pxPostPayment->ParentID = $storedTransaction->ID;
                        $pxPostPayment->write();

                    } else {
                        
                        return array(
                            'Success' => false
                        );
                        
                    }

                }
                
                return array(
                    'Success' => true,
                    'Complete' => true,
                    'Data' => $paymentResponse
                );

            }

        } elseif ($httpMethod == 'GET' && $request->param('ID') == 'check') {

            $paymentResponse = $this->paymentService->checkTransaction($request->getVar('sessionid'));

            $this->state->setByKey(self::IDENTIFIER . '.sessionid', $request->getVar('sessionid'));

            $this->storage->process($paymentResponse);
            
            if ($paymentResponse->StatusCode === 0) {

                return array(
                    'Success' => true,
                    'Data' => $paymentResponse
                );
                
            }

        }
        
        return array(
            'Success' => false
        );
    }

}
