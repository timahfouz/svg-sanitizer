<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SVG Sanitization Settings
    |--------------------------------------------------------------------------
    |
    | Configure how SVG files and code are sanitized to prevent XSS attacks.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Dangerous Elements
    |--------------------------------------------------------------------------
    |
    | Elements that will be completely removed from SVG content.
    | These elements can execute JavaScript or load external content.
    |
    */
    'dangerous_elements' => [
        'script',
        'foreignObject',
        'handler',
        'iframe',
        'frame',
        'frameset',
        'object',
        'embed',
        'import',
        'include',
        'base',
        'form',
        'input',
        'button',
        'meta',
        'link',
        'applet',
        'audio',
        'video',
        'source',
        'track',
    ],

    /*
    |--------------------------------------------------------------------------
    | Dangerous Attribute Patterns
    |--------------------------------------------------------------------------
    |
    | Regex patterns for attributes that should be removed.
    | Event handlers like onclick, onload, onerror are blocked by default.
    |
    */
    'dangerous_attribute_patterns' => [
        '/^on\w+$/i',  // All event handlers
    ],

    /*
    |--------------------------------------------------------------------------
    | Dangerous Value Patterns
    |--------------------------------------------------------------------------
    |
    | Regex patterns for attribute values that indicate malicious content.
    |
    */
    'dangerous_value_patterns' => [
        '/javascript\s*:/i',
        '/vbscript\s*:/i',
        '/livescript\s*:/i',
        '/data\s*:\s*text\/html/i',
        '/data\s*:\s*application\/(javascript|ecmascript|x-javascript)/i',
        '/expression\s*\(/i',
        '/behavior\s*:/i',
        '/-moz-binding\s*:/i',
    ],

    /*
    |--------------------------------------------------------------------------
    | Dangerous Content Patterns
    |--------------------------------------------------------------------------
    |
    | Regex patterns to detect dangerous content in SVG files/code.
    | Used for quick validation before full sanitization.
    |
    */
    'dangerous_patterns' => [
        // Script elements
        '/<script/i',
        '/<\/script/i',

        // Event handlers
        '/\s+on\w+\s*=/i',

        // JavaScript/VBScript URLs
        '/javascript\s*:/i',
        '/vbscript\s*:/i',
        '/livescript\s*:/i',

        // Data URLs with scripts
        '/data\s*:\s*text\/html/i',
        '/data\s*:\s*application\/javascript/i',
        '/data\s*:\s*application\/x-javascript/i',
        '/data\s*:\s*application\/ecmascript/i',

        // Foreign objects
        '/<foreignObject/i',
        '/<\/foreignObject/i',

        // Handler element
        '/<handler/i',

        // Iframe/frame
        '/<iframe/i',
        '/<frame/i',
        '/<frameset/i',

        // Object/Embed
        '/<object/i',
        '/<embed/i',

        // Import/Include
        '/<import/i',
        '/<include/i',

        // Base tag
        '/<base\s/i',

        // Form elements
        '/<form/i',
        '/<input/i',
        '/<button/i',

        // Meta
        '/<meta/i',

        // Link with import
        '/<link[^>]*import/i',

        // SVG animation with dangerous attributes
        '/<set[^>]*attributeName\s*=\s*["\']?\s*on/i',
        '/<animate[^>]*attributeName\s*=\s*["\']?\s*on/i',

        // Entity references (XXE)
        '/<!ENTITY/i',
        '/<!DOCTYPE[^>]*\[/i',

        // CSS expressions
        '/expression\s*\(/i',
        '/behavior\s*:/i',
        '/-moz-binding\s*:/i',

        // External references
        '/<use[^>]*href\s*=\s*["\']?\s*http/i',
        '/<use[^>]*xlink:href\s*=\s*["\']?\s*http/i',
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Tags
    |--------------------------------------------------------------------------
    |
    | Whitelist of SVG tags that are considered safe.
    | Only these tags will be preserved after sanitization.
    |
    */
    'allowed_tags' => [
        // Root element
        'svg',

        // Container elements
        'g',
        'defs',
        'symbol',
        'marker',
        'clipPath',
        'mask',
        'pattern',

        // Shape elements
        'circle',
        'ellipse',
        'line',
        'path',
        'polygon',
        'polyline',
        'rect',

        // Text elements
        'text',
        'tspan',
        'textPath',

        // Gradient elements
        'linearGradient',
        'radialGradient',
        'stop',

        // Filter elements
        'filter',
        'feBlend',
        'feColorMatrix',
        'feComponentTransfer',
        'feComposite',
        'feConvolveMatrix',
        'feDiffuseLighting',
        'feDisplacementMap',
        'feDistantLight',
        'feFlood',
        'feFuncA',
        'feFuncB',
        'feFuncG',
        'feFuncR',
        'feGaussianBlur',
        'feImage',
        'feMerge',
        'feMergeNode',
        'feMorphology',
        'feOffset',
        'fePointLight',
        'feSpecularLighting',
        'feSpotLight',
        'feTile',
        'feTurbulence',

        // Descriptive elements
        'title',
        'desc',
        'metadata',

        // Other safe elements
        'image',
        'style',
        'switch',
        'view',
        'use', // Internal references only
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Attributes
    |--------------------------------------------------------------------------
    |
    | Whitelist of SVG attributes that are considered safe.
    | Only these attributes will be preserved after sanitization.
    |
    */
    'allowed_attributes' => [
        // Core attributes
        'id',
        'class',
        'style',
        'lang',
        'tabindex',

        // Presentation attributes
        'fill',
        'fill-opacity',
        'fill-rule',
        'stroke',
        'stroke-dasharray',
        'stroke-dashoffset',
        'stroke-linecap',
        'stroke-linejoin',
        'stroke-miterlimit',
        'stroke-opacity',
        'stroke-width',
        'color',
        'opacity',
        'transform',
        'transform-origin',

        // Geometry attributes
        'x',
        'y',
        'x1',
        'y1',
        'x2',
        'y2',
        'cx',
        'cy',
        'r',
        'rx',
        'ry',
        'width',
        'height',
        'd',
        'points',
        'pathLength',

        // ViewBox
        'viewBox',
        'preserveAspectRatio',

        // Gradient attributes
        'gradientUnits',
        'gradientTransform',
        'spreadMethod',
        'fx',
        'fy',
        'offset',
        'stop-color',
        'stop-opacity',

        // Filter attributes
        'filterUnits',
        'primitiveUnits',
        'in',
        'in2',
        'result',
        'mode',
        'type',
        'values',
        'stdDeviation',
        'dx',
        'dy',

        // Text attributes
        'font-family',
        'font-size',
        'font-weight',
        'font-style',
        'text-anchor',
        'text-decoration',
        'textLength',
        'lengthAdjust',

        // Other safe attributes
        'clip-path',
        'clip-rule',
        'mask',
        'filter',
        'marker-start',
        'marker-mid',
        'marker-end',
        'visibility',
        'display',
        'overflow',

        // Namespace attributes
        'xmlns',
        'xmlns:svg',
        'xmlns:xlink',
        'version',

        // Aria attributes
        'role',
        'aria-label',
        'aria-labelledby',
        'aria-describedby',
        'aria-hidden',
    ],

    /*
    |--------------------------------------------------------------------------
    | Remove Remote References
    |--------------------------------------------------------------------------
    |
    | Whether to remove references to external resources.
    | Recommended to keep true for security.
    |
    */
    'remove_remote_references' => true,

    /*
    |--------------------------------------------------------------------------
    | Remove XML Tag
    |--------------------------------------------------------------------------
    |
    | Whether to remove the XML declaration tag.
    |
    */
    'remove_xml_tag' => true,

    /*
    |--------------------------------------------------------------------------
    | Max File Size
    |--------------------------------------------------------------------------
    |
    | Maximum allowed SVG file size in bytes. Default: 2MB
    |
    */
    'max_file_size' => 2097152,

    /*
    |--------------------------------------------------------------------------
    | Max Code Length
    |--------------------------------------------------------------------------
    |
    | Maximum allowed SVG code length in characters. Default: 100KB
    |
    */
    'max_code_length' => 102400,

];
