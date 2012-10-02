<?php

namespace Heystack\Subsystem\Payment\DPS;

/**
 * Holds constants corresponding to the services defined in the dps_services.yml file
 *
 * @copyright  Heyday
 * @author Cam Spiers
 * @package Ecommerce-Payment
 */
final class Services
{

    const PXFUSION_SERVICE = 'pxfusion_service';
    const PXFUSION_INPUT_PROCESSOR = 'pxfusion_input_processor';
    const PXFUSION_OUTPUT_PROCESSOR = 'pxfusion_output_processor';
    const PXFUSION_PAYMENT_RESPONSE = 'pxfusion_payment_response';

    const PXPOST_SERVICE = 'pxpost_service';
    const PXPOST_INPUT_PROCESSOR = 'pxpost_input_processor';
    const PXPOST_OUTPUT_PROCESSOR = 'pxpost_output_processor';
    const PXPOST_PAYMENT_RESPONSE = 'pxpost_payment_response';

}
