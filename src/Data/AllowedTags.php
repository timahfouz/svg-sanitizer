<?php

namespace Timahfouz\SvgSanitizer\Data;

use enshrined\svgSanitize\data\TagInterface;

class AllowedTags implements TagInterface
{
    protected array $tags;

    public function __construct(array $tags = [])
    {
        $this->tags = !empty($tags) ? $tags : $this->getDefaultTags();
    }

    /**
     * Returns an array of allowed tags.
     *
     * @return array
     */
    public static function getTags(): array
    {
        return (new static())->tags;
    }

    /**
     * Get the default allowed tags.
     *
     * @return array
     */
    protected function getDefaultTags(): array
    {
        return [
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

            // Other
            'image',
            'style',
            'switch',
            'view',
            'use',
        ];
    }
}
