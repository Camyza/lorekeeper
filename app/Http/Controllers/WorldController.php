<?php

namespace App\Http\Controllers;

use App\Models\Character\CharacterCategory;
use App\Models\Character\CharacterClass;
use App\Models\Claymore\Gear;
use App\Models\Claymore\GearCategory;
use App\Models\Claymore\Weapon;
use App\Models\Claymore\WeaponCategory;
use App\Models\Currency\Currency;
use App\Models\Element\Element;
use App\Models\Feature\Feature;
use App\Models\Feature\FeatureCategory;
use App\Models\Item\Item;
use App\Models\Item\ItemCategory;
use App\Models\Level\Level;
use App\Models\Pet\Pet;
use App\Models\Pet\PetCategory;
use App\Models\Rarity;
use App\Models\Shop\Shop;
use App\Models\Shop\ShopStock;
use App\Models\Skill\Skill;
use App\Models\Skill\SkillCategory;
use App\Models\Species\Species;
use App\Models\Species\Subtype;
use App\Models\Stat\Stat;
use App\Models\Status\StatusEffect;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorldController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | World Controller
    |--------------------------------------------------------------------------
    |
    | Displays information about the world, as entered in the admin panel.
    | Pages displayed by this controller form the site's encyclopedia.
    |
    */

    /**
     * Shows the index page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex() {
        return view('world.index');
    }

    /**
     * Shows the currency page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCurrencies(Request $request) {
        $query = Currency::query();
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%')->orWhere('abbreviation', 'LIKE', '%'.$name.'%');
        }

        return view('world.currencies', [
            'currencies' => $query->orderBy('name')->orderBy('id')->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the rarity page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getRarities(Request $request) {
        $query = Rarity::query();
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%');
        }

        return view('world.rarities', [
            'rarities' => $query->orderBy('sort', 'DESC')->orderBy('id')->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the species page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSpecieses(Request $request) {
        $query = Species::query();
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%');
        }

        return view('world.specieses', [
            'specieses' => $query->with(['subtypes' => function ($query) {
                $query->visible(Auth::user() ?? null)->orderBy('sort', 'DESC');
            }])->visible(Auth::user() ?? null)->orderBy('sort', 'DESC')->orderBy('id')->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the subtypes page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSubtypes(Request $request) {
        $query = Subtype::query();
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%');
        }

        return view('world.subtypes', [
            'subtypes' => $query->with('species')->visible(Auth::user() ?? null)->orderBy('sort', 'DESC')->orderBy('id')->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the item categories page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getItemCategories(Request $request) {
        $query = ItemCategory::query();
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%');
        }

        return view('world.item_categories', [
            'categories' => $query->visible(Auth::user() ?? null)->orderBy('sort', 'DESC')->orderBy('id')->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the trait categories page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getFeatureCategories(Request $request) {
        $query = FeatureCategory::query();
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%');
        }

        return view('world.feature_categories', [
            'categories' => $query->visible(Auth::user() ?? null)->orderBy('sort', 'DESC')->orderBy('id')->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the traits page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getFeatures(Request $request) {
        $query = Feature::visible(Auth::user() ?? null)->with('category')->with('rarity')->with('species');
        $data = $request->only(['rarity_id', 'feature_category_id', 'species_id', 'subtype_id', 'name', 'sort']);
        if (isset($data['rarity_id']) && $data['rarity_id'] != 'none') {
            $query->where('rarity_id', $data['rarity_id']);
        }
        if (isset($data['feature_category_id']) && $data['feature_category_id'] != 'none') {
            if ($data['feature_category_id'] == 'withoutOption') {
                $query->whereNull('feature_category_id');
            } else {
                $query->where('feature_category_id', $data['feature_category_id']);
            }
        }
        if (isset($data['species_id']) && $data['species_id'] != 'none') {
            if ($data['species_id'] == 'withoutOption') {
                $query->whereNull('species_id');
            } else {
                $query->where('species_id', $data['species_id']);
            }
        }
        if (isset($data['subtype_id']) && $data['subtype_id'] != 'none') {
            if ($data['subtype_id'] == 'withoutOption') {
                $query->whereNull('subtype_id');
            } else {
                $query->where('subtype_id', $data['subtype_id']);
            }
        }
        if (isset($data['name'])) {
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        }

        if (isset($data['sort'])) {
            switch ($data['sort']) {
                case 'alpha':
                    $query->sortAlphabetical();
                    break;
                case 'alpha-reverse':
                    $query->sortAlphabetical(true);
                    break;
                case 'category':
                    $query->sortCategory();
                    break;
                case 'rarity':
                    $query->sortRarity();
                    break;
                case 'rarity-reverse':
                    $query->sortRarity(true);
                    break;
                case 'species':
                    $query->sortSpecies();
                    break;
                case 'subtypes':
                    $query->sortSubtype();
                    break;
                case 'newest':
                    $query->sortNewest();
                    break;
                case 'oldest':
                    $query->sortOldest();
                    break;
            }
        } else {
            $query->sortCategory();
        }

        return view('world.features', [
            'features'   => $query->orderBy('id')->paginate(20)->appends($request->query()),
            'rarities'   => ['none' => 'Any Rarity'] + Rarity::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'specieses'  => ['none' => 'Any Species'] + ['withoutOption' => 'Without Species'] + Species::visible(Auth::user() ?? null)->orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'subtypes'   => ['none' => 'Any Subtype'] + ['withoutOption' => 'Without Subtype'] + Subtype::visible(Auth::user() ?? null)->orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'categories' => ['none' => 'Any Category'] + ['withoutOption' => 'Without Category'] + FeatureCategory::visible(Auth::user() ?? null)->orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Shows a species' visual trait list.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSpeciesFeatures($id) {
        $categories = FeatureCategory::orderBy('sort', 'DESC')->get();
        $rarities = Rarity::orderBy('sort', 'ASC')->get();
        $species = Species::visible(Auth::user() ?? null)->where('id', $id)->first();
        if (!$species) {
            abort(404);
        }
        if (!config('lorekeeper.extensions.visual_trait_index.enable_species_index')) {
            abort(404);
        }

        $features = $species->features()->visible(Auth::user() ?? null);
        $features = count($categories) ?
            $features->orderByRaw('FIELD(feature_category_id,'.implode(',', $categories->pluck('id')->toArray()).')') :
            $features;
        $features = $features->orderByRaw('FIELD(rarity_id,'.implode(',', $rarities->pluck('id')->toArray()).')')
            ->orderBy('has_image', 'DESC')
            ->orderBy('name')
            ->get()->filter(function ($feature) {
                return $feature->subtype?->is_visible !== 0;
            })
            ->groupBy(['feature_category_id', 'id']);

        return view('world.species_features', [
            'species'    => $species,
            'categories' => $categories->keyBy('id'),
            'rarities'   => $rarities->keyBy('id'),
            'features'   => $features,
        ]);
    }

    /**
     * Shows a subtype's visual trait list.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSubtypeFeatures($id, Request $request) {
        $categories = FeatureCategory::orderBy('sort', 'DESC')->get();
        $rarities = Rarity::orderBy('sort', 'ASC')->get();
        $speciesBasics = $request->get('add_basics');
        $subtype = Subtype::visible(Auth::user() ?? null)->where('id', $id)->first();
        $species = Species::visible(Auth::user() ?? null)->where('id', $subtype->species->id)->first();
        if (!$subtype) {
            abort(404);
        }
        if (!config('lorekeeper.extensions.visual_trait_index.enable_subtype_index')) {
            abort(404);
        }

        $features = $speciesBasics ? $species : $subtype;
        $features = $features->features()->visible(Auth::user() ?? null);
        $features = count($categories) ?
            $features->orderByRaw('FIELD(feature_category_id,'.implode(',', $categories->pluck('id')->toArray()).')') :
            $features;
        $features = $features->orderByRaw('FIELD(rarity_id,'.implode(',', $rarities->pluck('id')->toArray()).')')
            ->orderBy('has_image', 'DESC')
            ->orderBy('name')
            ->get();

        if (!$speciesBasics) {
            $features = $features->groupBy(['feature_category_id', 'id']);
        } else {
            $features = $features
                ->filter(function ($feature) use ($subtype) {
                    return !($feature->subtype && $feature->subtype->id != $subtype->id);
                })
                ->groupBy(['feature_category_id', 'id']);
        }

        return view('world.subtype_features', [
            'subtype'    => $subtype,
            'categories' => $categories->keyBy('id'),
            'rarities'   => $rarities->keyBy('id'),
            'features'   => $features,
        ]);
    }

    /**
     * Shows a universal visual trait list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUniversalFeatures(Request $request) {
        $categories = FeatureCategory::orderBy('sort', 'DESC')->get();
        $rarities = Rarity::orderBy('sort', 'ASC')->get();

        if (!config('lorekeeper.extensions.visual_trait_index.enable_universal_index')) {
            abort(404);
        }

        $features = Feature::whereNull('species_id')
            ->visible(Auth::user() ?? null);
        $features = count($categories) ?
            $features->orderByRaw('FIELD(feature_category_id,'.implode(',', $categories->pluck('id')->toArray()).')') :
            $features;
        $features = $features->orderByRaw('FIELD(rarity_id,'.implode(',', $rarities->pluck('id')->toArray()).')')
            ->orderBy('has_image', 'DESC')
            ->orderBy('name')
            ->get()->groupBy(['feature_category_id', 'id']);

        return view('world.universal_features', [
            'categories' => $categories->keyBy('id'),
            'rarities'   => $rarities->keyBy('id'),
            'features'   => $features,
        ]);
    }

    /**
     * Provides a single trait's description html for use in a modal.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getFeatureDetail($id) {
        $feature = Feature::visible(Auth::user() ?? null)->where('id', $id)->first();

        if (!$feature) {
            abort(404);
        }

        return view('world._feature_entry', [
            'feature' => $feature,
        ]);
    }

    /**
     * Shows the status effects page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getStatusEffects(Request $request) {
        $query = StatusEffect::query();
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%');
        }

        return view('world.status_effects', [
            'statuses' => $query->orderBy('name')->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the items page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getItems(Request $request) {
        $query = Item::with('category')->released(Auth::user() ?? null);

        $categoryVisibleCheck = ItemCategory::visible(Auth::user() ?? null)->pluck('id', 'name')->toArray();
        // query where category is visible, or, no category and released
        $query->where(function ($query) use ($categoryVisibleCheck) {
            $query->whereIn('item_category_id', $categoryVisibleCheck)->orWhereNull('item_category_id');
        });
        $data = $request->only(['item_category_id', 'name', 'sort', 'artist', 'rarity_id']);
        if (isset($data['item_category_id'])) {
            if ($data['item_category_id'] == 'withoutOption') {
                $query->whereNull('item_category_id');
            } else {
                $query->where('item_category_id', $data['item_category_id']);
            }
        }
        if (isset($data['name'])) {
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        }
        if (isset($data['artist'])) {
            $query->where('artist_id', $data['artist']);
        }
        if (isset($data['rarity_id'])) {
            if ($data['rarity_id'] == 'withoutOption') {
                $query->whereNull('data->rarity_id');
            } else {
                $query->where('data->rarity_id', $data['rarity_id']);
            }
        }

        if (isset($data['sort'])) {
            switch ($data['sort']) {
                case 'alpha':
                    $query->sortAlphabetical();
                    break;
                case 'alpha-reverse':
                    $query->sortAlphabetical(true);
                    break;
                case 'category':
                    $query->sortCategory();
                    break;
                case 'newest':
                    $query->sortNewest();
                    break;
                case 'oldest':
                    $query->sortOldest();
                    break;
            }
        } else {
            $query->sortCategory();
        }

        return view('world.items', [
            'items'       => $query->orderBy('id')->paginate(20)->appends($request->query()),
            'categories'  => ['withoutOption' => 'Without Category'] + ItemCategory::visible(Auth::user() ?? null)->orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'shops'       => Shop::orderBy('sort', 'DESC')->get(),
            'artists'     => User::whereIn('id', Item::whereNotNull('artist_id')->pluck('artist_id')->toArray())->pluck('name', 'id')->toArray(),
            'rarities'    => ['withoutOption' => 'Without Rarity'] + Rarity::orderBy('rarities.sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Shows an individual item's page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getItem($id) {
        $categories = ItemCategory::orderBy('sort', 'DESC')->get();

        $item = Item::where('id', $id)->released(Auth::user() ?? null)->first();

        if (!$item) {
            abort(404);
        }
        if ($item->category && !$item->category->is_visible) {
            if (Auth::check() ? !Auth::user()->isStaff : true) {
                abort(404);
            }
        }

        return view('world.item_page', [
            'item'        => $item,
            'imageUrl'    => $item->imageUrl,
            'name'        => $item->displayName,
            'description' => $item->parsed_description,
            'categories'  => $categories->keyBy('id'),
            'shops'       => Shop::where(function ($shops) {
                if (Auth::check() && Auth::user()->isStaff) {
                    return $shops;
                }

                return $shops->where('is_staff', 0);
            })->whereIn('id', ShopStock::where('item_id', $item->id)->pluck('shop_id')->unique()->toArray())->orderBy('sort', 'DESC')->get(),
        ]);
    }

    /**
     * Shows the character categories page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterCategories(Request $request) {
        $query = CharacterCategory::query();
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%')->orWhere('code', 'LIKE', '%'.$name.'%');
        }

        return view('world.character_categories', [
            'categories' => $query->visible(Auth::user() ?? null)->orderBy('sort', 'DESC')->orderBy('id')->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     *  LEVELS.
     */
    public function getLevels() {
        return view('world.level_index');
    }

    /**
     * Level types.
     *
     * @param mixed $type
     */
    public function getLevelTypes($type) {
        if ($type == 'user') {
            $levels = Level::where('level_type', 'User')->get();
        } elseif ($type == 'character') {
            $levels = Level::where('level_type', 'Character')->get();
        } else {
            abort(404);
        }

        return view('world.level_type_index', [
            'levels' => $levels->paginate(20),
            'type'   => $type,
        ]);
    }

    /**
     * Shows the stats page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getStats(Request $request) {
        $query = Stat::query();

        if ($request->has('name')) {
            $squery->where('name', 'LIKE', '%'.$request->get('name').'%');
        }

        return view('world.stats', [
            'stats' => $query->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows an individual stat's page.
     *
     * @param mixed $abbreviation
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getStat($abbreviation) {
        $stat = Stat::where('abbreviation', $abbreviation)->first();

        return view('world.stat', [
            'stat' => $stat,
        ]);
    }

    /**
     * Shows the skill categories page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSkillCategories(Request $request) {
        $query = SkillCategory::query()->visible(Auth::check() ? Auth::user() : null);
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%');
        }

        return view('world.skill_categories', [
            'categories' => $query->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the skills page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSkills(Request $request) {
        $query = Skill::with('category')->visible(Auth::check() ? Auth::user() : null);

        $categoryVisibleCheck = SkillCategory::visible(Auth::check() ? Auth::user() : null)->pluck('id', 'name')->toArray();
        // query where category is visible, or, no category and visible
        $query->where(function ($query) use ($categoryVisibleCheck) {
            $query->whereIn('skill_category_id', $categoryVisibleCheck)->orWhereNull('skill_category_id');
        });

        $data = $request->only(['skill_category_id', 'name', 'sort']);
        if (isset($data['skill_category_id']) && $data['skill_category_id'] != 'none') {
            $query->where('skill_category_id', $data['skill_category_id']);
        }
        if (isset($data['name'])) {
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        }

        return view('world.skills', [
            'skills'     => $query->paginate(20)->appends($request->query()),
            'categories' => ['none' => 'Any Category'] + SkillCategory::visible(Auth::check() ? Auth::user() : null)->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Shows an individual skill's page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSkill($id) {
        $categories = SkillCategory::get();
        $skill = Skill::where('id', $id)->first();
        if (!$skill) {
            abort(404);
        }

        if (!$skill->is_visible) {
            if (Auth::check() ? !Auth::user()->isStaff : true) {
                abort(404);
            }
        }

        return view('world.skill_page', [
            'skill'       => $skill,
            'imageUrl'    => $skill->imageUrl,
            'name'        => $skill->displayName,
            'description' => $skill->parsed_description,
            'categories'  => $categories->keyBy('id'),
        ]);
    }

    /**
     * Shows the pet categories page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getPetCategories(Request $request) {
        $query = PetCategory::query()->visible(Auth::check() ? Auth::user() : null);
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%');
        }

        return view('world.pet_categories', [
            'categories' => $query->orderBy('sort', 'DESC')->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the pets page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getPets(Request $request) {
        $query = Pet::with('category')->visible(Auth::check() ? Auth::user() : null);

        $categoryVisibleCheck = PetCategory::visible(Auth::check() ? Auth::user() : null)->pluck('id', 'name')->toArray();
        // query where category is visible, or, no category and visible
        $query->where(function ($query) use ($categoryVisibleCheck) {
            $query->whereIn('pet_category_id', $categoryVisibleCheck)->orWhereNull('pet_category_id');
        });

        $data = $request->only(['pet_category_id', 'name', 'sort']);
        if (isset($data['pet_category_id']) && $data['pet_category_id'] != 'none') {
            $query->where('pet_category_id', $data['pet_category_id']);
        }
        if (isset($data['name'])) {
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        }

        if (isset($data['sort'])) {
            switch ($data['sort']) {
                case 'alpha':
                    $query->sortAlphabetical();
                    break;
                case 'alpha-reverse':
                    $query->sortAlphabetical(true);
                    break;
                case 'category':
                    $query->sortCategory();
                    break;
                case 'newest':
                    $query->sortNewest();
                    break;
                case 'oldest':
                    $query->sortOldest();
                    break;
            }
        } else {
            $query->sortCategory();
        }

        return view('world.pets', [
            'pets'       => $query->paginate(20)->appends($request->query()),
            'categories' => ['none' => 'Any Category'] + PetCategory::visible(Auth::check() ? Auth::user() : null)->orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Gets a specific pet page.
     *
     * @param mixed $id
     */
    public function getPet($id) {
        $pet = Pet::with('category')->findOrFail($id);

        if (!$pet->is_visible) {
            if (Auth::check() ? !Auth::user()->isStaff : true) {
                abort(404);
            }
        }

        return view('world.pet_page', [
            'pet' => $pet,
        ]);
    }

    /**
     * Shows the weapon categories page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getWeaponCategories(Request $request) {
        $query = WeaponCategory::query()->visible(Auth::check() ? Auth::user() : null);
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%');
        }

        return view('world.weapon_categories', [
            'categories' => $query->orderBy('sort', 'DESC')->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the weapons page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getWeapons(Request $request) {
        $query = Weapon::with('category')->visible(Auth::check() ? Auth::user() : null);

        $categoryVisibleCheck = WeaponCategory::visible(Auth::check() ? Auth::user() : null)->pluck('id', 'name')->toArray();
        // query where category is visible, or, no category and visible
        $query->where(function ($query) use ($categoryVisibleCheck) {
            $query->whereIn('weapon_category_id', $categoryVisibleCheck)->orWhereNull('weapon_category_id');
        });

        $data = $request->only(['weapon_category_id', 'name', 'sort']);
        if (isset($data['weapon_category_id']) && $data['weapon_category_id'] != 'none') {
            $query->where('weapon_category_id', $data['weapon_category_id']);
        }
        if (isset($data['name'])) {
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        }

        if (isset($data['sort'])) {
            switch ($data['sort']) {
                case 'alpha':
                    $query->sortAlphabetical();
                    break;
                case 'alpha-reverse':
                    $query->sortAlphabetical(true);
                    break;
                case 'category':
                    $query->sortCategory();
                    break;
                case 'newest':
                    $query->sortNewest();
                    break;
                case 'oldest':
                    $query->sortOldest();
                    break;
            }
        } else {
            $query->sortCategory();
        }

        return view('world.weapons', [
            'weapons'    => $query->paginate(20)->appends($request->query()),
            'categories' => ['none' => 'Any Category'] + WeaponCategory::visible(Auth::check() ? Auth::user() : null)->orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Shows an individual weapon's page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getWeapon($id) {
        $categories = WeaponCategory::orderBy('sort', 'DESC')->get();
        $weapon = Weapon::where('id', $id)->first();
        if (!$weapon) {
            abort(404);
        }

        if (!$weapon->is_visible) {
            if (Auth::check() ? !Auth::user()->isStaff : true) {
                abort(404);
            }
        }

        return view('world.weapon_page', [
            'weapon'      => $weapon,
            'imageUrl'    => $weapon->imageUrl,
            'name'        => $weapon->displayName,
            'description' => $weapon->parsed_description,
            'categories'  => $categories->keyBy('id'),
        ]);
    }

    /**
     * Shows the gear categories page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGearCategories(Request $request) {
        $query = GearCategory::query()->visible(Auth::check() ? Auth::user() : null);
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%');
        }

        return view('world.gear_categories', [
            'categories' => $query->orderBy('sort', 'DESC')->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the gears page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGears(Request $request) {
        $query = Gear::with('category')->visible(Auth::check() ? Auth::user() : null);

        $categoryVisibleCheck = GearCategory::visible(Auth::check() ? Auth::user() : null)->pluck('id', 'name')->toArray();
        // query where category is visible, or, no category and visible
        $query->where(function ($query) use ($categoryVisibleCheck) {
            $query->whereIn('gear_category_id', $categoryVisibleCheck)->orWhereNull('gear_category_id');
        });

        $data = $request->only(['gear_category_id', 'name', 'sort']);
        if (isset($data['gear_category_id']) && $data['gear_category_id'] != 'none') {
            $query->where('gear_category_id', $data['gear_category_id']);
        }
        if (isset($data['name'])) {
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        }

        if (isset($data['sort'])) {
            switch ($data['sort']) {
                case 'alpha':
                    $query->sortAlphabetical();
                    break;
                case 'alpha-reverse':
                    $query->sortAlphabetical(true);
                    break;
                case 'category':
                    $query->sortCategory();
                    break;
                case 'newest':
                    $query->sortNewest();
                    break;
                case 'oldest':
                    $query->sortOldest();
                    break;
            }
        } else {
            $query->sortCategory();
        }

        return view('world.gears', [
            'gears'      => $query->paginate(20)->appends($request->query()),
            'categories' => ['none' => 'Any Category'] + GearCategory::visible(Auth::check() ? Auth::user() : null)->orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Shows an individual gear's page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGear($id) {
        $categories = GearCategory::orderBy('sort', 'DESC')->get();
        $gear = Gear::where('id', $id)->first();
        if (!$gear) {
            abort(404);
        }

        if (!$gear->is_visible) {
            if (Auth::check() ? !Auth::user()->isStaff : true) {
                abort(404);
            }
        }

        return view('world.gear_page', [
            'gear'        => $gear,
            'imageUrl'    => $gear->imageUrl,
            'name'        => $gear->displayName,
            'description' => $gear->parsed_description,
            'categories'  => $categories->keyBy('id'),
        ]);
    }

    /**
     * Shows the character classes page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterClasses(Request $request) {
        $query = CharacterClass::query()->visible(Auth::check() ? Auth::user() : null);
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%');
        }

        return view('world.character_class', [
            'classes' => $query->orderBy('name', 'DESC')->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the elements page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getElements(Request $request) {
        $query = Element::query()->visible(Auth::check() ? Auth::user() : null);
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%');
        }

        if (isset($data['sort'])) {
            switch ($data['sort']) {
                case 'alpha':
                    $query->sortAlphabetical();
                    break;
                case 'alpha-reverse':
                    $query->sortAlphabetical(true);
                    break;
                case 'newest':
                    $query->sortNewest();
                    break;
                case 'oldest':
                    $query->sortOldest();
                    break;
            }
        } else {
            $query->sortAlphabetical();
        }

        return view('world.elements', [
            'elements' => $query->orderBy('name', 'DESC')->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows a single element's page.
     *
     * @param mixed $id
     */
    public function getElement($id) {
        $element = Element::where('id', $id)->first();
        if (!$element) {
            abort(404);
        }

        if (!$element->is_visible) {
            if (Auth::check() ? !Auth::user()->isStaff : true) {
                abort(404);
            }
        }

        return view('world.element_page', [
            'element' => $element,
        ]);
    }
}
