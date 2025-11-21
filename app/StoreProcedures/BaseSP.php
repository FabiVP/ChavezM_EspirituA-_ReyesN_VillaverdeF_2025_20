<?php

namespace App\StoreProcedures;

use Illuminate\Support\Facades\DB;

class BaseSP
{
    /**
     * Ejecuta un procedimiento almacenado MySQL.
     *
     * @param string $procedure  Nombre del SP
     * @param array  $params     Parámetros del SP
     *
     * @return \Illuminate\Support\Collection
     */
    protected function executeProcedure(string $procedure, array $params = [])
    {
        if (empty($params)) {
            $query = "CALL {$procedure}()";
        } else {
            $placeholders = implode(',', array_fill(0, count($params), '?'));
            $query = "CALL {$procedure}({$placeholders})";
        }

        $result = DB::select($query, $params);

        return collect($result);
    }


    /**
     * ============================================================
     * 📌 Ejecuta un SP que debe devolver SOLO 1 registro
     * Útil para: mostrar por ID, detalles, obtener un solo objeto
     * ============================================================
     */
    protected function callProcedureSingle(string $procedure, array $params = [])
    {
        $collection = $this->executeProcedure($procedure, $params);

        // Retornar el primer objeto, o null si no existe
        return $collection->first();
    }
}
