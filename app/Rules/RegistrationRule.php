<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidationValidator;

class RegistrationRule implements ValidationRule, DataAwareRule, ValidatorAwareRule
{

    private array $data;
    private ValidationValidator $validator;

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */

    public function setData(array $data): RegistrationRule
    {
        $this->data = $data;
        return $this;
    }

    public function setValidator(ValidationValidator $validator): RegistrationRule
    {
        $this->validator = $validator;
        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->data["username"] == $value) {
            $fail("$attribute must be different with username");
        }
    }
}
