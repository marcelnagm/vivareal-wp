<?php
namespace Models;
 
use \Illuminate\Database\Eloquent\Model;
 
class sfguard extends Model {
     
    protected $table = 'sf_guard_user';
      
    public function sfguard_group()
    {
        return $this->hasOne('Models\sf_guard_user_group', 'user_id', 'id');
    } 
}