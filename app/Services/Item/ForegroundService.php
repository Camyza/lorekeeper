<?php

namespace App\Services\Item;

use App\Models\Character\Character;
use App\Models\Item\Item;
use App\Models\Item\ItemTag;
use App\Services\Service;
use Carbon\Carbon;
use DB;

class ForegroundService extends Service {
    /*
    |--------------------------------------------------------------------------
    | Foreground Service
    |--------------------------------------------------------------------------
    |
    | Handles the editing and usage of foreground type items.
    |
    */

    /**
     * Retrieves any data that should be used in the item tag editing form.
     *
     * @return array
     */
    public function getEditData() {
        return [];
    }

    /**
     * Processes the data attribute of the tag and returns it in the preferred format.
     *
     * @param string $tag
     *
     * @return mixed
     */
    public function getTagData($tag) {
        $data = [];
        if ($tag->data) {
            $data = $tag->data;
        }

        return $data;
    }

    /**
     * Processes the data attribute of the tag and returns it in the preferred format.
     *
     * @param string $tag
     * @param array  $data
     *
     * @return bool
     */
    public function updateData($tag, $data) {
        DB::beginTransaction();

        try {
            $image = null;
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);

                $saveName = $tag->id.'-image.'.$image->getClientOriginalExtension();
                $fileName = $tag->id.'-image.'.$image->getClientOriginalExtension().'?v='.Carbon::now()->format('mdY_').randomString(6);

                $newData['background-image'] = 'images/data/items/foregrounds/'.$tag->item_id.'/'.$tag->id.'/'.$fileName;
            }

            if ($image) {
                $this->handleImage($image, public_path('images/data/items/foregrounds/'.$tag->item_id.'/'.$tag->id), $saveName);
            } else {
                $newData['background-image'] = ($tag->getData() ? $tag->getData()['background-image'] : null);
            }

            $tag->update(['data' => $newData]);

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Acts upon the item when used from the inventory.
     *
     * @param \App\Models\User\UserItem $stacks
     * @param \App\Models\User\User     $user
     * @param array                     $data
     *
     * @return bool
     */
    public function act($stacks, $user, $data) {
        //
    }

    public function checkForeground($character) {
        // Fetch all items with the 'foreground' tag that the character has equipped
        $foregroundItems = ItemTag::where('tag', 'foreground')->pluck('item_id');

        // Get all the items that the character has with the foreground tag
        $items = $character->items()
            ->where('count', '>', '0')
            ->whereIn('item_id', $foregroundItems)
            ->get();

        $foregroundData = [];

        foreach ($items as $item) {
            $tag = $item->tag('foreground');
            if ($tag && $tag->is_active && isset($tag->id)) {
                $foregroundData[] = [
                    'item_id'  => $item->id, // Required for the path: .../{item_id}/...
                    'tag_id'   => (int) $tag->id, // Required for the path: .../{tag_id}/{tag_id}-image.png
                    'css_data' => $tag->getData() ?? [],
                ];
            }
        }

        return !empty($foregroundData) ? $foregroundData : null; // Return all tag IDs or null if none
    }
}
