<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class BoardCard extends Model
{
    protected $table = 'cards';

    protected $fillable = [
        'list_id',
        'description',
        'ordering'
    ];

    public function list()
    {
        return $this->belongsTo(BoardList::class, "list_id");
    }

    public function cardActivity()
    {
        return $this->hasOne(CardActivity::class);
    }
}
