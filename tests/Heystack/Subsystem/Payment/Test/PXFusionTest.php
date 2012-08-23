<?php

namespace Heystack\Subsystem\Payment\Test;

use Heystack\Subsystem\Payment\DPS\PXFusion\Service;

use Heystack\Subsystem\Payment\DPS\PXFusion\InputProcessor;

use Symfony\Component\EventDispatcher\EventDispatcher;

class PXFusionTest extends \PHPUnit_Framework_TestCase
{

    protected $paymentService;

    protected function setUp()
    {

        $this->paymentService = new Service(new EventDispatcher(), new TestTransaction());
        $this->paymentService->setTesting(true);

    }

    protected function tearDown()
    {

        $this->paymentService = null;

    }

    public function testConfig()
    {

        $message = null;

        try {

            $this->paymentService->setConfig(array());

        } catch (\Heystack\Subsystem\Core\Exception\ConfigurationException $e) {

            $message = $e->getMessage();

        }

        $this->assertNotEquals(null, $message);

        $message = null;

        try {

            $this->paymentService->setConfig(array(
                'Type' => 'Auth-Complete',
                'Username' => 'Test',
                'Password' => 'Test',
                'Wsdl' => 'http://test.com'
            ));

        } catch (\Heystack\Subsystem\Core\Exception\ConfigurationException $e) {

            $message = $e->getMessage();

        }

        $this->assertEquals(null, $message);

    }

    public function testReturnUrl()
    {

        $this->paymentService->setConfig(array(
            'Type' => 'Auth-Complete',
            'Username' => 'Test',
            'Password' => 'Test',
            'Wsdl' => 'http://test.com'
        ));

        $this->assertEquals(\Director::absoluteURL(\EcommerceInputController::$url_segment . '/process/' . InputProcessor::IDENTIFIER . '/auth'), $this->paymentService->getReturnUrl());

        $this->paymentService->setConfig(array(
            'Type' => 'Purchase',
            'Username' => 'Test',
            'Password' => 'Test'
        ));

        $this->assertEquals(\Director::absoluteURL(\EcommerceInputController::$url_segment . '/process/' . InputProcessor::IDENTIFIER . '/purchase'), $this->paymentService->getReturnUrl());

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

    public function testGetTransactionId()
    {

        $this->paymentService->setConfig(array(
            'Type' => Service::TYPE_PURCHASE,
            'Username' => 'HeydayPXFDev',
            'Password' => 'test1234'
        ));

        $this->assertInternalType('string', $this->paymentService->getTransactionId());

        $this->paymentService->setConfig(array(
            'Type' => Service::TYPE_AUTH_COMPLETE,
            'Username' => 'HeydayPXFDev',
            'Password' => 'test1234'
        ));

        $this->assertInternalType('string', $this->paymentService->getTransactionId());

    }

    public function testSetAdditionalConfig()
    {

        $this->paymentService->setAdditionalConfig(array(
            'txnData1' => 'Hello',
            'badKey' => 'bad'
        ));

        $this->assertEquals(array(
            'txnData1' => 'Hello'
        ), $this->paymentService->getAdditionalConfig());

        $allowedKeys = array_flip($this->paymentService->getAllowedAdditionalConfig());

        $this->paymentService->setAdditionalConfig($allowedKeys);

        $this->assertEquals($allowedKeys, $this->paymentService->getAdditionalConfig());

        $this->paymentService->setAllowedAdditionalConfig(array(
            'badKey'
        ));

        $this->paymentService->setAllowedAdditionalConfig(array(
            'badKey'
        ));

        $this->paymentService->setAdditionalConfig(array(
            'txnData1' => 'Hello',
            'badKey' => 'bad'
        ));

        $this->assertNotEquals(array(
            'badKey' => 'Hello'
        ), $this->paymentService->getAdditionalConfig());

        $this->assertEquals(array(
            'badKey' => 'bad'
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
            'Wsdl' => 'https://sec2.paymentexpress.com/pxf/pxf.svc?wsdl'
        ));

        $this->assertEquals('10.00', $this->paymentService->getAmount());

        $this->paymentService->setConfig(array(
            'Type' => Service::TYPE_AUTH_COMPLETE,
            'Username' => 'HeydayPXFDev',
            'Password' => 'test1234',
            'Wsdl' => 'https://sec2.paymentexpress.com/pxf/pxf.svc?wsdl'
        ));

        $this->assertEquals('1.00', $this->paymentService->getAmount());

    }

}
