<?php

namespace Xbigdaddyx\Accuracy\Models;

use Domain\Users\Models\Company;
use Domain\Users\Models\User;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use RichanFongdasen\EloquentBlameable\BlameableTrait;
use Sfolador\Locked\Traits\HasLocks;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Teresa\CartonBoxGuard\Models\CartonBox as ModelsCartonBox;
use Teresa\CartonBoxGuard\Traits\HasStringId;
use OwenIt\Auditing\Contracts\Auditable;


class CartonBox extends Model implements Auditable
{

    //use PowerJoins;
    use \OwenIt\Auditing\Auditable;
    use LogsActivity;
    // use HasStringId;
    use HasLocks;
    use HasFactory;
    use BlameableTrait;
    use SoftDeletes;
    // protected $primary = 'id';


    // protected $keyType = 'string';

    // protected $primaryKey = 'id';

    protected $guarded = [];


    public function blameable()
    {
        return [
            'guard' => null,
            'user' => config('accuracy.models.user'),
            'createdBy' => 'created_by',
            'updatedBy' => 'updated_by',
        ];
    }
    protected $casts = [
        'is_completed' => 'boolean',
        'in_inspection' => 'boolean',
    ];
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {

            $model->company_id = auth()->user()->company->id;
        });
    }
    // public function prefixable(): array
    // {
    //     return [
    //         'id_prefix' => 'CB',
    //         'company_id' => Filament::getTenant()->id,
    //         'company_short_name' => Filament::getTenant()->short_name,
    //     ];
    // }
    protected $auditInclude = [
        'box_code', 'size', 'color', 'is_completed', 'carton_number', 'quantity', 'locked_at',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['box_code', 'size', 'color', 'is_completed', 'carton_number', 'quantity', 'locked_at'])
            ->logOnlyDirty();
        // Chain fluent methods for configuration options
    }



    // protected $appends = [
    //     'percentage',

    // ];

    // public function getPercentageAttribute()
    // {
    //     if ($this->polybags->count() > 0) {
    //         return number_format($this->quantity == 0 ? 0 : ($this->polybags->count() / $this->quantity) * 100, 0);
    //     }

    //     return 0;
    // }

    // public function tags(): MorphMany
    // {
    //     return $this->morphMany(Tag::class, 'taggable');
    // }
    public function final(): HasMany
    {
        return $this->hasMany(Inspection::class, 'carton_box_id', 'id');
    }
    public function finishInspection()
    {
        $final = Inspection::where('carton_box_id', $this->id)->where('is_finish', false)->first();
        $final->is_finish = true;
        $final->finished_at = now();
        $final->save();

        $this->in_inspection = false;
        $this->inspection_at = null;
        $this->inspection_requested_by = null;
        $this->save();
    }
    public function inspection($requester)
    {
        $final = new Inspection([
            'carton_box_id' => $this->id,
            'inspector' => $requester,
        ]);
        $this->final()->save($final);
        $this->in_inspection = true;
        $this->inspection_at = now();
        $this->inspection_requested_by = $requester;
        if ($this->is_completed === true) {
            $this->is_completed = false;
            $this->completed_at = null;
            $this->completed_by = null;
            $this->unlock();
            $this->polybags()->delete();
        }
        $this->save();
    }

    public function polybags(): HasMany
    {
        return $this->hasMany(Polybag::class);
    }
    public function scopeOutstanding($query)
    {
        return $query->where('is_completed', false);
    }

    public function packingList(): BelongsTo
    {
        return $this->belongsTo(PackingList::class, 'packing_list_id', 'id');
    }

    public function packingLists(): BelongsTo
    {
        return $this->belongsTo(PackingList::class, 'packing_list_id', 'id');
    }


    public function user()
    {
        return $this->belongsTo(config('accuracy.models.user'), 'updated_by', 'id');
    }
    public function company()
    {
        return $this->belongsTo(config('accuracy.tenant'), config('accuracy.tenant_foreign_key'), config('accuracy.tenant_other_key'));
    }
    public function polybagTags(): HasManyThrough
    {
        return $this->hasManyThrough(Tag::class, Polybag::class, 'carton_box_id', 'taggable_id')->where(
            'taggable_type',
            Polybag::class
        );
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(CartonBoxAttribute::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(config('accuracy.models.user'), 'updated_by', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(config('accuracy.models.user'), 'created_by', 'id');
    }

    public function completedBy()
    {
        return $this->belongsTo(config('accuracy.models.user'), 'completed_by', 'id');
    }
}
