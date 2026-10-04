<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Facebook, Google and others tack a click id onto links to the site. Drop it
 * with a redirect so readers who copy the address share the clean one.
 */
class StripClickIds
{
    /**
     * @var list<string>
     */
    private const PARAMETERS = ['fbclid', 'gclid', 'msclkid', 'igshid'];

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('GET') || ! array_intersect(self::PARAMETERS, array_keys($request->query()))) {
            return $next($request);
        }

        $query = http_build_query(array_diff_key($request->query(), array_flip(self::PARAMETERS)));

        return redirect($request->url().($query !== '' ? "?{$query}" : ''), 301);
    }
}
