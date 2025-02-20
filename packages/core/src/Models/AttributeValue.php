<?php

declare(strict_types=1);

namespace Shopper\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property string $value
 * @property string $key
 * @property int $position
 */
class AttributeValue extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'value',
        'key',
        'position',
        'attribute_id',
    ];

    public function getTable(): string
    {
        return shopper_table('attribute_values');
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    public function attributeProduct(): BelongsTo
    {
        return $this->belongsTo(AttributeProduct::class, 'attribute_value_id');
    }
}
