<?php

namespace App\Http\Models\Tracker;

use Illuminate\Database\Eloquent\Model;

class TrackerActions extends Model
{
    protected $table = 'tracker_actions';
    public function trello()
    {
        return $this->belongsTo(TrackerTrello::class, 'trello_id');
    }
    protected $hidden = [
    ];

    protected $guarded = [];
}
