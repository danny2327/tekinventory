<?php
namespace App\Models\Sold;
use Illuminate\Database\Eloquent\Model;

class soldItem extends Model
{
	
    protected $table = 'sold_items';
        
    protected $fillable = [
        'sold_id',
        'item_id',
        'quantity'
    ];	
	



    public function soldEntry()
    {
        return $this->belongsTo('App\Models\Sold\soldEntry','id','sold_id');
    }


}

