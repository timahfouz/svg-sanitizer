<?php

namespace Timahfouz\SvgSanitizer\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Timahfouz\SvgSanitizer\Services\SvgCodeSanitizer;

/**
 * Validates that SVG code input (from textarea) is safe.
 *
 * Usage in validation rules:
 *   'icon' => ['nullable', 'string', new SvgCodeSafe()]
 * Or using the string shorthand:
 *   'icon' => 'nullable|string|svg_code_safe'
 */
class SvgCodeSafe implements ValidationRule
{
    protected ?SvgCodeSanitizer $sanitizer = null;

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param \Closure(string): void $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Skip if empty
        if (empty($value)) {
            return;
        }

        // Must be a string
        if (!is_string($value)) {
            $fail(__('validation.string', ['attribute' => $attribute]));
            return;
        }

        // Check max length
        $maxLength = config('svg-sanitizer.max_code_length', 102400);
        if (strlen($value) > $maxLength) {
            $fail(__('The :attribute may not be greater than :max characters.', [
                'attribute' => $attribute,
                'max' => $maxLength,
            ]));
            return;
        }

        // Check for dangerous content
        if (!$this->getSanitizer()->isSafe($value)) {
            $fail(__('The :attribute contains potentially unsafe content.', ['attribute' => $attribute]));
            return;
        }

        // If it looks like SVG, validate structure
        if (preg_match('/<svg/i', $value)) {
            if (!$this->isValidSvgStructure($value)) {
                $fail(__('The :attribute contains invalid SVG structure.', ['attribute' => $attribute]));
                return;
            }
        }
    }

    /**
     * Get the sanitizer instance.
     */
    protected function getSanitizer(): SvgCodeSanitizer
    {
        if ($this->sanitizer === null) {
            $this->sanitizer = app(SvgCodeSanitizer::class);
        }

        return $this->sanitizer;
    }

    /**
     * Check if the SVG has valid XML structure.
     */
    protected function isValidSvgStructure(string $svg): bool
    {
        libxml_use_internal_errors(true);

        $dom = new \DOMDocument();

        // Add XML declaration if missing
        if (!preg_match('/^<\?xml/i', $svg)) {
            $svg = '<?xml version="1.0" encoding="UTF-8"?>' . $svg;
        }

        $loaded = @$dom->loadXML($svg, LIBXML_NOENT | LIBXML_DTDLOAD);

        libxml_clear_errors();

        return $loaded !== false;
    }
}
