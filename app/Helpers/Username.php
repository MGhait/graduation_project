<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
class Username {
    public static function generateMissingUsernames(): array
    {
        $users = User::where(function ($query) {
            $query->whereNull('username')
                ->orWhere('username', '')
                ->orWhere('username', ' ');
        })->get();

        $results = [
            'total_processed' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0,
            'users' => []
        ];

        foreach ($users as $user) {
            $results['total_processed']++;
            $result = self::processUser($user);

            $results['users'][] = [
                'id' => $user->id,
                'name' => "{$user->first_name} {$user->last_name}",
                'old_username' => $user->getOriginal('username'),
                'new_username' => $result['username'] ?? null,
                'status' => $result['status'],
                'message' => $result['message'] ?? null
            ];

            $results[$result['status']]++;
        }

        return $results;
    }

    /**
     * Process a single user and generate username if needed
     */
    public static function processUser(User $user): array
    {
        // Check if user already has a username
        if (!empty(trim($user->username))) {
            return [
                'status' => 'skipped',
                'message' => 'User already has username'
            ];
        }
        $username = null;
        if(!empty(trim($user->first_name)) && !empty(trim($user->last_name))){
            $username = self::generateUsername($user->first_name, $user->last_name);
        }
        // Check if we have the required fields
        elseif (!empty($user->provider) && !empty(trim($user->first_name))) {
            $username = self::generateUsernameFromFullName($user->first_name);
        } else {
            return [
                'status' => 'errors',
                'message' => 'Missing required name fields to generate username'
            ];
        }

        try {
            $user->username = $username;
            $user->save();

            return [
                'status' => 'updated',
                'username' => $username
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'errors',
                'message' => 'Error saving username: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate a unique username from first and last name
     */
    public static function generateUsername(string $firstName, string $lastName): string
    {
        $firstName = self::cleanString($firstName);
        $lastName = self::cleanString($lastName);

        // Create base username
        $baseUsername = strtolower($firstName . $lastName);

        // Ensure it's unique
        return self::makeUsernameUnique($baseUsername);
    }

    /**
     * Generate username from full name (for provider users)
     */
    public static function generateUsernameFromFullName(string $fullName): string
    {
        $fullName = self::cleanString($fullName);

        // Try to split the full name
        $nameParts = explode(' ', trim($fullName));

        if (count($nameParts) >= 2) {
            // Has multiple parts, use first and last
            $firstName = $nameParts[0];
            $lastName = end($nameParts);
            $baseUsername = strtolower($firstName . $lastName);
        } else {
            // Single name, just use it as is
            $baseUsername = strtolower($fullName);
        }

        return self::makeUsernameUnique($baseUsername);
    }


    /**
     * Generate username for a specific user without saving
     */
    public static function generateUsernameForUser(User $user): ?string
    {
        if (empty($user->first_name) || empty($user->last_name)) {
            return null;
        }

        return self::generateUsername($user->first_name, $user->last_name);
    }

    /**
     * Update username for a specific user
     */
    public static function updateUserUsername(User $user, bool $forceUpdate = false): array
    {
        if (!$forceUpdate && !empty(trim($user->username))) {
            return [
                'success' => false,
                'message' => 'User already has a username. Use forceUpdate = true to override.'
            ];
        }

        if (empty($user->first_name) || empty($user->last_name)) {
            return [
                'success' => false,
                'message' => 'Missing first_name or last_name'
            ];
        }

        try {
            $oldUsername = $user->username;
            $newUsername = self::generateUsername($user->first_name, $user->last_name);

            $user->username = $newUsername;
            $user->save();

            return [
                'success' => true,
                'old_username' => $oldUsername,
                'new_username' => $newUsername,
                'message' => 'Username updated successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error updating username: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Clean string for username generation
     */
    private static function cleanString(string $string): string
    {
        // Convert to ASCII (removes accents)
        $string = Str::ascii($string);

        // Remove everything except letters and numbers
        $string = preg_replace('/[^a-zA-Z0-9]/', '', $string);

        return trim($string);
    }

    /**
     * Make username unique by adding numbers if needed
     */
    private static function makeUsernameUnique(string $baseUsername): string
    {
        $username = $baseUsername;
        $counter = 1;

        // Keep checking until we find a unique username
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Alternative username generation methods
     */
    public static function generateUsernameAlternative(string $firstName, string $lastName, string $method = 'full'): string
    {
        $firstName = self::cleanString($firstName);
        $lastName = self::cleanString($lastName);

        switch ($method) {
            case 'first_last':
                $baseUsername = strtolower($firstName . $lastName);
                break;

            case 'first_dot_last':
                $baseUsername = strtolower($firstName . '.' . $lastName);
                break;

            case 'first_underscore_last':
                $baseUsername = strtolower($firstName . '_' . $lastName);
                break;

            case 'first_initial_last':
                $baseUsername = strtolower(substr($firstName, 0, 1) . $lastName);
                break;

            case 'first_last_initial':
                $baseUsername = strtolower($firstName . substr($lastName, 0, 1));
                break;

            default: // 'full'
                $baseUsername = strtolower($firstName . $lastName);
        }

        return self::makeUsernameUnique($baseUsername);
    }

    /**
     * Bulk update users by IDs
     */
    public static function updateUsersByIds(array $userIds, bool $forceUpdate = false): array
    {
        $users = User::whereIn('id', $userIds)->get();
        $results = [];

        foreach ($users as $user) {
            $results[] = [
                'user_id' => $user->id,
                'result' => self::updateUserUsername($user, $forceUpdate)
            ];
        }

        return $results;
    }

    /**
     * Check if username is available
     */
    public static function isUsernameAvailable(string $username): bool
    {
        return !User::where('username', $username)->exists();
    }

    /**
     * Get suggested usernames for a user
     */
    public static function getSuggestedUsernames(string $firstName, string $lastName, int $count = 5): array
    {
        $suggestions = [];
        $methods = ['first_last', 'first_dot_last', 'first_underscore_last', 'first_initial_last', 'first_last_initial'];

        foreach ($methods as $method) {
            if (count($suggestions) >= $count) break;

            $username = self::generateUsernameAlternative($firstName, $lastName, $method);
            $suggestions[] = $username;
        }

        return array_unique($suggestions);
    }
}
