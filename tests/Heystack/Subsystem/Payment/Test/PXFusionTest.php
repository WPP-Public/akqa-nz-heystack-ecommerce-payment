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
        
        $this->paymentService = new Service('TestPXFusionPayment', new TestTransaction(), new EventDispatcher());
        
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
        
        $message = null;
        
        try {
        
            $this->paymentService->setConfig(array(
                'Type' => 'Auth-Complete',
                'Username' => 'Test',
                'Password' => 'Test',
                'Wsdl' => 'test.com'
            ));

        } catch (\Heystack\Subsystem\Core\Exception\ConfigurationException $e) {
            
            $message = $e->getMessage();
            
        }
        
        $this->assertNotEquals(null, $message);
        
        $message = null;
        
        try {
        
            $this->paymentService->setConfig(array(
                'Type' => 'Bob',
                'Username' => 'Test',
                'Password' => 'Test',
                'Wsdl' => 'test.com'
            ));

        } catch (\Heystack\Subsystem\Core\Exception\ConfigurationException $e) {
            
            $message = $e->getMessage();
            
        }
        
        $this->assertNotEquals(null, $message);
        
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
            'Password' => 'Test',
            'Wsdl' => 'http://test.com'
        ));
        
        $this->assertEquals(\Director::absoluteURL(\EcommerceInputController::$url_segment . '/process/' . InputProcessor::IDENTIFIER . '/purchase'), $this->paymentService->getReturnUrl());
        
    }
    
    public function testSetGetType()
    {
        
        $this->paymentService->setConfig(array(
            'Type' => 'Purchase',
            'Username' => 'Test',
            'Password' => 'Test',
            'Wsdl' => 'http://test.com'
        ));
        
        $this->assertEquals('Purchase', $this->paymentService->getType());
        
        $this->paymentService->setType('Auth-Complete');
        
        $this->assertEquals('Auth-Complete', $this->paymentService->getType());        
        
    }
    
    public function testGetTransactionId()
    {
        
        $this->paymentService->setConfig(array(
            'Type' => 'Purchase',
            'Username' => 'HeydayPXFDev',
            'Password' => 'test1234',
            'Wsdl' => 'https://sec2.paymentexpress.com/pxf/pxf.svc?wsdl'
        ));
        
        $this->assertInternalType('string', $this->paymentService->getTransactionId());
        
    }
    
}