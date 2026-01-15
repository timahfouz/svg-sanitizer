<?php

namespace YourVendor\SvgSanitizer\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use YourVendor\SvgSanitizer\Services\SvgFileSanitizer;

/**
 * Validates that an uploaded SVG file is safe.
 *
 * Usage in validation rules:
 *   'file' => ['file', 'mimes:svg', new SvgFileSafe()]
 * Or using the string shorthand:
 *   'file' => 'file|mimes:svg|svg_file_safe'
 */
class SvgFileSafe implements ValidationRule
{
    protected ?SvgFileSanitizer $sanitizer = null;

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param \Closure(string): void $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Only validate uploaded files
        if (!$value instanceof UploadedFile) {
            return;
        }

        // Only check SVG files
        $extension = strtolower($value->getClientOriginalExtension());
        $mimeType = $value->getMimeType();

        if ($extension !== 'svg' && $mimeType !== 'image/svg+xml') {
            return;
        }

        // Read file content
        $content = file_get_contents($value->getRealPath());

        if ($content === false) {
            $fail(__('The :attribute could not be read.', ['attribute' => $attribute]));
            return;
        }

        // Check for dangerous content
        if (!$this->getSanitizer()->isContentSafe($content)) {
            $fail(__('The :attribute contains potentially malicious content.', ['attribute' => $attribute]));
            return;
        }

        // Validate XML structure
        if (!$this->isValidXml($content)) {
            $fail(__('The :attribute is not a valid SVG file.', ['attribute' => $attribute]));
            return;
        }
    }

    /**
     * Get the sanitizer instance.
     */
    protected function getSanitizer(): SvgFileSanitizer
    {
        if ($this->sanitizer === null) {
            $this->sanitizer = app(SvgFileSanitizer::class);
        }

        return $this->sanitizer;
    }

    /**
     * Check if content is valid XML.
     */
    protected function isValidXml(string $content): bool
    {
        libxml_use_internal_errors(true);

        $dom = new \DOMDocument();
        $loaded = @$dom->loadXML($content, LIBXML_NOENT | LIBXML_DTDLOAD);

        libxml_clear_errors();

        return $loaded !== false;
    }
}
