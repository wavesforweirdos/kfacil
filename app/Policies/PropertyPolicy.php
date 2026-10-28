<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

/**
 * Los administradores gestionan todos los inmuebles; los agentes ven todos, crean los suyos y
 * solo editan, clonan y borran los que han creado ellos (permiso edit_own_properties).
 */
class PropertyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isAgent();
    }

    public function view(User $user, Property $property): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_properties');
    }

    public function update(User $user, Property $property): bool
    {
        return $user->isAdmin()
            || ($user->hasPermission('edit_own_properties') && $property->user_id === $user->id);
    }

    public function delete(User $user, Property $property): bool
    {
        return $this->update($user, $property);
    }
}
