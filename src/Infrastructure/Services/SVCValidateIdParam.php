<?php

namespace App\Infrastructure\Services;

class SVCValidateIdParam{

    public function validate(string $id): int
    {
        if (!is_numeric($id) || !ctype_digit($id)) {
            throw new \InvalidArgumentException('Invalid ID. ID must be a positive integer');
        }

        $id = (int) $id;

        if ($id <= 0) {
            throw new \InvalidArgumentException('Invalid ID. ID must be a positive number');
        }

        return $id;
    }

}
