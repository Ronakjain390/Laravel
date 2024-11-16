<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanelSettings extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'settings'];
    protected $casts = ['settings' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSetting($panel, $key, $default = false)
    {
        return $this->settings[$panel][$key] ?? $default;
    }

    public function setSetting($panel, $key, $value)
    {
        $settings = $this->settings ?? [];
        $settings[$panel] = $settings[$panel] ?? [];
        $settings[$panel][$key] = $value;
        $this->settings = $settings;
        $this->save();
    }

    public static function getOrCreate($userId)
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            ['settings' => []]
        );
    }
}
