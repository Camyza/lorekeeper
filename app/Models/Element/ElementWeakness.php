<?php

namespace App\Models\Element;

use App\Models\Model;

class ElementWeakness extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'element_id', 'weakness_id', 'multiplier',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'element_weaknesses';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the element that this weakness is of.
     */
    public function element() {
        return $this->belongsTo(Element::class, 'element_id');
    }

    /**
     * Get the element that this weakness is of.
     */
    public function weakness() {
        return $this->belongsTo(Element::class, 'weakness_id');
    }
}
