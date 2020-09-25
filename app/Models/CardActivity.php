<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class CardActivity extends Model
{
    protected $table = 'activity';

    protected $fillable = [
        'card_id',
        'ordering',
        'text'
    ];

    public function cards()
    {
        return $this->belongsTo(BoardCard::class);
    }
}
