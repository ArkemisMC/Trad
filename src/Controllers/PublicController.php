<?php

namespace Azuriom\Plugin\Trad\Controllers;

use Azuriom\Plugin\Trad\Controllers\AdminController;
use Azuriom\Http\Controllers\Controller;

class PublicController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $dbType = config("database.default");
        config([
            'database.connections.messages.driver' => 'mysql',
            'database.connections.messages.host' => setting("trad.host") ?? config("database.connections." . $dbType . ".host"),
            'database.connections.messages.port' => setting("trad.port") ?? config("database.connections." . $dbType . ".port"),
            'database.connections.messages.username' => setting("trad.username") ?? config("database.connections." . $dbType . ".username"),
            'database.connections.messages.password' => setting("trad.password") ?? config("database.connections." . $dbType . ".password"),
            'database.connections.messages.database' => "messages"
        ]);
        return view('trad::public.index');
    }
}
