<?php

namespace App\Models\Shop;

use App\Models\Character\Character;
use App\Models\Currency\Currency;
use App\Models\Item\Item;
use App\Models\Model;
use App\Models\User\User;

class ShopLog extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shop_id', 'character_id', 'user_id', 'cost', 'item_id', 'quantity', 'stock_type',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shop_log';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'stock_id' => 'required',
        'shop_id'  => 'required',
        'bank'     => 'required|in:user,character',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'cost' => 'array',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user who purchased the item.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the character who purchased the item.
     */
    public function character() {
        return $this->belongsTo(Character::class);
    }

    /**
     * Get the purchased item.
     */
    public function item() {
        $model = getAssetModelString(strtolower($this->stock_type ?? 'Item'));

        return $this->belongsTo($model);
    }

    /**
     * Get the shop the item was purchased from.
     */
    public function shop() {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the currency used to purchase the item.
     */
    public function currency() {
        return $this->belongsTo(Currency::class);
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Get the item data that will be added to the stack as a record of its source.
     *
     * @return string
     */
    public function getItemDataAttribute() {
        $cost = mergeAssetsArrays(parseAssetData($this->cost['user']), parseAssetData($this->cost['character']));
        return 'Purchased from '.$this->shop->name.' by '.
            ($this->character_id ? $this->character->slug.' (owned by '.$this->user->name.')' : $this->user->displayName).' using '.
            (createRewardsString($cost, true, true) == 'Nothing. :(' ? 'Free' : createRewardsString($cost, true, true));
    }

    /**
     * Get the cost of the item.
     */
    public function getTotalCostAttribute() {
        return mergeAssetsArrays(parseAssetData($this->cost['user']), parseAssetData($this->cost['character']));
    }

    /**
     * Get the cost of the item in a readable format.
     *
     * @return string
     */
    public function getDisplayCostAttribute() {
        return createRewardsString(mergeAssetsArrays(parseAssetData($this->cost['user']), parseAssetData($this->cost['character'])), true, true) == 'Nothing. :('
            ? 'Free' : createRewardsString(mergeAssetsArrays(parseAssetData($this->cost['user']), parseAssetData($this->cost['character'])), true, true);
    }
}
