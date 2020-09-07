<?php
declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
* RequestValidator - validate request
* @package  Enquiry Api
* @author   Piotr Rybinski
*/
class RequestValidator
{
    /**
     * Validate name
     * @param $name
     * @return string
     */
    public function validateName(string $name): void
    {
        if (empty($name)) {
            throw new HttpException(400, 'Name cannot be null');
        }

        if (strlen($name) < 3) {
            throw new HttpException(400, 'Name must be minimum 3 characters');
        }
    }

    /**
     * Validate name
     * @param $name
     * @return string
     */
    public function validateMobile(string $mobile): void
    {
        if (empty($mobile)) {
            throw new HttpException(400, 'Mobile cannot be null');
        }

        if (strlen($mobile) < 11) {
            throw new HttpException(400, 'Mobile must be minimum 11 characters');
        }
    }

    /**
     * Validate name
     * @param $name
     * @return string
     */
    public function validateArivalDate(string $arrival_date): void
    {
        if (empty($arrival_date)) {
            throw new HttpException(400, 'Name cannot be null');
        }

        if (strlen($arrival_date) < 3) {
            throw new HttpException(400, 'Name must be minimum 3 characters');
        }

        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $arrival_date)) {
            throw new HttpException(400, 'Please provide a valid date in format YYYY-mm-dd');
        }
    }

    /**
     * Validate name
     * @param $name
     * @return string
     */
    public function validateAirport(string $airport): void
    {
        if (empty($airport)) {
            throw new HttpException(400, 'Airport cannot be null');
        }
    }

    /**
     * Validate name
     * @param $name
     * @return string
     */
    public function validateFlightNumber(string $flight_number): void
    {
        if (empty($flight_number)) {
            throw new HttpException(400, 'Flight number cannot be null');
        }

        /** @internal - flight number can hace 3 characters, eg BA1 */
        if (strlen($flight_number) < 3) {
            throw new HttpException(400, 'Name must be minimum 3 characters');
        }
    }

}
