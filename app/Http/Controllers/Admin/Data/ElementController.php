<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\Element\Element;
use App\Models\Element\Typing;
use App\Services\ElementService;
use App\Services\TypingManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ElementController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Admin / Element Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of element categories and elements.
    |
    */

    /**********************************************************************************************

        ELEMENTS

    **********************************************************************************************/

    /**
     * Shows the element index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex(Request $request) {
        $query = Element::query();
        $data = $request->only(['name']);
        if (isset($data['name'])) {
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        }

        return view('admin.elements.elements', [
            'elements' => $query->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the create element page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateElement() {
        return view('admin.elements.create_edit_element', [
            'element'  => new Element,
            'elements' => Element::orderBy('name')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Shows the edit element page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditElement($id) {
        $element = Element::find($id);
        if (!$element) {
            abort(404);
        }

        return view('admin.elements.create_edit_element', [
            'element'  => $element,
            'elements' => Element::where('id', '!=', $element->id)->orderBy('name')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Creates or edits an element.
     *
     * @param App\Services\ElementService $service
     * @param int|null                    $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditElement(Request $request, ElementService $service, $id = null) {
        $id ? $request->validate(Element::$updateRules) : $request->validate(Element::$createRules);
        $data = $request->only([
            'name', 'description', 'image', 'remove_image', 'weakness_id', 'weakness_multiplier', 'immunity_id', 'colour', 'is_visible',
        ]);
        if ($id && $service->updateElement(Element::find($id), $data, Auth::user())) {
            flash('Element updated successfully.')->success();
        } elseif (!$id && $element = $service->createElement($data, Auth::user())) {
            flash('Element created successfully.')->success();

            return redirect()->to('admin/data/elements/edit/'.$element->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the element deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteElement($id) {
        $element = Element::find($id);

        return view('admin.elements._delete_element', [
            'element' => $element,
        ]);
    }

    /**
     * Creates or edits an element.
     *
     * @param App\Services\ElementService $service
     * @param int                         $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteElement(Request $request, ElementService $service, $id) {
        if ($id && $service->deleteElement(Element::find($id), Auth::user())) {
            flash('Element deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }

            return redirect()->back();
        }

        return redirect()->to('admin/data/elements');
    }

    /**********************************************************************************************

        TYPING

    **********************************************************************************************/

    /**
     * Adds typing row for a model.
     */
    public function postTyping(Request $request, TypingManager $service) {
        $data = $request->only(['type', 'typing_model', 'typing_id', 'element_ids']);
        if (isset($data['type']) && $data['type']) {
            $type = Typing::find($data['type']);
            if (!$type) {
                flash('Invalid typing.')->error();

                return response()->json([
                    'error'   => 'Invalid typing.',
                ], 400);
            }
            if (!$service->editTyping($type, $data['element_ids'] ?? null, Auth::user())) {
                flash('Failed to edit typing.')->error();

                return response()->json([
                    'error'   => $service->errors()->getMessages()['error'][0],
                ], 400);
            }
        } elseif (!$type = $service->createTyping(urldecode($data['typing_model']), $data['typing_id'], $data['element_ids'] ?? null, Auth::user())) {
            flash('Failed to create typing.')->error();

            return response()->json([
                'error'   => $service->errors()->getMessages()['error'][0],
            ], 400);
        }

        flash('Typing '.($type ? 'edited' : 'created').' successfully.')->success();

        return response()->json([
            'success' => 'Typing added successfully.',
        ]);
    }

    /**
     * gets the delete typing modal.
     *
     * @param mixed $id
     */
    public function getDeleteTyping($id) {
        $typing = Typing::find($id);
        if (!$typing) {
            abort(404);
        }

        return view('admin.elements._delete_typing', [
            'typing' => $typing,
        ]);
    }

    /**
     * deletes a typing.
     *
     * @param mixed $id
     */
    public function postDeleteTyping(Request $request, TypingManager $service, $id) {
        if ($id && $service->deleteTyping(Typing::find($id), Auth::user())) {
            flash('Typing deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
