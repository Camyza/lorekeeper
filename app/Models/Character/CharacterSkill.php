<?php

namespace App\Models\Character;

use App\Models\Model;
use App\Models\Skill\Skill;

class CharacterSkill extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_id', 'skill_id', 'level',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_skills';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the character this profile belongs to.
     */
    public function character() {
        return $this->belongsTo(Character::class, 'character_id');
    }

    /**
     * Get the skill.
     */
    public function skill() {
        return $this->belongsTo(Skill::class, 'skill_id');
    }
}
