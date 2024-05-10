<?php

namespace Xbigdaddyx\Accuracy\Models;

use Domain\Users\Models\Company;
use Domain\Users\Models\User;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
//use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

use RichanFongdasen\EloquentBlameable\BlameableTrait;
//use OwenIt\Auditing\Contracts\Auditable;

//class PackingList extends Model implements Auditable
class PackingList extends Model
{
    //use \OwenIt\Auditing\Auditable;
    use HasFactory;

    use BlameableTrait;
    use SoftDeletes;

    // protected $auditInclude = [
    //     'id', 'buyer_id', 'po', 'style_no', 'contract_no', 'batch', 'description', 'is_ratio', '',
    // ];




    protected $guarded = [];

    protected $casts = [
        'is_ratio' => 'boolean',
    ];
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {

            $model->company_id = auth()->user()->company->id;
        });
    }
    // protected static $blameable = [
    //     'guard' => null,
    //     'user' => User::class,
    //     'createdBy' => 'created_by',
    //     'updatedBy' => 'updated_by',
    // ];

    // protected $appends = [
    //     'percentage',
    //     'completedBoxCount',
    // ];

    // public static function boot()
    // {
    //     parent::boot();
    //     self::creating(function ($model) {
    //         $count = ($model::where('id', 'like', Filament::getTenant()->short_name . '%')->withTrashed()->count() + 1);
    //         if ($count < 10) {
    //             $number = '000' . $count;
    //         } elseif ($count >= 10 && $count < 100) {
    //             $number = '00' . $count;
    //         } elseif ($count >= 100 && $count < 1000) {
    //             $number = '0' . $count;
    //         } else {
    //             $number = $count;
    //         }
    //         $model->company_id = Filament::getTenant()->id;
    //         $model->id = Filament::getTenant()->short_name . '.PL.' . $number;
    //     });
    // }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class, 'buyer_id', 'id');
    }
    public function buyers()
    {
        return $this->belongsTo(Buyer::class, 'buyer_id', 'id');
    }
    public function cartonBoxes()
    {
        return $this->hasMany(CartonBox::class, 'packing_list_id', 'id');
    }

    // public function packingListAttributes(): HasMany
    // {
    //     return $this->hasMany(PackingListAttribute::class);
    // }
}
