<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tax extends Model
{
    use HasFactory;

    protected $table = 'taxes';
    public $timestamps = true;
    protected $fillable = [
        'uuid',
        'description',
        'percentage',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function getTax()
    {
        return DB::table('taxes')
        ->selectRaw("
            id,
            uuid,
            description,
            percentage,
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
        ->when($this->description, function ($query, $description) {
            return $query->where('description', '=', $description);
        })
        ->get();
    }
}
