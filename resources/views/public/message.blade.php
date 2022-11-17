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
                        {{ $lang->lang_name }}
                        <label class="form-label" for="auto-next" style="font-size: initial;">Auto-next</label>
                        <input type="checkbox" id="auto-next" name="auto-next" checked>
                        <span style="float: right;">{{ $amountTranslated . "/" . $pagination->total() }}</span>
                        <span id="loading" style="visibility: hidden; float: right; margin-right: 8px;">{{ trans('messages.loading') }}</span>
                    </h3>
                    <div class="row">
                        <div class="col-5">
                            <?php
                            ?>
                            @foreach($msgs as $msg)
                                <p class="message @if($msg->msg_key == $msg->msg_value || ($msg->msg_suggestion == null && $msg->msg_value == null)) empty-message @endif  @if($msg->comments == '✔') checked-message @endif" onclick="selectMessage('{{ $msg->msg_key }}')" id="left-msg-{{ $msg->msg_key }}">
                                    {{ $msg->msg_value ?? $msg->msg_suggestion ?? $msg->msg_key }}
                                </p>
                            @endforeach
                            {{ $pagination->links() }}
                        </div>
                        <div class="col-7">
                            <div class="mb-3" style="display: flex;">
                                <div class="form-left">
                                    <label class="form-label" for="msg_key">{{ trans('trad::public.msgs.msg_key') }}</label>
                                    <input type="text" class="form-control" id="msg_key" name="msg_key" readonly value="{{ $msg_key }}">
                                </div>
                                <div class="form-right">
                                    <label class="form-label" for="comments">{{ trans('trad::public.msgs.comments') }}</label>
                                    <input type="text" class="form-control" id="comments" name="comments" readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="msg_value_other">{{ trans('trad::public.msgs.msg_value_other') }}</label>
                                <textarea class="form-control" id="msg_value_other" name="msg_value_other" readonly></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="msg_value">{{ trans('trad::public.msgs.msg_value') }}</label>
                                @if(Auth::user()->can('trad.accept'))
                                    <p style="display: flex;">
                                        <textarea class="form-control" id="msg_value" name="msg_value"></textarea>
                                        <button class="btn btn-success" style="margin-left: 8px;" onclick="saveMessage()">
                                            <i class="bi bi-save"></i>
                                        </button>
                                    </p>
                                @else
                                    <textarea class="form-control" id="msg_value" name="msg_value" readonly></textarea>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="msg_suggestion">{{ trans('trad::public.msgs.msg_suggestion') }}</label>
                                <p style="display: flex;">
                                    <textarea class="form-control" id="msg_suggestion" name="msg_suggestion"></textarea>
                                    <button class="btn btn-success" style="margin-left: 8px;" onclick="saveSuggestion()">
                                        <i class="bi bi-save"></i>
                                    </button>
                                </p>
                            </div>
                            @if(Auth::user()->can('trad.accept'))
                                <button id="suggestion-accept" class="btn btn-success" onclick="acceptSuggestion()">
                                    {{ trans('trad::public.msgs.accept') }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-scripts')
    <script type="text/javascript">
        function getMessage(key) {
            return fixFormatting(document.getElementById(key).value);
        }

        function parseFormatting(msg) {
            return msg == undefined ? "" : msg.replace("%n%", "\n");
        }

        function fixFormatting(msg) {
            return msg == undefined ? "" : msg.replace("\n", "%n%");
        }

        let msg_key = "{{ $msg_key }}";
        function nextMessage(oldKey) {
            if(document.getElementById("auto-next").value) {
                let leftMsg = document.getElementById("left-msg-" + oldKey);
                if(leftMsg != undefined)
                    leftMsg.remove();
                let possibleMsg = document.querySelectorAll('*[id^="left-msg"]');
                if(possibleMsg.length > 0) {
                    selectMessage(possibleMsg[0].id.replace("left-msg-", ""));
                }
            } else {
                selectMessage(oldKey);
            }
        }

        function selectMessage(key) {
            if(key == "")
                return;
            msg_key = key;
            selectWikiPage('{{ route("trad.message-specific", ["lang_id" => "$lang->id", "msg_key" => "MSG_KEY"]) }}'.replace("MSG_KEY", key));
            let loading = document.getElementById("loading");
            loading.style.visibility = "visible";
            axios.post('{{ route("trad.fetch") }}', { msg_key: key, lang_id: '{{ $lang->id }}' }).then((response) => {
                loading.style.visibility = "hidden";
                let d = response.data;
                document.getElementById("msg_key").value = d.msg_key;
                document.getElementById("msg_value").value = parseFormatting(d.msg_value)
                document.getElementById("msg_value_other").value = parseFormatting(d.msg_value_other);
                document.getElementById("msg_suggestion").value = parseFormatting(d.msg_suggestion);
                document.getElementById("comments").value = d.comments;

                let leftMsg = document.getElementById("left-msg-" + key);
                if(leftMsg != undefined && d.msg_value != "" && d.msg_value != d.msg_key)
                    leftMsg.remove();

                document.getElementById("suggestion-accept").style.visibility = (fixFormatting(d.msg_suggestion) == "" ? "hidden" : null);
            })
        }
        setTimeout(() => selectMessage(msg_key), 100);

        @if(Auth::user()->can('trad.accept'))
            function saveMessage() {
                axios.post('{{ route("trad.save") }}', { msg_key: msg_key, lang_id: '{{ $lang->id }}', msg_value: getMessage("msg_value") }).then((response) => {
                    nextMessage(msg_key);
                })
            }
            function acceptSuggestion() {
                axios.post('{{ route("trad.accept") }}', { msg_key: msg_key, lang_id: '{{ $lang->id }}', msg_value: getMessage("msg_value"), msg_suggestion: getMessage("msg_suggestion") }).then((response) => {
                    nextMessage(msg_key);
                })
            }
        @endif

        function saveSuggestion() {
            axios.post('{{ route("trad.suggestion") }}', { msg_key: msg_key, lang_id: '{{ $lang->id }}', msg_suggestion: getMessage("msg_suggestion") }).then((response) => {
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