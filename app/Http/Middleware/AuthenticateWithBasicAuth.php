<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth as Middleware;

class AuthenticateWithBasicAuth extends Middleware
{
    /**
     * Get the credentials the user should use to authenticate.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, string>
     */
    protected function credentials(Request $request): array
    {
        return $request->only($this->username());
    }

    /**
     * Get the username field from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function username(Request $request): string
    {
        return $request->get('email') ?: $request->getUser();
    }
}
