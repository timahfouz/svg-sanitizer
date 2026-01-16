<?php

namespace Timahfouz\SvgSanitizer\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to add security headers when serving SVG files.
 *
 * Apply this middleware to routes that serve SVG files:
 *   Route::get('/images/{file}', ...)->middleware('svg.headers');
 *
 * Or configure your web server (nginx/apache) to add these headers.
 */
class SecureSvgHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Check if the response is an SVG file
        $contentType = $response->headers->get('Content-Type', '');
        $path = $request->path();

        if ($this->isSvgResponse($contentType, $path)) {
            $this->addSecurityHeaders($response);
        }

        return $response;
    }

    /**
     * Check if the response is an SVG.
     */
    protected function isSvgResponse(string $contentType, string $path): bool
    {
        return str_contains($contentType, 'svg')
            || str_contains($contentType, 'image/svg+xml')
            || str_ends_with(strtolower($path), '.svg');
    }

    /**
     * Add security headers to the response.
     */
    protected function addSecurityHeaders(Response $response): void
    {
        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Content Security Policy to block inline scripts
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'none'; style-src 'unsafe-inline'; img-src data:;"
        );

        // Prevent embedding in iframes from other origins
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Referrer policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
    }
}
