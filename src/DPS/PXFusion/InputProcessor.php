<?php

namespace Heystack\Payment\DPS\PXFusion;

use Heystack\Core\Identifier\Identifier;

use Heystack\Core\Input\ProcessorInterface;
use Heystack\Core\State\State;
use Heystack\Core\Storage\Backends\SilverStripeOrm\Backend;

use Heystack\Core\Storage\Storage;

use Heystack\Ecommerce\Transaction\Interfaces\TransactionInterface;
use Heystack\Payment\DPS\PXPost\PaymentResponse as PXPostPaymentResponse;

/**
 * Class InputProcessor
 * @package Heystack\Payment\DPS\PXFusion
 */
class InputProcessor implements ProcessorInterface
{

    /**
     *
     */
    const IDENTIFIER = 'dps_fusion';

    /**
     * Holds the payment handler
     * @var \Heystack\Payment\DPS\PXFusion\Service
     */
    protected $paymentService;

    /**
     * @var \Heystack\Core\State\State
     */
    protected $state;

    /**
     * @var \Heystack\Core\Storage\Storage
     */
    protected $storage;

    /**
     * Holds the transaction
     * @var \Heystack\Ecommerce\Transaction\Interfaces\TransactionInterface
     */
    protected $transaction;

    /**
     * @param \Heystack\Payment\DPS\PXFusion\Service $paymentService
     * @param Storage $storage
     * @param State $state
     * @param TransactionInterface $transaction
     */
    public function __construct(
        Service $paymentService,
        Storage $storage,
        State $state,
        TransactionInterface $transaction
    ) {
        $this->paymentService = $paymentService;
        $this->storage = $storage;
        $this->state = $state;
        $this->transaction = $transaction;
    }
    /**
     * @return \Heystack\Core\Identifier\Identifier
     */
    public function getIdentifier()
    {
        return new Identifier(self::IDENTIFIER);
    }

    /**
     * @param  \SS_HTTPRequest $request
     * @return array
     */
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

                $payment = \DataList::create('StoredPXFusionPayment')->filter("SessionId", $sessionId)->first();

                if ($payment instanceof \StoredPXFusionPayment && $payment->DpsTxnRef) {

                    // Set the transaction status to processing 
                    $this->transaction->setStatus('Processing');

                    // Keep track of the payment status
                    $paymentSuccessful = false;
                    $storedPxPostPayment = false;

                    $paymentResponse = $this->paymentService->completeTransaction($payment->DpsTxnRef);

                    if ($paymentResponse instanceof PXPostPaymentResponse) {
                        // Store the payment response
                        $storedPxPostPayment = $this->storage->process($paymentResponse)[Backend::IDENTIFIER];
                        $paymentResponse->updateTransaction($this->transaction);
                        $paymentSuccessful = (bool) $paymentResponse->Success;
                    }

                    // store the transaction
                    $storedTransaction = $this->storage->process($this->transaction)[Backend::IDENTIFIER];
                    $payment->ParentID = $storedTransaction->ID;

                    if ($storedPxPostPayment && $storedTransaction) {
                        // set the parents of each object
                        $payment->PXPostPaymentID = $storedPxPostPayment->ID;
                        $payment->write();

                        $storedPxPostPayment->ParentID = $storedTransaction->ID;
                        $storedPxPostPayment->write();

                    } else {

                        $payment->write();

                        return [
                            'Success' => false
                        ];

                    }

                    return [
                        'Success' => $paymentSuccessful,
                        'Complete' => true,
                        'Data' => $paymentResponse
                    ];

                }

            }

        } elseif ($httpMethod == 'GET' && $request->param('ID') == 'check') {

            $paymentResponse = $this->paymentService->checkTransaction($request->getVar('sessionid'));

            $this->state->setByKey(self::IDENTIFIER . '.sessionid', $request->getVar('sessionid'));

            $this->storage->process($paymentResponse);

            if ($paymentResponse->StatusCode === 0) {
                return [
                    'Success' => true,
                    'Data' => $paymentResponse
                ];

            } else {
                return [
                    'Success' => false,
                    'CheckFailure' => true
                ];

            }

        }

        return [
            'Success' => false
        ];
    }

}
