<?php

namespace Anuncia;

use Illuminate\Database\Eloquent\Model;

class Footbridge extends Model
{

    /**
     * Database used by this model
     * @var string
     */

    protected $table = 'footbridges';

    protected $fillable = [

        'name', 'availability','municipality_id'

    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function images()

    {

        return $this->hasMany(Photo::class, 'footbridge_id');

    }

    public function addPhoto(Photo $photo)

    {

        return $this->images()->save($photo);

    }


    public function municipality()

    {

        return $this->belongsTo(Municipality::class, 'municipality_id');

    }

    public function scopeCloseFootbridge($query,$footbridge){


        return $query->select('footbridges.id','footbridges.name', 'images.thumbnail_path','images.path')

            ->join('images','footbridges.id','=','footbridge_id')

            ->where(function ($query) use ($footbridge) {

                $query->where('footbridges.municipality_id', $footbridge->municipality_id)

                    ->where('footbridges.id', '!=', $footbridge->id);

            })

            ->groupBy('footbridges.name')

            ->orderBy('images.order','asc')

            ->take(6)

            ->get();

    }
}
