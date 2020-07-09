<?php

namespace Illuminate\Foundation\Auth;
use Illuminate\Support\Facades\Auth;
use App\AdminUserType;

trait RedirectsUsers
{
    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        $user_types = AdminUserType::all();

        foreach ($user_types as $key => $type) {
            switch (Auth::user()->user_type) {
                case 'production':
                    $this->redirectTo = '/production/dashboard';
                    if (method_exists($this, 'redirectTo')) {
                        return $this->redirectTo();
                    }
                    return property_exists($this, 'redirectTo') ? $this->redirectTo : '/login';
                    break;
                
                default:
                    $this->redirectTo = '/dashboard';

                    if (method_exists($this, 'redirectTo')) {
                        return $this->redirectTo();
                    }
                    break;
            }
        }
    }
}
