<?php

namespace Modules\Invoices\Infrastructure\Models;

use Database\Factories\InvoiceModelFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Invoices\Domain\Enums\StatusEnum;

/**
 * @property string $id
 * @property StatusEnum $status
 * @property string $customer_name
 * @property string $customer_email
 * @property Collection<int,InvoiceProductLineModel> $product_lines
 *
 * @mixin \Eloquent
 */
class InvoiceModel extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'status',
        'customer_name',
        'customer_email',
    ];

    protected $casts = [
        'status' => StatusEnum::class,
    ];

    protected static function newFactory() : InvoiceModelFactory
    {
        return InvoiceModelFactory::new();
    }

    public function product_lines() : HasMany
    {
        return $this->hasMany(InvoiceProductLineModel::class, 'invoice_id', 'id');
    }
}
