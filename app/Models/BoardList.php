<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class BoardList extends Model
{
    protected $table = 'lists';

    protected $fillable = [
        'board_id',
        'title',
        'ordering'
    ];

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function cards()
    {
        return $this->hasMany(BoardCard::class, "list_id");
    }
}
