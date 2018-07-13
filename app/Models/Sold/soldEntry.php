<?php
namespace App\Models\Sold;
use Illuminate\Database\Eloquent\Model;

class soldEntry extends Model
{
	
    protected $table = 'sold';

    private $itemsToAdd;
        
    protected $fillable = [
        'client',
        'collection',
        'notes'
    ];	

    public function create($add)
    {
        
    }


    public function soldItem()
    {
        return $this->hasMany('App\Models\Sold\soldItem', 'sold_id','id');
    }

}

