<?php

namespace Timahfouz\SvgSanitizer\Services;

class SvgCodeSanitizer
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Sanitize SVG code string.
     *
     * @param string|null $svg
     * @return string|null Returns sanitized SVG or null if invalid/dangerous
     */
    public function sanitize(?string $svg): ?string
    {
        if (empty($svg)) {
            return $svg;
        }

        // Check max length
        $maxLength = $this->config['max_code_length'] ?? 102400;
        if (strlen($svg) > $maxLength) {
            return null;
        }

        // First pass: Quick regex cleaning
        $svg = $this->quickClean($svg);

        // If it doesn't look like SVG, return as escaped string
        if (!preg_match('/<svg/i', $svg)) {
            // It's not SVG code, might be an icon class name
            if ($this->containsDangerousContent($svg)) {
                return null;
            }
            return htmlspecialchars($svg, ENT_QUOTES, 'UTF-8');
        }

        // Second pass: DOM-based cleaning for SVG
        $cleanedSvg = $this->domClean($svg);

        if ($cleanedSvg === null) {
            return null;
        }

        // Final validation
        if ($this->containsDangerousContent($cleanedSvg)) {
            return null;
        }

        return $cleanedSvg;
    }

    /**
     * Check if SVG code is safe without sanitizing.
     *
     * @param string|null $svg
     * @return bool
     */
    public function isSafe(?string $svg): bool
    {
        if (empty($svg)) {
            return true;
        }

        return !$this->containsDangerousContent($svg);
    }

    /**
     * Quick regex-based cleaning.
     */
    protected function quickClean(string $svg): string
    {
        // Remove event handlers
        $svg = preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']?/i', '', $svg);
        $svg = preg_replace('/\s+on\w+\s*=\s*[^\s>]*/i', '', $svg);

        // Remove dangerous URLs
        $svg = preg_replace('/javascript\s*:/i', 'removed:', $svg);
        $svg = preg_replace('/vbscript\s*:/i', 'removed:', $svg);

        // Remove script tags
        $svg = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $svg);
        $svg = preg_replace('/<script[^>]*\/?>/i', '', $svg);

        // Remove foreignObject
        $svg = preg_replace('/<foreignObject[^>]*>.*?<\/foreignObject>/is', '', $svg);

        return $svg;
    }

    /**
     * DOM-based cleaning for thorough sanitization.
     */
    protected function domClean(string $svg): ?string
    {
        libxml_use_internal_errors(true);

        $dom = new \DOMDocument();

        // Wrap in XML declaration if not present
        if (!preg_match('/^<\?xml/i', $svg)) {
            $svg = '<?xml version="1.0" encoding="UTF-8"?>' . $svg;
        }

        if (!@$dom->loadXML($svg, LIBXML_NOENT | LIBXML_DTDLOAD)) {
            libxml_clear_errors();
            return null;
        }

        libxml_clear_errors();

        // Remove dangerous elements
        $this->removeDangerousElements($dom);

        // Remove dangerous attributes
        $this->removeDangerousAttributes($dom);

        // Get the SVG element
        $svgElements = $dom->getElementsByTagName('svg');
        if ($svgElements->length === 0) {
            return null;
        }

        $result = $dom->saveXML($svgElements->item(0));

        return $result !== false ? $result : null;
    }

    /**
     * Remove dangerous elements from DOM.
     */
    protected function removeDangerousElements(\DOMDocument $dom): void
    {
        $dangerousElements = $this->config['dangerous_elements'] ?? [
            'script', 'foreignObject', 'handler', 'iframe', 'frame',
            'frameset', 'object', 'embed', 'import', 'include', 'base',
            'form', 'input', 'button', 'meta', 'link', 'applet',
        ];

        foreach ($dangerousElements as $tagName) {
            $elements = $dom->getElementsByTagName($tagName);
            $toRemove = [];

            foreach ($elements as $element) {
                $toRemove[] = $element;
            }

            foreach ($toRemove as $element) {
                if ($element->parentNode) {
                    $element->parentNode->removeChild($element);
                }
            }
        }
    }

    /**
     * Remove dangerous attributes from all elements.
     */
    protected function removeDangerousAttributes(\DOMDocument $dom): void
    {
        $dangerousAttrPatterns = $this->config['dangerous_attribute_patterns'] ?? ['/^on\w+$/i'];
        $dangerousValuePatterns = $this->config['dangerous_value_patterns'] ?? [
            '/javascript\s*:/i',
            '/vbscript\s*:/i',
            '/data\s*:\s*text\/html/i',
            '/expression\s*\(/i',
        ];

        $xpath = new \DOMXPath($dom);
        $allElements = $xpath->query('//*');

        foreach ($allElements as $element) {
            $attributesToRemove = [];

            foreach ($element->attributes as $attr) {
                $attrName = strtolower($attr->name);
                $attrValue = $attr->value;

                // Check attribute name
                foreach ($dangerousAttrPatterns as $pattern) {
                    if (preg_match($pattern, $attrName)) {
                        $attributesToRemove[] = $attr->name;
                        continue 2;
                    }
                }

                // Check attribute value
                foreach ($dangerousValuePatterns as $pattern) {
                    if (preg_match($pattern, $attrValue)) {
                        $attributesToRemove[] = $attr->name;
                        continue 2;
                    }
                }
            }

            foreach ($attributesToRemove as $attrName) {
                $element->removeAttribute($attrName);
            }
        }
    }

    /**
     * Check if content contains dangerous patterns.
     */
    protected function containsDangerousContent(string $content): bool
    {
        $patterns = $this->config['dangerous_patterns'] ?? [
            '/<script/i',
            '/\s+on\w+\s*=/i',
            '/javascript\s*:/i',
            '/vbscript\s*:/i',
            '/<foreignObject/i',
            '/<iframe/i',
            '/<handler/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }
}
