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

    public function getDefaultLang() {
        return Langs::on("messages")->where("id", setting('trad.default_lang_id'))->first();
    }

    public static function configDatabase() {
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
        $defLang = $this->getDefaultLang();
        $perPage = setting("trad.per_page");
        $page = isset(request()->page) ? request()->page - 1 : 0;
        $lang = null;
        $msgs = null;
        $pagination = null;
        if($defLang->id == $lang_id) {
            $lang = $defLang;
            $ordering = "IF(msg_key = msg_value OR msg_key = '', msg_key, '') DESC, IF(comments != '✔', '', msg_key), msg_key";
            $msgs = Messages::on("messages")->table($lang->table_name)->orderByRaw($ordering)->offset($page * $perPage)->limit($perPage)->get();
            $pagination = Messages::on("messages")->table($lang->table_name)->orderByRaw($ordering)->paginate($perPage);
        } else {
            $lang = Langs::on("messages")->where("id", $lang_id)->first();
            $tableA = $defLang->table_name;
            $tableB = $lang->table_name;
            $ordering = "IF(" . $tableB . ".msg_key is null OR " . $tableB . ".msg_key = " . $tableB . ".msg_value, " . $tableB . ".msg_key, ''), " . $tableB . ".msg_key";
            $beginMsgBuilder = Messages::on("messages")->table($tableA)->leftJoin($tableB, $tableA . '.msg_key', '=', $tableB . '.msg_key')->orderByRaw($ordering);
            $pagination = $beginMsgBuilder->paginate($perPage);
            $msgs = $beginMsgBuilder->offset($page * $perPage)->limit($perPage)->select($tableA . '.msg_key as msg_key', $tableB .'.msg_value')->get();
        }
        return view('trad::public.message', compact('lang', 'msgs', 'msg_key', 'pagination', 'defLang'));
    }

    public function fetch(Request $request) {
        $this->configDatabase();
        $lang = Langs::on("messages")->where("id", "=", $request->lang_id)->first();
        $msg = Messages::on("messages")->table($lang->table_name)->where("msg_key", $request->msg_key)->first();
        if($msg != null) {
            $possibleDefMsg = Messages::on("messages")->table($this->getDefaultLang()->table_name)->where("msg_key", $request->msg_key)->get();
            $msg->msg_value_other = count($possibleDefMsg) >= 1 ? $possibleDefMsg[0]->msg_value : "";
        } else {
            Messages::on("messages")->table($lang->table_name)->insert([
                'msg_key' => $request->msg_key,
                'msg_value' => $request->msg_key
            ]);
            $msg = Messages::on("messages")->table($lang->table_name)->where("msg_key", $request->msg_key)->first();
        }
        return json_encode($msg);
    }

    public function suggestion(Request $request) {
        $this->configDatabase();
        $lang = Langs::on("messages")->where("id", $request->lang_id)->first();
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
