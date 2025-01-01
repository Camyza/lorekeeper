<?php

namespace App\Models\Character;

use App\Models\Model;
use App\Models\Stat\Stat;

class CharacterStat extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_id', 'stat_id', 'stat_level', 'count', 'current_count', 'count',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_stats';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    public function character() {
        return $this->belongsTo(Character::class);
    }

    public function stat() {
        return $this->belongsTo(Stat::class);
    }
}
