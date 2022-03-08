<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'payment_methods';
    public $timestamps = true;
    protected $fillable = [
        'uuid',
        'description',
        'tax_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function getPaymentMethod()
    {
        return DB::table('payment_methods as payment_method')
        ->leftJoin('taxes as tax', 'tax.id', '=', 'payment_method.tax_id')
        ->selectRaw("
            payment_method.id,
            payment_method.uuid,
            payment_method.description,
            payment_method.tax_id,
            tax.percentage,
            payment_method.created_at,
            payment_method.updated_at,
            payment_method.deleted_at
        ")
        ->when($this->id, function ($query, $id) {
            return $query->where('payment_method.id', '=', $id);
        })
        ->when($this->uuid, function ($query, $uuid) {
            return $query->where('payment_method.uuid', '=', $uuid);
        })
        ->when($this->tax_id, function ($query, $tax_id) {
            return $query->where('payment_method.tax_id', '=', $tax_id);
        })
        ->when($this->description, function ($query, $description) {
            return $query->where('payment_method.description', '=', $description);
        })
        ->get();
    }
}
