<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class StrongPassword implements Rule
{
    public function passes($attribute, $value)
    {
        if (strlen($value) < 8) {
            return false;
        }

        if ($value !== trim($value)) {
            return false;
        }

        $complexityCount = 0;

        if (preg_match('/[A-Z]/', $value)) {
            $complexityCount++;
        }

        if (preg_match('/[a-z]/', $value)) {
            $complexityCount++;
        }

        if (preg_match('/[0-9]/', $value)) {
            $complexityCount++;
        }

        if (preg_match('/[!@#$%^&*()_+=\-{}\[\]|:;"\'<>,.?\/~`]/', $value)) {
            $complexityCount++;
        }

        return $complexityCount >= 3;
    }

    public function message()
    {
        return 'Mật khẩu phải có ít nhất 8 ký tự và bao gồm ít nhất 3 trong 4 yếu tố: chữ in hoa, chữ thường, chữ số, ký tự đặc biệt.';
    }
}
