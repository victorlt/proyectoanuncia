<?php

namespace Anuncia;

use Illuminate\Database\Eloquent\Model;

class Footbridges extends Model
{
    /**
     * Database used by this model
     * @var string
     */
    protected $table = 'footbridges';

    protected $fillable = array('name', 'availability');

    public function images(){

        return $this->hasMany(Image::class,'footbridge_id');
    }

}
