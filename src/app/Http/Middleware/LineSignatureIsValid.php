<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use LINE\Parser\SignatureValidator;
use Illuminate\Support\Arr;
use LINE\Constants\HTTPHeader;

class LineSignatureIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Arr::has($request->header(), mb_strtolower(HTTPHeader::LINE_SIGNATURE))) {
            if (!SignatureValidator::validateSignature(
                $request->getContent(),
                env('LINE_CHANNEL_SECRET'),
                $request->header(mb_strtolower(HTTPHeader::LINE_SIGNATURE))
            )) {
                throw new InvalidSignatureException('Invalid signature has given');
            }
        }
        return $next($request);
    }
}
