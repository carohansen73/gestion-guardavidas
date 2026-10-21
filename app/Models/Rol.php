<?php

namespace App\Models;

use App\Enums\ColorRol;
use Spatie\Permission\Models\Role;

/**
 * Rol de Spatie con los metadatos que el sistema necesita para tratarlo (ver la migración
 * add_metadatos_a_roles). Se registra en config/permission.php, así `assignRole()`, `hasRole()` y compañía
 * lo devuelven en lugar del modelo base.
 */
class Rol extends Role
{
    /** Íconos que sabe dibujar el componente role-badge. */
    public const ICONOS = ['user' => 'Persona', 'shield-check' => 'Escudo', 'star' => 'Estrella', 'lifebuoy' => 'Salvavidas', 'sparkles' => 'Destellos'];

    protected function casts(): array
    {
        return [
            'es_plantel' => 'boolean',
            'usa_temporada' => 'boolean',
            'asignable' => 'boolean',
            'intocable' => 'boolean',
            'rango' => 'integer',
        ];
    }

    /** Nombre para mostrar; si no tiene etiqueta, el nombre del rol con la primera en mayúscula. */
    public function getNombreVisibleAttribute(): string
    {
        return $this->etiqueta ?: ucfirst($this->name);
    }

    /** "Etiqueta (descripción)", como se muestra en los selectores. */
    public function getTextoSelectorAttribute(): string
    {
        return $this->nombre_visible.($this->descripcion ? " ({$this->descripcion})" : '');
    }

    public function colorRol(): ColorRol
    {
        return ColorRol::tryFrom((string) $this->color) ?? ColorRol::Slate;
    }
}
