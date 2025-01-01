<?php

namespace App\Models\Character;

use App\Models\Level\Level;
use App\Models\Model;

class CharacterLevel extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_id', 'current_level', 'current_exp', 'current_points',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_levels';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the shop stock.
     */
    public function character() {
        return $this->belongsTo(Character::class);
    }

    /**
     * Get the current level for the character.
     */
    public function level() {
        return $this->belongsTo(Level::class, 'current_level', 'level')->where('level_type', 'Character');
    }

    /**********************************************************************************************

        ATTRIBUTES

    **********************************************************************************************/

    /**
     * get the next level.
     */
    public function getNextLevelAttribute() {
        return Level::where('level_type', 'Character')->where('level', $this->current_level + 1)->first();
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
