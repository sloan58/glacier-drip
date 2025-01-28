<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ManifestFile;

class ManifestFilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('user');
    }

    public function view(User $user, ManifestFile $manifestFile): bool
    {
        return $user->hasRole('admin') || $user->id === $manifestFile->user_id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole('admin', 'user');
    }

    public function update(User $user, ManifestFile $manifestFile): bool
    {
        return $user->hasRole('admin') || $user->id === $manifestFile->user_id;
    }

    public function delete(User $user, ManifestFile $manifestFile): bool
    {
        return $user->hasRole('admin') || $user->id === $manifestFile->user_id;
    }
}
