<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Articles extends Model
{
    //
    protected $fillable = ['articles_classes_id','users_id','title','description'];
    
    public function user(){
        return $this->belongsTo(User::CLASS,'users_id');
    }
    
    public function class(){
        return $this->belongsTo(ArticlesClass::CLASS,'articles_classes_id');
    }
}
