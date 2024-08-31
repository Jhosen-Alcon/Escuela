<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PeriodoCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->transform(function($row, $key) {

            $nombre = $row->nombre;
            $nombre = explode('-', $nombre);

            return [
                'id' => $row->id,
                'anio' => $nombre[0],
                'semestre' => $nombre[1],
            ];
        });
    }
}