<?php
declare(strict_types=1);

namespace App\PropertyAccessor;

use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * ExceptionPropertyAccessor - Accessing Exception object properties in clean way
 * @package  Enquiry Api
 * @author   Piotr Rybinski
 */
class ExceptionPropertyAccessor
{
    /**
     * Get Exception object properties
     * @param object $data
     * @return array
     */
    public function accessExceptionProperties($data)
    {
        $prop_acsr = PropertyAccess::createPropertyAccessor();

        $status_code = $prop_acsr->getValue($data, 'statusCode');
        return ['statusCode' => $status_code];
    }
}
