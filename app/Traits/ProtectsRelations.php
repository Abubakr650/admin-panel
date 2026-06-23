<?php

namespace App\Traits;

trait ProtectsRelations
{
    /**
     * Check if the model has any records in protected relationships.
     * Usually defined in the model as: protected array $protectedRelations = ['relationName'];
     *
     * @return bool
     */
    public function hasProtectedRelations(): bool
    {
        // Get protected relations from the model property
        $relations = property_exists($this, 'protectedRelations') ? $this->protectedRelations : [];

        foreach ($relations as $relation) {
            if (method_exists($this, $relation)) {
                $relationQuery = $this->$relation();

                // Check if the relation has any records
                // We use withTrashed() if the related model supports soft deletes to ensure full integrity
                if (method_exists($relationQuery, 'withTrashed')) {
                    if ($relationQuery->withTrashed()->exists()) {
                        return true;
                    }
                } else {
                    if ($relationQuery->exists()) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
