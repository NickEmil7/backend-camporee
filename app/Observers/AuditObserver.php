<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditObserver
{
    // Se ejecuta automáticamente al CREAR un registro
    public function created(Model $model)
    {
        $this->logAction($model, 'CREATE', [], $model->toArray());
    }

    // Se ejecuta automáticamente al ACTUALIZAR un registro
    public function updated(Model $model)
    {
        // Ignoramos si solo se actualizó 'updated_at'
        if ($model->wasChanged()) {
            $oldValues = $model->getOriginal();
            $newValues = $model->getChanges();
            
            $this->logAction($model, 'UPDATE', $oldValues, $newValues);
        }
    }

    // Se ejecuta automáticamente al BORRAR un registro
    public function deleted(Model $model)
    {
        $this->logAction($model, 'DELETE', $model->toArray(), []);
    }
    
    // Función privada para guardar en la BD
    private function logAction(Model $model, $action, $oldValues, $newValues)
    {
        // Opcional: No auditar el propio AuditLog para evitar bucles infinitos
        if ($model instanceof AuditLog) return;

        AuditLog::create([
            'user_id'      => Auth::id(), // Usuario logueado actual
            'action'       => $action,
            'entity_type'  => class_basename($model), // Ej: "User", "Club"
            'entity_id'    => $model->id,
            'old_values'   => $oldValues,
            'new_values'   => $newValues,
            'ip_address'   => Request::ip(),
            'user_agent'   => Request::userAgent(),
        ]);
    }
}