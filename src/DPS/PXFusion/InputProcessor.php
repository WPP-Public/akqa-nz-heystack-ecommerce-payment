<?php

namespace Heystack\Subsystem\Payment\DPS\PXFusion;

use Heystack\Subsystem\Core\DataObjectHandler\DataObjectHandlerInterface;
use Heystack\Subsystem\Core\Identifier\Identifier;
use Heystack\Subsystem\Core\Input\ProcessorInterface;

use Heystack\Subsystem\Core\Storage\Storage;
use Heystack\Subsystem\Core\Storage\Backends\SilverStripeOrm\Backend;

use Heystack\Subsystem\Core\State\State;

use Heystack\Subsystem\Ecommerce\Transaction\Interfaces\TransactionInterface;

use Heystack\Subsystem\Payment\DPS\PXPost\PaymentResponse as PXPostPaymentResponse;

/**
 * Class InputProcessor
 * @package Heystack\Subsystem\Payment\DPS\PXFusion
 */
class InputProcessor implements ProcessorInterface
{

    /**
     *
     */
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

    /**
     * @var \Heystack\Subsystem\Core\DataObjectHandler\DataObjectHandlerInterface
     */
    protected $dataObjectHandler;

    /**
     * @param PaymentServiceInterface $paymentService
     * @param Storage $storage
     * @param State $state
     * @param TransactionInterface $transaction
     * @param DataObjectHandlerInterface $dataObjectHandler
     */
    public function __construct(
        PaymentServiceInterface $paymentService,
        Storage $storage,
        State $state,
        TransactionInterface $transaction,
        DataObjectHandlerInterface $dataObjectHandler
    ) {
        $this->paymentService = $paymentService;
        $this->storage = $storage;
        $this->state = $state;
        $this->transaction = $transaction;
        $this->dataObjectHandler = $dataObjectHandler;
    }
    /**
     * @return \Heystack\Subsystem\Core\Identifier\Identifier
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

                $payment = $this->dataObjectHandler->getDataObject('StoredPXFusionPayment', "SessionId = '{$sessionId}'");

                if ($payment instanceof \StoredPXFusionPayment && $payment->DpsTxnRef) {

                    $this->transaction->setStatus('Processing');

                    //make sure completeTrans does a try catch type thing returning false on badness
                    $paymentResponse = $this->paymentService->completeTransaction($payment->DpsTxnRef);

                    $results = false;

                    if ($paymentResponse instanceof PXPostPaymentResponse) {

                        $results = $this->storage->process($paymentResponse);

                        $paymentResponse->updateTransaction($this->transaction);

                    }

                    // store the transaction
                    $transactionResults = $this->storage->process($this->transaction);
                    $payment->ParentID = $transactionResults[Backend::IDENTIFIER]->ID;

                    if ($results && isset($results[Backend::IDENTIFIER]) && isset($transactionResults[Backend::IDENTIFIER])) {

                        // get the actual transaction and payment
                        $storedTransaction = $transactionResults[Backend::IDENTIFIER];
                        $pxPostPayment = $results[Backend::IDENTIFIER];

                        // set the parents of each object

                        $payment->PXPostPaymentID = $pxPostPayment->ID;
                        $payment->write();

                        $pxPostPayment->ParentID = $storedTransaction->ID;
                        $pxPostPayment->write();

                    } else {

                        $payment->write();

                        return [
                            'Success' => false
                        ];

                    }

                }

                return [
                    'Success' => true,
                    'Complete' => true,
                    'Data' => $paymentResponse
                ];

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