<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Currency extends Model
{
    use HasFactory;

    protected $table = 'currencies';
    public $timestamps = true;
    protected $fillable = [
        'uuid',
        'description',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function getCurrency()
    {
        return DB::table('currencies')
        ->selectRaw("
            id,
            uuid,
            description,
            created_at,
            updated_at,
            deleted_at
        ")
        ->when($this->id, function ($query, $id) {
            return $query->where('id', '=', $id);
        })
        ->when($this->uuid, function ($query, $uuid) {
            return $query->where('uuid', '=', $uuid);
        })
        ->when($this->onlyActive, function ($query, $onlyActive) {
            return $query->whereNull('deleted_at');
        })
        ->get();
    }
}
