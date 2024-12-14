<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateBase64Image implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        // Check if the value is a valid base64 string
        if (!preg_match('/^data:image\/(\w+);base64,/', $value)) {
            $fail('Value is not a valid base_64 string');
        }

        // Decode the base64 string
        $imageData = base64_decode(preg_replace('/^data:image\/(\w+);base64,/', '', $value));

        // Check if the image data is not empty
        if (empty($imageData)) {
            $fail('Image data is empty');
        }

        // Check if the decoded image data is a valid image
        $imageInfo = getimagesizefromstring($imageData);
        if (!$imageInfo) {
            $fail('Value is not a valid image');
        }

        // Check if the image type is PNG or JPEG
        if (!in_array($imageInfo[2], [IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_WEBP])) {
            $fail('Image must be png, jpg, jpeg, or webp format');
        }

        // Check if the image size is within the specified limit (in bytes)
        $maxSize = $parameters[0] ?? 5242880; // Default to 5MB
        if (strlen($imageData) > $maxSize) {
            $fail('Maximum Size For Image is ' . ($maxSize / (1024 * 1024)));
        }

    }
}
