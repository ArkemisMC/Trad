<?php

namespace Azuriom\Plugin\Trad\Controllers;

use Azuriom\Plugin\Trad\Controllers\PublicController;
use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\Trad\Models\Langs;
use Illuminate\Http\Request;
use Azuriom\Models\Setting;

class AdminController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        PublicController::configDatabase();
        $langs = Langs::on("messages")->get();
        return view('trad::admin.index', compact('langs'));
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'per_page' => ['required', 'integer', 'min:5'],
            'default_lang_id' => ['required', 'integer']
        ]);
        Setting::updateSettings([
            'trad.per_page' => $request->input('per_page'),
            'trad.default_lang_id' => $request->input('default_lang_id')
        ]);

        return redirect()->route('trad.admin.index')->with('success', trans('trad::admin.setting.updated'));
    }
}
