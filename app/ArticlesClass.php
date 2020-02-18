<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticlesClass extends Model
{
    //
    protected $fillable = ['name'];
    
    public function articles(){
        return $this->hasMany(Articles::CLASS,'articles_classes_id');
    }
}
