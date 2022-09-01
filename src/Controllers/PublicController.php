<?php

namespace Azuriom\Plugin\Trad\Controllers;

use Azuriom\Plugin\Trad\Controllers\AdminController;
use Azuriom\Plugin\Trad\Models\Langs;
use Azuriom\Plugin\Trad\Models\Messages;
use Azuriom\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PublicController extends Controller
{

    public function configDatabase() {
        $dbType = config("database.default");
        config([
            'database.connections.messages.driver' => 'mysql',
            'database.connections.messages.host' => setting("trad.host") ?? config("database.connections." . $dbType . ".host"),
            'database.connections.messages.port' => setting("trad.port") ?? config("database.connections." . $dbType . ".port"),
            'database.connections.messages.username' => setting("trad.username") ?? config("database.connections." . $dbType . ".username"),
            'database.connections.messages.password' => setting("trad.password") ?? config("database.connections." . $dbType . ".password"),
            'database.connections.messages.database' => "messages"
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $this->configDatabase();
        $langs = Langs::on("messages")->get();
        return view('trad::public.index', compact('langs'));
    }

    public function show($lang_id) {
        return $this->show2($lang_id, "");
    }

    public function show2($lang_id, $msg_key) {
        $this->configDatabase();
        $lang = Langs::on("messages")->where("id", "=", $lang_id)->first();
        $msgs = Messages::on("messages")->table($lang->table_name)->limit(15)->get();
        return view('trad::public.message', compact('lang', 'msgs', 'msg_key'));
    }

    public function fetch(Request $request) {
        $this->configDatabase();
        $lang = Langs::on("messages")->where("id", "=", $request->lang_id)->first();
        return json_encode(Messages::on("messages")->table($lang->table_name)->where("msg_key", "=", $request->msg_key)->first());
    }

    public function suggestion(Request $request) {
        $this->configDatabase();
        $lang = Langs::on("messages")->where("id", "=", $request->lang_id)->first();
        Messages::on("messages")->table($lang->table_name)->where("msg_key", "=", $request->msg_key)->update([
            'msg_suggestion' => $request->msg_suggestion,
            'suggestionner' => Auth::user()->name
        ]);
        return json_encode(array("result" => "200"));
    }

    public function accept(Request $request) {
        $this->configDatabase();
        $lang = Langs::on("messages")->where("id", "=", $request->lang_id)->first();
        Messages::on("messages")->table($lang->table_name)->where("msg_key", "=", $request->msg_key)->update([
            'msg_value' => $request->msg_suggestion ?? $request->msg_value,
            'msg_suggestion' => "",
            'suggestion_accepter' => Auth::user()->name
        ]);
        return json_encode(array("result" => "200"));
    }

    public function save(Request $request) {
        $this->configDatabase();
        $lang = Langs::on("messages")->where("id", "=", $request->lang_id)->first();
        Messages::on("messages")->table($lang->table_name)->where("msg_key", "=", $request->msg_key)->update([
            'msg_value' => $request->msg_value,
            'msg_suggestion' => "",
            'suggestionner' => "",
            'suggestion_accepter' => ""
        ]);
        return json_encode(array("result" => "200"));
    }
}
