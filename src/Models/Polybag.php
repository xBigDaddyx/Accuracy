<?php

namespace Xbigdaddyx\Accuracy\Models;

use Domain\Users\Models\User;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use RichanFongdasen\EloquentBlameable\BlameableTrait;
use Teresa\CartonBoxGuard\Models\Polybag as ModelsPolybag;
use Teresa\CartonBoxGuard\Traits\HasStringId;

class Polybag extends Model
{
    // use HasStringId;
    use HasFactory;
    use BlameableTrait;
    use SoftDeletes;
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {

            $model->company_id = auth()->user()->company->id;
        });
    }
    // protected $keyType = 'string';

    // protected $primaryKey = 'id';

    // protected $connection = 'teresa_box';
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

    // protected static $blameable = [
    //     'guard' => null,
    //     'user' => config('accuracy.models.user'),
    //     'createdBy' => 'created_by',
    //     'updatedBy' => 'updated_by',
    // ];

    // public function prefixable(): array
    // {
    //     return [
    //         'id_prefix' => 'PB',
    //         'company_id' => auth()->user()->company->id,
    //         'company_short_name' => auth()->user()->company->short_name,
    //     ];
    // }
    // protected $dispatchesEvents = [

    //     'created' => \Teresa\CartonBoxGuard\Events\PolybagCreated::class,
    //     //..
    // ];
    public function tags(): MorphMany
    {
        return $this->morphMany(Tag::class, 'taggable');
    }

    public function user()
    {
        return $this->belongsTo(config('accuracy.models.user'), 'created_by', 'id');
    }
    public function scannedBy(): BelongsTo
    {
        return $this->belongsTo(config('accuracy.models.user'), 'created_by', 'id');
    }
    public function createdBy()
    {
        return $this->belongsTo(config('accuracy.models.user'), 'created_by', 'id');
    }

    public function box()
    {
        return $this->belongsTo(CartonBox::class, 'carton_box_id', 'id');
    }

    // public function garments()
    // {
    //     return $this->hasMany(PolybagGarment::class);
    // }

    // public function polybagGarments(): BelongsToMany
    // {
    //     return $this->belongsToMany(CartonBoxAttribute::class, 'polybag_garments');
    // }

    // protected static function newFactory()
    // {
    //     return \Modules\Packing\Database\factories\PolybagFactory::new();
    // }
}
