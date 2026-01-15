<?php

namespace YourVendor\SvgSanitizer;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use YourVendor\SvgSanitizer\Rules\SvgFileSafe;
use YourVendor\SvgSanitizer\Rules\SvgCodeSafe;
use YourVendor\SvgSanitizer\Services\SvgFileSanitizer;
use YourVendor\SvgSanitizer\Services\SvgCodeSanitizer;

class SvgSanitizerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(__DIR__ . '/../config/svg-sanitizer.php', 'svg-sanitizer');

        // Register SvgFileSanitizer as singleton
        $this->app->singleton(SvgFileSanitizer::class, function ($app) {
            return new SvgFileSanitizer(config('svg-sanitizer'));
        });

        // Register SvgCodeSanitizer as singleton
        $this->app->singleton(SvgCodeSanitizer::class, function ($app) {
            return new SvgCodeSanitizer(config('svg-sanitizer'));
        });

        // Register main facade binding
        $this->app->singleton('svg-sanitizer', function ($app) {
            return new SvgSanitizer(
                $app->make(SvgFileSanitizer::class),
                $app->make(SvgCodeSanitizer::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Publish config
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/svg-sanitizer.php' => config_path('svg-sanitizer.php'),
            ], 'svg-sanitizer-config');
        }

        // Register validation rules
        $this->registerValidationRules();
    }

    /**
     * Register custom validation rules.
     */
    protected function registerValidationRules(): void
    {
        // Rule for SVG file uploads: svg_file_safe
        Validator::extend('svg_file_safe', function ($attribute, $value, $parameters, $validator) {
            $rule = new SvgFileSafe();
            $passes = true;
            $errorMessage = '';

            $rule->validate($attribute, $value, function ($message) use (&$passes, &$errorMessage) {
                $passes = false;
                $errorMessage = $message;
            });

            if (!$passes) {
                $validator->setCustomMessages([$attribute . '.svg_file_safe' => $errorMessage]);
            }

            return $passes;
        });

        Validator::replacer('svg_file_safe', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, $message ?: 'The :attribute contains potentially unsafe SVG content.');
        });

        // Rule for SVG code in text fields: svg_code_safe
        Validator::extend('svg_code_safe', function ($attribute, $value, $parameters, $validator) {
            $rule = new SvgCodeSafe();
            $passes = true;
            $errorMessage = '';

            $rule->validate($attribute, $value, function ($message) use (&$passes, &$errorMessage) {
                $passes = false;
                $errorMessage = $message;
            });

            if (!$passes) {
                $validator->setCustomMessages([$attribute . '.svg_code_safe' => $errorMessage]);
            }

            return $passes;
        });

        Validator::replacer('svg_code_safe', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, $message ?: 'The :attribute contains potentially unsafe SVG content.');
        });
    }
}
