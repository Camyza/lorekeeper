<?php

namespace App\Models\User;

use App\Models\Character\Character;
use App\Models\Level\Level;
use App\Models\Model;

class UserLevel extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'current_level', 'current_exp', 'current_points', 'stamina', 'character_id', 'arena_wins', 'arena_losses',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_levels';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attached character.
     */
    public function character() {
        return $this->belongsTo(Character::class);
    }

    /**
     * Get the current level.
     */
    public function level() {
        return $this->belongsTo(Level::class, 'current_level', 'level')->where('level_type', 'User');
    }

    /**********************************************************************************************

        ATTRIBUTES

    **********************************************************************************************/

    /**
     * get the next level.
     */
    public function getNextLevelAttribute() {
        return Level::where('level_type', 'User')->where('level', $this->current_level + 1)->first();
    }

    /**
     * Get current stamina as a progress bar width.
     */
    public function getStaminaProgressAttribute() {
        return ($this->stamina / 15) * 100;
    }

    /**
     * Calculates the width of the progress bar for the level.
     */
    public function getProgressBarWidthAttribute() {
        $nextLevel = $this->nextLevel;
        if (!$nextLevel) {
            return 100;
        }

        return ($this->current_exp / $nextLevel->exp_required) * 100;
    }
}
