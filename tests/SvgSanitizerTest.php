<?php

namespace Timahfouz\SvgSanitizer\Tests;

use Orchestra\Testbench\TestCase;
use Timahfouz\SvgSanitizer\SvgSanitizerServiceProvider;
use Timahfouz\SvgSanitizer\Services\SvgCodeSanitizer;
use Timahfouz\SvgSanitizer\Services\SvgFileSanitizer;

class SvgSanitizerTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [SvgSanitizerServiceProvider::class];
    }

    /** @test */
    public function it_blocks_script_tags()
    {
        $sanitizer = new SvgCodeSanitizer(config('svg-sanitizer') ?? []);

        $malicious = '<svg><script>alert("XSS")</script></svg>';
        
        $this->assertFalse($sanitizer->isSafe($malicious));
    }

    /** @test */
    public function it_blocks_event_handlers()
    {
        $sanitizer = new SvgCodeSanitizer(config('svg-sanitizer') ?? []);

        $malicious = '<svg onload="alert(1)"></svg>';
        
        $this->assertFalse($sanitizer->isSafe($malicious));
    }

    /** @test */
    public function it_blocks_javascript_urls()
    {
        $sanitizer = new SvgCodeSanitizer(config('svg-sanitizer') ?? []);

        $malicious = '<svg><a href="javascript:alert(1)">click</a></svg>';
        
        $this->assertFalse($sanitizer->isSafe($malicious));
    }

    /** @test */
    public function it_blocks_foreign_objects()
    {
        $sanitizer = new SvgCodeSanitizer(config('svg-sanitizer') ?? []);

        $malicious = '<svg><foreignObject><body xmlns="http://www.w3.org/1999/xhtml"><script>alert(1)</script></body></foreignObject></svg>';
        
        $this->assertFalse($sanitizer->isSafe($malicious));
    }

    /** @test */
    public function it_allows_safe_svg()
    {
        $sanitizer = new SvgCodeSanitizer(config('svg-sanitizer') ?? []);

        $safe = '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="blue"/></svg>';
        
        $this->assertTrue($sanitizer->isSafe($safe));
    }

    /** @test */
    public function it_sanitizes_malicious_svg_code()
    {
        $sanitizer = new SvgCodeSanitizer(config('svg-sanitizer') ?? []);

        $malicious = '<svg onload="alert(1)"><script>alert(2)</script><circle cx="12" cy="12" r="10"/></svg>';
        
        $clean = $sanitizer->sanitize($malicious);
        
        // Should either return null or cleaned SVG without dangerous content
        if ($clean !== null) {
            $this->assertStringNotContainsString('onload', $clean);
            $this->assertStringNotContainsString('<script', $clean);
            $this->assertStringContainsString('<circle', $clean);
        }
    }

    /** @test */
    public function it_returns_null_for_completely_malicious_svg()
    {
        $sanitizer = new SvgCodeSanitizer(config('svg-sanitizer') ?? []);

        $malicious = '<script>alert(1)</script>';
        
        $result = $sanitizer->sanitize($malicious);
        
        // Should return null since there's no valid SVG structure
        $this->assertNull($result);
    }

    /** @test */
    public function it_handles_empty_input()
    {
        $sanitizer = new SvgCodeSanitizer(config('svg-sanitizer') ?? []);

        $this->assertNull($sanitizer->sanitize(null));
        $this->assertSame('', $sanitizer->sanitize(''));
        $this->assertTrue($sanitizer->isSafe(null));
        $this->assertTrue($sanitizer->isSafe(''));
    }

    /** @test */
    public function it_escapes_non_svg_content()
    {
        $sanitizer = new SvgCodeSanitizer(config('svg-sanitizer') ?? []);

        $iconClass = 'fa-home';
        $result = $sanitizer->sanitize($iconClass);
        
        $this->assertSame(htmlspecialchars($iconClass, ENT_QUOTES, 'UTF-8'), $result);
    }
}
