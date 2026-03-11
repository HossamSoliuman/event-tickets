<?php

namespace App\Models;

/**
 * AdminUser is NOT a separate table.
 * It points to the same `users` table but adds a global scope
 * so the admin auth guard can only authenticate users with role = 'admin'.
 */
class AdminUser extends User
{
    protected $table = 'users';

    /**
     * Boot the model and add a global scope to restrict to admins only.
     * This ensures the admin guard cannot log in as a regular user.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('admin', function ($query) {
            $query->where('role', 'admin');
        });
    }
}
