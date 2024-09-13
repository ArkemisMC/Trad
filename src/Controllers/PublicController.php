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
        return $this->show("");
    }

    public function show($msg_key) {
        $this->configDatabase();
        $langs = Langs::on("messages")->get();
        $messages = Messages::on("messages")->where("msg_fr", "!=", "")->orderByRaw("IF(msg_key = msg_en OR ((msg_en = '' OR msg_en is null) AND msg_fr != ''), msg_key, '') DESC, IF(comments != '✔', '', msg_key), msg_key")->get();
        return view('trad::public.index', compact('langs', 'messages', 'msg_key'));
    }

    public function save(Request $request) {
        $this->configDatabase();
        $langs = Langs::on("messages")->get();
        $content = [];
        foreach($langs as $lang) {
            if($lang->lang_key != "fr") // no updated of french
                $content["msg_" . $lang->lang_key] = $request->{ "msg_" . $lang->lang_key };
        }
        if($content["msg_en"] != null && $content["msg_en"] != "" && $request["comments"] == "V2") {// set english
            $content["comments"] = "V3 Automatique";
        }
        Messages::on("messages")->where("id", "=", $request->id)->update($content);
        return json_encode([ "result" => "200" ]);
    }
}
