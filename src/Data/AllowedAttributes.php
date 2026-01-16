<?php

namespace Timahfouz\SvgSanitizer\Data;

use enshrined\svgSanitize\data\AttributeInterface;

class AllowedAttributes implements AttributeInterface
{
    protected array $attributes;

    public function __construct(array $attributes = [])
    {
        $this->attributes = !empty($attributes) ? $attributes : $this->getDefaultAttributes();
    }

    /**
     * Returns an array of allowed attributes.
     *
     * @return array
     */
    public static function getAttributes(): array
    {
        return (new static())->attributes;
    }

    /**
     * Get the default allowed attributes.
     *
     * @return array
     */
    protected function getDefaultAttributes(): array
    {
        return [
            // Core attributes
            'id',
            'class',
            'style',
            'lang',
            'tabindex',

            // Presentation attributes
            'alignment-baseline',
            'baseline-shift',
            'clip',
            'clip-path',
            'clip-rule',
            'color',
            'color-interpolation',
            'color-interpolation-filters',
            'cursor',
            'direction',
            'display',
            'dominant-baseline',
            'fill',
            'fill-opacity',
            'fill-rule',
            'filter',
            'flood-color',
            'flood-opacity',
            'font-family',
            'font-size',
            'font-size-adjust',
            'font-stretch',
            'font-style',
            'font-variant',
            'font-weight',
            'glyph-orientation-horizontal',
            'glyph-orientation-vertical',
            'image-rendering',
            'letter-spacing',
            'lighting-color',
            'marker-end',
            'marker-mid',
            'marker-start',
            'mask',
            'opacity',
            'overflow',
            'paint-order',
            'pointer-events',
            'shape-rendering',
            'stop-color',
            'stop-opacity',
            'stroke',
            'stroke-dasharray',
            'stroke-dashoffset',
            'stroke-linecap',
            'stroke-linejoin',
            'stroke-miterlimit',
            'stroke-opacity',
            'stroke-width',
            'text-anchor',
            'text-decoration',
            'text-rendering',
            'transform',
            'transform-origin',
            'unicode-bidi',
            'vector-effect',
            'visibility',
            'word-spacing',
            'writing-mode',

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

            // ViewBox and viewport
            'viewBox',
            'preserveAspectRatio',

            // Gradient attributes
            'gradientUnits',
            'gradientTransform',
            'spreadMethod',
            'fx',
            'fy',
            'offset',

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
            'k1',
            'k2',
            'k3',
            'k4',
            'operator',
            'scale',
            'xChannelSelector',
            'yChannelSelector',
            'baseFrequency',
            'numOctaves',
            'seed',
            'stitchTiles',
            'surfaceScale',
            'diffuseConstant',
            'specularConstant',
            'specularExponent',
            'kernelMatrix',
            'order',
            'divisor',
            'bias',
            'targetX',
            'targetY',
            'edgeMode',
            'kernelUnitLength',
            'preserveAlpha',
            'radius',
            'azimuth',
            'elevation',
            'pointsAtX',
            'pointsAtY',
            'pointsAtZ',
            'limitingConeAngle',

            // Clip/Mask/Pattern attributes
            'clipPathUnits',
            'maskUnits',
            'maskContentUnits',
            'patternUnits',
            'patternContentUnits',
            'patternTransform',

            // Marker attributes
            'markerWidth',
            'markerHeight',
            'markerUnits',
            'orient',
            'refX',
            'refY',

            // Text attributes
            'textLength',
            'lengthAdjust',
            'startOffset',
            'method',
            'spacing',
            'rotate',

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

            // Internal references only
            'href',
            'xlink:href',
        ];
    }
}
