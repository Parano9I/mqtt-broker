<?php

namespace App\Models;

use App\Enums\SensorStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Symfony\Component\Uid\Uuid;

class Sensor extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';

    protected $keyType = 'string';

    public $incrementing = false;
    protected $fillable = [
        'name',
        'description'
    ];

    protected $attributes = [
        'status' => SensorStatusEnum::OFFLINE
    ];

    protected $casts = [
        'status' => SensorStatusEnum::class
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $uuid = Uuid::fromString(Str::uuid());
            $model->uuid = $uuid->toBase58();
        });
    }
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}
