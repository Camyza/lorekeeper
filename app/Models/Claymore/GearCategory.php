<?php

namespace App\Models\Claymore;

use App\Models\Model;

class GearCategory extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'sort', 'has_image', 'description', 'parsed_description', 'class_restriction', 'is_visible',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'gear_categories';

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name'        => 'required|unique:gear_categories|between:3,25',
        'description' => 'nullable',
        'image'       => 'mimes:png,webp',
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name'        => 'required|between:3,25',
        'description' => 'nullable',
        'image'       => 'mimes:png',
    ];

    /**********************************************************************************************

        RELATIONSHIPS

    **********************************************************************************************/

    /**
     * gets all gears of this category.
     */
    public function gears() {
        return $this->hasMany(Gear::class, 'gear_category_id');
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to show only visible gear categories.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed|null                            $user
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query, $user = null) {
        if ($user && $user->hasPower('edit_claymores')) {
            return $query;
        }

        return $query->where('is_visible', 1);
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Displays the model's name, linked to its encyclopedia page.
     *
     * @return string
     */
    public function getDisplayNameAttribute() {
        return '<a href="'.$this->url.'" class="display-category">'.$this->name.'</a>';
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute() {
        return 'images/data/gear-categories';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getCategoryImageFileNameAttribute() {
        return $this->id.'-image.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getCategoryImagePathAttribute() {
        return public_path($this->imageDirectory);
    }

    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getCategoryImageUrlAttribute() {
        if (!$this->has_image) {
            return null;
        }

        return asset($this->imageDirectory.'/'.$this->categoryImageFileName);
    }

    /**
     * Gets the URL of the model's encyclopedia page.
     *
     * @return string
     */
    public function getUrlAttribute() {
        return url('world/gear-categories?name='.$this->name);
    }

    /**
     * Gets the URL for an encyclopedia search of gears in this category.
     *
     * @return string
     */
    public function getSearchUrlAttribute() {
        return url('world/gears?gear_category_id='.$this->id);
    }

    /**
     * Gets the admin edit URL.
     *
     * @return string
     */
    public function getAdminUrlAttribute() {
        return url('admin/gear/gear-categories/edit/'.$this->id);
    }

    /**
     * Gets the power required to edit this model.
     *
     * @return string
     */
    public function getAdminPowerAttribute() {
        return 'edit_claymores';
    }
}
