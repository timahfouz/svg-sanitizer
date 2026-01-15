<?php

namespace YourVendor\SvgSanitizer;

use Illuminate\Http\UploadedFile;
use YourVendor\SvgSanitizer\Services\SvgFileSanitizer;
use YourVendor\SvgSanitizer\Services\SvgCodeSanitizer;

class SvgSanitizer
{
    protected SvgFileSanitizer $fileSanitizer;
    protected SvgCodeSanitizer $codeSanitizer;

    public function __construct(SvgFileSanitizer $fileSanitizer, SvgCodeSanitizer $codeSanitizer)
    {
        $this->fileSanitizer = $fileSanitizer;
        $this->codeSanitizer = $codeSanitizer;
    }

    /**
     * Sanitize an SVG file upload.
     *
     * @param UploadedFile $file
     * @return string|null Sanitized SVG content or null if invalid/dangerous
     */
    public function sanitizeFile(UploadedFile $file): ?string
    {
        return $this->fileSanitizer->sanitize($file);
    }

    /**
     * Sanitize SVG code string.
     *
     * @param string|null $svg
     * @return string|null Sanitized SVG or null if invalid/dangerous
     */
    public function sanitizeCode(?string $svg): ?string
    {
        return $this->codeSanitizer->sanitize($svg);
    }

    /**
     * Check if an SVG file is safe (without sanitizing).
     *
     * @param UploadedFile $file
     * @return bool
     */
    public function isFileSafe(UploadedFile $file): bool
    {
        return $this->fileSanitizer->isSafe($file);
    }

    /**
     * Check if SVG code is safe (without sanitizing).
     *
     * @param string|null $svg
     * @return bool
     */
    public function isCodeSafe(?string $svg): bool
    {
        return $this->codeSanitizer->isSafe($svg);
    }

    /**
     * Get the file sanitizer instance.
     *
     * @return SvgFileSanitizer
     */
    public function file(): SvgFileSanitizer
    {
        return $this->fileSanitizer;
    }

    /**
     * Get the code sanitizer instance.
     *
     * @return SvgCodeSanitizer
     */
    public function code(): SvgCodeSanitizer
    {
        return $this->codeSanitizer;
    }
}
