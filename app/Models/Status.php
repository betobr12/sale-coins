<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Status extends Model
{
    use HasFactory;

    protected $table = 'status';
    public $timestamps = true;
    protected $fillable = [
        'uuid',
        'description',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function getStatus()
    {
        return DB::table('status')
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
        ->get();
    }
}
