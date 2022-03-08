<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';
    public $timestamps = true;
    protected $fillable = [
        'uuid',
        'user_id',
        'status_id',
        'payment_method_id',
        'currency_id',
        'value',
        'net_value',
        'tax_payment',
        'tax_conversion',
        'currency_amount',
        'confirmad_date_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function getTransactions()
    {
        return DB::table('transactions     as transaction')
        ->leftJoin('users           as user',           'user.id',           '=', 'transaction.user_id')
        ->leftJoin('status          as stts',           'stts.id',           '=', 'transaction.status_id')
        ->leftJoin('payment_methods as payment_method', 'payment_method.id', '=', 'transaction.payment_method_id')
        ->leftJoin('taxes           as tax',            'tax.id',            '=', 'payment_method.tax_id')
        ->selectRaw("
            transaction.id,
            transaction.uuid,
            transaction.user_id,
            user.name                       as user_name,
            user.cpf_cnpj                   as user_cpf_cnpj,
            transaction.status_id,
            stts.description                as status,
            transaction.payment_method_id,
            payment_method.description      as payment_method_description,
            tax.percentage                  as payment_method_tax_percentage,
            transaction.currency_id,
            transaction.value,
            transaction.net_value,
            transaction.tax_payment,
            transaction.tax_conversion,
            transaction.currency_amount,
            transaction.confirmad_date_at,
            transaction.created_at,
            transaction.updated_at,
            transaction.deleted_at
        ")
        ->orderBy('id', 'desc')
        ->when($this->id, function ($query, $id) {
            return $query->where('transaction.id', '=', $id);
        })
        ->when($this->uuid, function ($query, $uuid) {
            return $query->where('transaction.id', '=', $uuid);
        })
        ->when($this->onlyActive, function ($query, $onlyActive) {
            return $query->whereNull('transaction.deleted_at');
        })
        ->get();
    }
}
