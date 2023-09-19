<?php

namespace JJalving\Autograph;

use JJalving\Autograph\Contracts\UserFormatter;

class CustomUserFormatter implements UserFormatter {
    /**
     * Get the display name for the given user
     *
     * @param mixed $user
     * @return string
     */
    public static function getDisplayName(mixed $user): string {
        // Find username
        if($user->first_name && $user->last_name) {
            $displayName = "{$user->first_name} {$user->last_name}";
        } else {
            $displayName = "{$user->name}";
        }
        // Append email if present
        if($user->email) {
            $displayName .= " ({$user->email})";
        }
        // Return full display name
        return $displayName;
    }
}