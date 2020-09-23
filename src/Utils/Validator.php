<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Utils;

use function Symfony\Component\String\u;
use SplFileInfo;
use Symfony\Component\Console\Exception\InvalidArgumentException;

/**
 * This class is used to provide an example of integrating simple classes as
 * services into a Symfony application.
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class Validator
{
    public function validateFirstname(?string $firstName): string
    {
        if (empty($firstName)) {
            throw new InvalidArgumentException('Поле не может быть пустым');
        }

        if (u($firstName)->trim()->length() > 30) {
            throw new InvalidArgumentException('Поле не может превышать 30 символов');
        }

        return $firstName;
    }

    public function validateLastName(?string $lastName): string
    {
        if (empty($lastName)) {
            throw new InvalidArgumentException('Поле не может быть пустым');
        }

        if (u($lastName)->trim()->length() > 100) {
            throw new InvalidArgumentException('Поле не может превышать 100 символов');
        }
        return $lastName;
    }

    public function validatePhone(?string $phone): string
    {
        if (empty($phone)) {
            throw new InvalidArgumentException('Поле не может быть пустым');
        }
        if (1 !== preg_match('/7\d{10}/', u($phone)->trim())) {
            throw new InvalidArgumentException('Номер телефона должен быть равен 11 символам с первой цифрой "7"');
        }
        return $phone;
    }
    public function validatePassword(?string $plainPassword): string
    {
        if (empty($plainPassword)) {
            throw new InvalidArgumentException('Поле не может быть пустым');
        }

        if (u($plainPassword)->trim()->length() < 6) {
            throw new InvalidArgumentException('The password must be at least 6 characters long.');
        }

        return $plainPassword;
    }

    public function validateEmail(?string $email): string
    {
        // if (empty($email)) {
        //     throw new InvalidArgumentException('The email can not be empty.');
        // }

        if (null === u($email)->indexOf('@')) {
            throw new InvalidArgumentException('The email should look like a real email.');
        }

        return $email;
    }

    public function validateCsvFile(?string $csv): string
    {
        if (empty($csv)) {
            throw new InvalidArgumentException('Поле не может быть пустым');
        }
        $info = new SplFileInfo($csv);
        if ($info->getExtension() !== 'csv') {
            throw new InvalidArgumentException('Файл должен быть в формате csv');
        }
        return $csv;
    }
}
