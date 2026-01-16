<?php

namespace Timahfouz\SvgSanitizer\Services;

use enshrined\svgSanitize\Sanitizer;
use Illuminate\Http\UploadedFile;
use Timahfouz\SvgSanitizer\Data\AllowedTags;
use Timahfouz\SvgSanitizer\Data\AllowedAttributes;

class SvgFileSanitizer
{
    protected array $config;
    protected Sanitizer $sanitizer;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->initializeSanitizer();
    }

    /**
     * Initialize the enshrined SVG sanitizer with config.
     */
    protected function initializeSanitizer(): void
    {
        $this->sanitizer = new Sanitizer();

        if ($this->config['remove_remote_references'] ?? true) {
            $this->sanitizer->removeRemoteReferences(true);
        }

        if ($this->config['remove_xml_tag'] ?? true) {
            $this->sanitizer->removeXMLTag(true);
        }

        // Set allowed tags and attributes
        $this->sanitizer->setAllowedTags(new AllowedTags($this->config['allowed_tags'] ?? []));
        $this->sanitizer->setAllowedAttrs(new AllowedAttributes($this->config['allowed_attributes'] ?? []));
    }

    /**
     * Sanitize an uploaded SVG file.
     *
     * @param UploadedFile $file
     * @return string|null Sanitized SVG content or null if invalid/dangerous
     */
    public function sanitize(UploadedFile $file): ?string
    {
        // Check file extension
        if (strtolower($file->getClientOriginalExtension()) !== 'svg') {
            return null;
        }

        // Check file size
        $maxSize = $this->config['max_file_size'] ?? 2097152;
        if ($file->getSize() > $maxSize) {
            return null;
        }

        // Read file content
        $content = file_get_contents($file->getRealPath());
        if ($content === false) {
            return null;
        }

        return $this->sanitizeContent($content);
    }

    /**
     * Sanitize SVG content string.
     *
     * @param string $content
     * @return string|null
     */
    public function sanitizeContent(string $content): ?string
    {
        // First pass: Use enshrined sanitizer
        $cleanSvg = $this->sanitizer->sanitize($content);

        if ($cleanSvg === false || empty($cleanSvg)) {
            return null;
        }

        // Second pass: Additional security checks
        $cleanSvg = $this->removeEventHandlers($cleanSvg);
        $cleanSvg = $this->removeJavaScriptUrls($cleanSvg);
        $cleanSvg = $this->removeDangerousElements($cleanSvg);

        // Final validation
        if (!$this->isContentSafe($cleanSvg)) {
            return null;
        }

        return $cleanSvg;
    }

    /**
     * Check if an uploaded SVG file is safe without sanitizing.
     *
     * @param UploadedFile $file
     * @return bool
     */
    public function isSafe(UploadedFile $file): bool
    {
        if (strtolower($file->getClientOriginalExtension()) !== 'svg') {
            return true; // Not an SVG, skip check
        }

        $content = file_get_contents($file->getRealPath());
        if ($content === false) {
            return false;
        }

        return $this->isContentSafe($content);
    }

    /**
     * Check if SVG content is safe.
     *
     * @param string $content
     * @return bool
     */
    public function isContentSafe(string $content): bool
    {
        $patterns = $this->config['dangerous_patterns'] ?? [];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Remove event handler attributes.
     */
    protected function removeEventHandlers(string $svg): string
    {
        $svg = preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']?/i', '', $svg);
        $svg = preg_replace('/\s+on\w+\s*=\s*[^\s>]*/i', '', $svg);

        return $svg;
    }

    /**
     * Remove JavaScript URLs.
     */
    protected function removeJavaScriptUrls(string $svg): string
    {
        $svg = preg_replace('/javascript\s*:/i', 'removed:', $svg);
        $svg = preg_replace('/vbscript\s*:/i', 'removed:', $svg);
        $svg = preg_replace('/data\s*:\s*(?!image\/(png|jpeg|jpg|gif|webp|svg\+xml))[^"\'>\s]*/i', 'removed:', $svg);

        return $svg;
    }

    /**
     * Remove dangerous elements.
     */
    protected function removeDangerousElements(string $svg): string
    {
        $dangerousPatterns = [
            '/<script[^>]*>.*?<\/script>/is',
            '/<script[^>]*\/>/is',
            '/<script[^>]*>/is',
            '/<foreignObject[^>]*>.*?<\/foreignObject>/is',
            '/<foreignObject[^>]*\/>/is',
            '/<iframe[^>]*>.*?<\/iframe>/is',
            '/<iframe[^>]*\/>/is',
            '/<object[^>]*>.*?<\/object>/is',
            '/<embed[^>]*\/?>/is',
            '/<handler[^>]*>.*?<\/handler>/is',
            '/<set[^>]*attributeName\s*=\s*["\']on\w+["\'][^>]*\/?>/is',
            '/<animate[^>]*attributeName\s*=\s*["\']on\w+["\'][^>]*\/?>/is',
        ];

        foreach ($dangerousPatterns as $pattern) {
            $svg = preg_replace($pattern, '', $svg);
        }

        return $svg;
    }
}
