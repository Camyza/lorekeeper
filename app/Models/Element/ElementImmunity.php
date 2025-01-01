<?php

namespace App\Models\Element;

use App\Models\Model;

class ElementImmunity extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'element_id', 'immunity_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'element_immunities';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the element that this immunity is of.
     */
    public function element() {
        return $this->belongsTo(Element::class, 'element_id');
    }

    /**
     * Get the element that this immunity is of.
     */
    public function immunity() {
        return $this->belongsTo(Element::class, 'immunity_id');
    }
}
