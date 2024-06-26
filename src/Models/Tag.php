<?php

namespace Xbigdaddyx\Accuracy\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Teresa\CartonBoxGuard\Traits\HasStringId;
use RichanFongdasen\EloquentBlameable\BlameableTrait;

class Tag extends Model
{
    use SoftDeletes;
    use BlameableTrait;
    // use HasStringId;
    // protected $keyType = 'string';

    protected $primaryKey = 'id';
    public function blameable()
    {
        return [
            'guard' => null,
            'user' => config('accuracy.models.user'),
            'createdBy' => 'created_by',
            'updatedBy' => 'updated_by',
        ];
    }
    protected $guarded = [];
    // public function prefixable(): array
    // {
    //     return [
    //         'id_prefix' => 'TAG',
    //         'company_id' => Auth::user()->company->id,
    //         'company_short_name' => Auth::user()->company->short_name,
    //     ];
    // }
    // public function __construct(array $attributes = [])
    // {
    //     if (!isset($this->connection)) {
    //         $this->setConnection(config('sqlsrv'));
    //     }

    //     if (!isset($this->table)) {
    //         $this->setTable(config('carton-box-guard.tag.table_name'));
    //     }

    //     parent::__construct($attributes);
    // }
    public function taggable()
    {
        return $this->morphTo();
    }

    public function attributable()
    {
        return $this->morphTo();
    }

    public function createdBy()
    {
        return $this->belongsTo(config('accuracy.models.user'), 'created_by');
    }

    // public function attribute()
    // {
    //     return $this->belongsTo(Packing::class, 'attribute_id', 'id');
    // }

    public function updatedBy()
    {
        return $this->belongsTo(config('accuracy.models.user'), 'updated_by');
    }

    public function polybag(): BelongsTo
    {
        return $this->belongsTo(Polybag::class);
    }
}
