<?php

namespace KBox;

class HomeRoute
{
    /**
     * Get the home page route for the specified user
     *
     * @param \KBox\User $user
     * @return string
     */
    public static function get(User $user)
    {
        return app(GetHomeRoute::class)->__invoke($user);
    }
}
