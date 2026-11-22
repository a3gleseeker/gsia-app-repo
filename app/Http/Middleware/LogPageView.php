<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\PageView;
use App\Models\Property;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Stevebauman\Location\Facades\Location;


class LogPageView
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if(! $this->shouldLog($request, $response)){
            return $response;
        }

        $key = 'logged_' . md5($request->path());

        if ($request->session()->has($key)){
            return $response;
        }

        $request->session()->put($key, true);

        $visitorId = $request->cookie('visitor_id');

        if (! $visitorId){
            $visitorId = (string) Str::uuid();
            Cookie::queue('visitor_id', $visitorId, 60 * 24 * 365);
        }

        $agent = new Agent();
        $agent->setUserAgent($request->userAgent() ?? '');

        $pos = Location::get($request->ip());
        $property = $request->route('property');

        PageView::create([
            'visitor_id' => $visitorId,
            'user_id' => $request->user()?->id,
            'property_id' => $property instanceof Property ? $property->id : null,
            'path' => '/' . ltrim($request->path(),  '/ '),
            'route_name' => $request->route()?->getName(),
            'referrer' => substr((string) $request->headers->get('referrer'), 0, 255, ) ?: null,
            'ip_address' => $request->ip(),
            'country' => $pos ? $pos->countryName : null,
            'region' => $pos ? $pos->regionName : null,
            'city' => $pos ? $pos->cityName : null,
            'device_type' => $agent->isTablet() ? 'tablet' : ($agent->isMobile() ? 'mobile' : 'desktop'),
            'platform' => $agent->platform() ?: 'Unknown',
            'browser' => $agent->browser() ?: 'Unknown',
        ]);


        return $response;
    }

    private function shouldLog(Request $req, Response $res): bool{
        if (!$req->isMethod('GET') || $req->ajax()){
            return false;
        }

        if ($res->getStatusCode() !== 200){
            return false;
        }

        if ($req->is('up', 'analytics*')){
            return false;
        }

        if (($req->user()?->level ?? 0) >= 2){
            return false;
        }

        return ! str_contains(strtolower($req->userAgent() ?? ''), 'bot');

    }
}
