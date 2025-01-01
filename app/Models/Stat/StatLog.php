<?php

namespace App\Models\Stat;

use App\Models\Character\Character;
use App\Models\Model;
use App\Models\User\User;

class StatLog extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'recipient_id', 'stat_id', 'previous_level', 'new_level', 'leveller_type',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stat_log';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the stat that was logged.
     */
    public function stat() {
        return $this->belongsTo(Stat::class);
    }

    /**
     * Get the user who received the logged action.
     */
    public function leveller() {
        if ($this->leveller_type == 'User') {
            return $this->belongsTo(User::class, 'recipient_id');
        }

        return $this->belongsTo(Character::class, 'recipient_id');
    }
}
