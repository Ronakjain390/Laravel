<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class PropertyManager extends Model
{
    use HasFactory;
    use Searchable;
    public $table = "properties";
    protected $fillable = [
        'name',
        'email',
        'contactNumber',
        'title',
        'Utype',
        'reason',
        'location',
        'builtArea',
        'carpetArea',
        'bedroom',
        'bathroom',
        'parking',
        'priceType',
        'totalPrice',
        'description',
        'generalAmenities',
        'longitude',
        'latitude',
        'image'
    ];
    public function searchableAs()
    {
        return 'propertymanager_index';
    }
    public function toSearchableArray()
    {
        return [
            'reason' => (int) $this->reason,
            'price' => (float) $this->price,
            'bedroom' => (int) $this->bedroom,
            'bathroom' => (int) $this->bathroom,
        ];
    }
}
