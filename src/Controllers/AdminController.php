<?php

namespace Azuriom\Plugin\Trad\Controllers;

use Azuriom\Http\Controllers\Controller;

class AdminController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('trad::index');
    }
}
