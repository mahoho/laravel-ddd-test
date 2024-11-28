<?php

namespace Modules\Invoices\Infrastructure\Models;

use Database\Factories\InvoiceProductLineModelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Invoices\Domain\Entities\Invoice;

/**
 * @property string $id
 * @property string $invoice_id
 * @property string $name
 * @property int $price
 * @property int $quantity
 * @property Invoice $invoice
 *
 * @mixin \Eloquent
 */
class InvoiceProductLineModel extends Model
{
    use HasFactory;

    protected $table = 'invoice_product_lines';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'invoice_id',
        'name',
        'price',
        'quantity',
    ];

    protected $casts = [
        'price'    => "int",
        'quantity' => "int",
    ];

    protected static function newFactory() : InvoiceProductLineModelFactory
    {
        return InvoiceProductLineModelFactory::new();
    }

    public function invoice() : BelongsTo
    {
        return $this->belongsTo(InvoiceModel::class, 'invoice_id', 'id');
    }
}
