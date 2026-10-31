<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureActiveStaff;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    
->withMiddleware(function (Middleware $middleware): void {
    $middleware->trustProxies(at: '*');

    $middleware->redirectGuestsTo(
        fn () => route('admin.login')
    );
    $middleware->redirectUsersTo(
        fn () => route('admin.dashboard')
    );
    $middleware->alias([
        'active.staff' => EnsureActiveStaff::class,
    ]);
    $middleware->appendToPriorityList(
        \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
        EnsureActiveStaff::class
    );
})

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
