<?php

namespace App\Services\Phone;

use InvalidArgumentException;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;

class RussianPhoneService
{
    private PhoneNumberUtil $phoneUtil;

    public function __construct()
    {
        $this->phoneUtil = PhoneNumberUtil::getInstance();
    }

    protected function process(string $rawPhone): array
    {
        try {
            $phoneNumber = $this->phoneUtil->parse($rawPhone, 'RU');

            if (!$this->phoneUtil->isValidNumber($phoneNumber))
            {
                throw new InvalidArgumentException('Неверный российский номер телефона');
            }

            return [
                'raw' => $rawPhone,
                'e164' => $this->phoneUtil->format($phoneNumber, PhoneNumberFormat::E164),
                'international' => $this->phoneUtil->format($phoneNumber, PhoneNumberFormat::INTERNATIONAL),
                'national' => $this->phoneUtil->format($phoneNumber, PhoneNumberFormat::NATIONAL),
                'is_valid' => true,
                'is_mobile' => $this->phoneUtil->getNumberType($phoneNumber) === PhoneNumberType::MOBILE,
            ];
        }
        catch (NumberParseException $e)
        {
            throw new InvalidArgumentException('Ошибка обработки номера: ' . $e->getMessage());
        }
    }

    public function phoneNumberToE164(string $rawPhone)
    {
        return $this->process($rawPhone)['e164'];
    }
}
