<?php

namespace Heystack\Subsystem\Payment\Test;

use Heystack\Subsystem\Payment\DPS\PXPost\Service;

use Heystack\Subsystem\Payment\DPS\PXPost\InputProcessor;

use Symfony\Component\EventDispatcher\EventDispatcher;

class PXPostTest extends \PHPUnit_Framework_TestCase
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

    public function testPurchase()
    {

        $this->paymentService->setConfig(array(

        ));

    }


}
