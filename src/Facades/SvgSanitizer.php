<?php

namespace YourVendor\SvgSanitizer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string|null sanitizeFile(\Illuminate\Http\UploadedFile $file)
 * @method static string|null sanitizeCode(?string $svg)
 * @method static bool isFileSafe(\Illuminate\Http\UploadedFile $file)
 * @method static bool isCodeSafe(?string $svg)
 * @method static \YourVendor\SvgSanitizer\Services\SvgFileSanitizer file()
 * @method static \YourVendor\SvgSanitizer\Services\SvgCodeSanitizer code()
 *
 * @see \YourVendor\SvgSanitizer\SvgSanitizer
 */
class SvgSanitizer extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'svg-sanitizer';
    }
}
