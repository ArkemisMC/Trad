@extends('layouts.app')

@section('title', trans('trad::public.msgs.title'))

@push('styles')
    <style type="text/css">
        .message {
            cursor: pointer;
        }
        .empty-message {
            color: red;
        }
        .checked-message {
            color: green;
        }
        .form-right {
            width: 50%;
            padding-left: 1%;
        }
        .form-left {
            width: 50%;
            padding-right: 1%;
        }
    </style>
@endpush

@section('content')
    <div class="row" id="trad-message">
        <div class="col-12">
            <div class="card">
                <div class="card-body rounded">
                    <h3 class="mb-3">
                        <label class="form-label" for="auto-next" style="font-size: initial;">Auto-next</label>
                        <input type="checkbox" id="auto-next" name="auto-next" checked>
                    </h3>
                    <div class="row">
                        <div class="col-5">
                            <?php
                            ?>
                            @foreach($messages as $msg)
                                <p class="message @if($msg->msg_key == $msg->msg_fr || $msg->msg_key == $msg->msg_en || $msg->msg_fr == null || $msg->msg_fr == '' || $msg->msg_en == null) empty-message @else checked-message @endif" onclick="selectMessage('{{ $msg->msg_key }}')"
                                    data-comments="{{ $msg->comments }}" id="left-msg-{{ $msg->msg_key }}"
                                    data-msgid="{{ $msg->id }}"
                                    @foreach($langs as $lang)
                                        data-msg_{{ $lang->lang_key }}="{{ $msg->{ 'msg_' . $lang->lang_key } }}"
                                    @endforeach
                                    >
                                    {{ $msg->msg_key }}
                                </p>
                            @endforeach
                        </div>
                        <div class="col-7">
                            <div class="mb-3" style="display: flex;">
                                <input type="hidden" id="msgid">
                                <div class="form-left">
                                    <label class="form-label" for="msg_key">{{ trans('trad::public.msgs.msg_key') }}</label>
                                    <input type="text" class="form-control" id="msg_key" name="msg_key" readonly>
                                </div>
                                <div class="form-right">
                                    <label class="form-label" for="comments">{{ trans('trad::public.msgs.comments') }}</label>
                                    <input type="text" class="form-control" id="comments" name="comments" readonly>
                                </div>
                            </div>
                            @foreach($langs as $lang)
                                <div class="mb-3">
                                    <label class="form-label" for="msg_{{ $lang->lang_key }}">{{ trans('trad::public.msgs.msg_lang', ['lang' => $lang->lang_name]) }}</label>
                                    <textarea class="form-control" id="msg_{{ $lang->lang_key }}" name="msg_{{ $lang->lang_key }}" {{ $lang->lang_key == "fr" ? "readonly disabled" : "" }}></textarea>
                                </div>
                            @endforeach
                            <div class="mb-3">
                                <p style="display: flex;">
                                    <button class="btn btn-success" style="margin-left: 8px;" onclick="saveMessage()">
                                        <i class="bi bi-save"></i>
                                    </button>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-scripts')
    <script type="text/javascript">
        function getMessage(langKey) {
            return fixFormatting(document.getElementById(langKey).value);
        }

        function parseFormatting(msg) {
            return msg == undefined || msg == null ? "" : msg.replaceAll("%n%", "\n");
        }

        function fixFormatting(msg) {
            return msg == undefined || msg == null ? "" : msg.replaceAll("\n", "%n%");
        }

        let msg_key = "{{ $msg_key }}";
        function nextMessage(oldKey) {
            if(document.getElementById("auto-next").value) {
                let leftMsg = document.getElementById("left-msg-" + oldKey);
                if(leftMsg != undefined)
                    leftMsg.remove();
                let possibleMsg = document.querySelectorAll('*[id^="left-msg"]');
                if(possibleMsg.length > 0) {
                    selectMessage(possibleMsg[0].id.replace("left-msg-", ""), true);
                }
            } else {
                selectMessage(oldKey, true);
            }
        }

        function selectMessage(key, removeOld = false) {
            if(key == "")
                return;
            msg_key = key;
            selectWikiPage('{{ route("trad.show", ["msg_key" => "MSG_KEY"]) . (isset($_GET["page"]) ? "?page=" . $_GET["page"] : "") }}'.replace("MSG_KEY", key));
            let msg = document.getElementById("left-msg-" + key);
            let d = msg.dataset;
            document.getElementById("msg_key").value = key;
            @foreach($langs as $lang)
                document.getElementById("msg_{{ $lang->lang_key }}").value = parseFormatting(d.msg_{{ $lang->lang_key }});
            @endforeach
            document.getElementById("comments").value = d.comments;
            document.getElementById("msgid").value = d.msgid;

            if(msg != undefined && d.msg_value != "" && d.msg_value != d.msg_key && removeOld)
            msg.remove();
        }
        if(msg_key != "")
            setTimeout(() => selectMessage(msg_key), 100);

        function saveMessage() {
            var params = { msg_key: msg_key, id: document.getElementById("msgid").value, comments: document.getElementById("comments").value };
            @foreach($langs as $lang)
                params["msg_{{ $lang->lang_key }}"] = getMessage('msg_{{ $lang->lang_key }}');
            @endforeach
            axios.post('{{ route("trad.save") }}', params).then((response) => {
                nextMessage(msg_key);
            })
        }

        function selectWikiPage(href, replaceState = false) {
            /*const tab = bootstrap.Tab.getOrCreateInstance(element);
            tab.show();*/
            if (replaceState) {
                window.history.replaceState({}, '', href);
            } else {
                window.history.pushState({}, '', href);
            }
        }

        window.onpopstate = function(e) {
            let parts = e.target.location.href.split("/");
            selectMessage(parts[parts.length - 1]);
        };
    </script>
@endpush