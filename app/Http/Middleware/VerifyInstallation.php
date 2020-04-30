<?php

namespace App\Http\Middleware;

use Closure;

class VerifyInstallation
{
    protected $except = [
        'install*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function handle($request, Closure $next)
    {
        if (!file_exists(storage_path('installed')) && !$request->is('install*')) {
            return redirect()->to('install');
        }

        if (file_exists(storage_path('installed')) && $request->is('install*')
            && !$request->is('install/settings') && !$request->is('install/email_settings')
            && !$request->is('install/complete')
        ) {
            return redirect()->to('/');
        }

        return $next($request);
    }
}
