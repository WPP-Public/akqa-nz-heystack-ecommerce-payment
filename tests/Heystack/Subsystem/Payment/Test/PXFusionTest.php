<?php

namespace Heystack\Subsystem\Payment\Test;

use Heystack\Subsystem\Payment\DPS\PXFusion\InputProcessor;
use Heystack\Subsystem\Payment\DPS\PXFusion\Service;

class PXFusionTest extends \PHPUnit_Framework_TestCase
{

    protected $paymentService;

    protected function setUp()
    {
        $eventDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcher');

        $currencyService = $this->getMock('Heystack\Subsystem\Ecommerce\Currency\Interfaces\CurrencyServiceInterface');
        $currencyService->expects($this->any())
            ->method('getActiveCurrencyCode')
            ->will($this->returnValue('NZD'));

        $transaction = $this->getMock('Heystack\Subsystem\Ecommerce\Transaction\Interfaces\TransactionInterface');
        $transaction->expects($this->any())
            ->method('getTotal')
            ->will($this->returnValue(10));

        $this->paymentService = new Service(
            $eventDispatcher,
            $transaction,
            $currencyService
        );

        $this->paymentService->setTestingMode(true);
    }

    protected function tearDown()
    {

        $this->paymentService = null;

    }

    public function testConfig()
    {
        $this->assertEquals(3, count($this->paymentService->setConfig(array())));
        $this->assertTrue($this->paymentService->setConfig(array(
            'Type' => 'Auth-Complete',
            'Username' => 'Test',
            'Password' => 'Test'
        )));

    }

    public function testReturnUrl()
    {

        $this->paymentService->setConfig(array(
            'Type' => 'Auth-Complete',
            'Username' => 'Test',
            'Password' => 'Test'
        ));

        $this->assertEquals('http://localhost/ecommerce/input/process/' . InputProcessor::IDENTIFIER . '/check/auth', $this->paymentService->getReturnUrl());

        $this->paymentService->setConfig(array(
            'Type' => 'Purchase',
            'Username' => 'Test',
            'Password' => 'Test'
        ));

        $this->assertEquals('http://localhost/ecommerce/input/process/' . InputProcessor::IDENTIFIER . '/check/purchase', $this->paymentService->getReturnUrl());

    }

    public function testSetGetType()
    {

        $this->paymentService->setConfig(array(
            'Type' => Service::TYPE_PURCHASE,
            'Username' => 'Test',
            'Password' => 'Test'
        ));

        $this->assertEquals('Purchase', $this->paymentService->getType());

        $this->paymentService->setType(Service::TYPE_AUTH_COMPLETE);

        $this->assertEquals('Auth-Complete', $this->paymentService->getType());

    }

    public function testTxnType()
    {

        $this->paymentService->setConfig(array(
            'Type' => Service::TYPE_PURCHASE,
            'Username' => 'Test',
            'Password' => 'Test'
        ));

        $this->assertEquals('Purchase', $this->paymentService->getTxnType());

        $test = null;

        try {

            $this->paymentService->setStage('Complete');

        } catch (\Heystack\Subsystem\Core\Exception\ConfigurationException $e) {

            $test = $e->getMessage();

        }

        $this->assertNotNull($test);

        $this->paymentService->setConfig(array(
            'Type' => Service::TYPE_AUTH_COMPLETE,
            'Username' => 'Test',
            'Password' => 'Test'
        ));

        $this->assertEquals('Auth', $this->paymentService->getTxnType());

        $this->paymentService->setStage('Complete');

        $this->assertEquals('Purchase', $this->paymentService->getTxnType());

    }
    /**
     * @large
     */
    public function testGetTransactionIdPurchase()
    {

        $this->paymentService->setConfig(array(
            'Type' => Service::TYPE_PURCHASE,
            'Username' => 'HeydayPXFDev',
            'Password' => 'test1234'
        ));

        $this->assertInternalType('string', $this->paymentService->getTransactionId());

    }

    public function testGetTransactionIdAuth()
    {
        $this->paymentService->setConfig(array(
            'Type' => Service::TYPE_AUTH_COMPLETE,
            'Username' => 'HeydayPXFDev',
            'Password' => 'test1234'
        ));

        $this->assertInternalType('string', $this->paymentService->getTransactionId());
    }
    /**
     * @large
     */
    public function testGetTransaction()
    {

        $this->paymentService->setConfig(array(
            'Type' => Service::TYPE_PURCHASE,
            'Username' => 'HeydayPXFDev',
            'Password' => 'test1234'
        ));

        $id = $this->paymentService->getTransactionId();

        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-Type: multipart/form-data; boundary=----TestPXFusionBoundry",
                'content' => <<<REQUEST
------TestPXFusionBoundry
Content-Disposition: form-data; name="SessionId"

$id
------TestPXFusionBoundry
Content-Disposition: form-data; name="CardNumber"

4111111111111111
------TestPXFusionBoundry
Content-Disposition: form-data; name="ExpiryMonth"

12
------TestPXFusionBoundry
Content-Disposition: form-data; name="ExpiryYear"

12
------TestPXFusionBoundry
Content-Disposition: form-data; name="Cvc2"

123
------TestPXFusionBoundry
Content-Disposition: form-data; name="CardHolderName"

Joe Bloggs
------TestPXFusionBoundry--
REQUEST

            )
        ));

        $fp = @fopen('https://sec.paymentexpress.com/pxmi3/pxfusionauth', 'rb', false, $context);

        $response = @stream_get_contents($fp);

        $this->paymentService->checkTransaction($id);

    }

    public function testSetAdditionalConfig()
    {
        $this->assertCount(1, $this->paymentService->setAdditionalConfig(array(
            'txnData1' => 'Hello',
            'badKey' => 'bad'
        )));

        $this->paymentService->setAdditionalConfig(array(
            'txnData1' => 'Hello'
        ));

        $this->assertEquals(array(
            'txnData1' => 'Hello'
        ), $this->paymentService->getAdditionalConfig());
    }

    public function testSetGetAuthAmount()
    {
        $this->assertEquals(1, $this->paymentService->getAuthAmount());

        $this->paymentService->setAuthAmount(10);

        $this->assertEquals(10, $this->paymentService->getAuthAmount());
    }

    public function testGetAmount()
    {
        $this->paymentService->setConfig(array(
            'Type' => Service::TYPE_PURCHASE,
            'Username' => 'HeydayPXFDev',
            'Password' => 'test1234',
        ));

        $this->assertEquals('10.00', $this->paymentService->getAmount());

        $this->paymentService->setConfig(array(
            'Type' => Service::TYPE_AUTH_COMPLETE,
            'Username' => 'HeydayPXFDev',
            'Password' => 'test1234'
        ));

        $this->assertEquals('1.00', $this->paymentService->getAmount());
    }

}
