@extends('layouts.app')

@section('title', trans('trad::public.msgs.title'))

@push('styles')
    <style type="text/css">
        .message {
            cursor: pointer;
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
                    <h3 class="mb-3">{{ $lang->lang_name }}</h3>
                    <div class="row">
                        <div class="col-5">
                            @foreach($msgs as $msg)
                                <p class="message" onclick="selectMessage('{{ $msg->msg_key }}')" id="left-msg-{{ $msg->msg_key }}">
                                    {{ $msg->msg_value }}
                                </p>
                            @endforeach
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
                                <input type="textarea" class="form-control" id="msg_value_other" name="msg_value_other" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="msg_value">{{ trans('trad::public.msgs.msg_value') }}</label>
                                @if(Auth::user()->can('trad.accept'))
                                    <p style="display: flex;">
                                        <input type="textarea" class="form-control" id="msg_value" name="msg_value">
                                        <button class="btn btn-success" onclick="saveMessage()">
                                            <i class="bi bi-save"></i>
                                        </button>
                                    </p>
                                @else
                                    <input type="textarea" class="form-control" id="msg_value" name="msg_value" readonly>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="msg_suggestion">{{ trans('trad::public.msgs.msg_suggestion') }}</label>
                                <p style="display: flex;">
                                    <input type="textarea" class="form-control" id="msg_suggestion" name="msg_suggestion">
                                    <button class="btn btn-success" onclick="saveSuggestion()">
                                        <i class="bi bi-save"></i>
                                    </button>
                                </p>
                            </div>
                            @if(Auth::user()->can('trad.accept'))
                                <button class="btn btn-success" onclick="acceptSuggestion()">
                                    {{ trans('trad::public.msgs.accept') }}
                                </button>
                            @endif
                            <p id="loading" style="visibility: hidden;">{{ trans('messages.loading') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-scripts')
    <script type="text/javascript">
        let msg_key = "{{ $msg_key }}";
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
                document.getElementById("msg_value").value = d.msg_value;
                document.getElementById("msg_suggestion").value = d.msg_suggestion == undefined ? "" : d.msg_suggestion;
                document.getElementById("comments").value = d.comments;

                document.getElementById("left-msg-" + key).innerHTML = d.msg_value;
            })
        }
        setTimeout(() => selectMessage(msg_key), 100);

        @if(Auth::user()->can('trad.accept'))
            function saveMessage() {
                axios.post('{{ route("trad.save") }}', { msg_key: msg_key, lang_id: '{{ $lang->id }}', msg_value: document.getElementById("msg_value").value }).then((response) => {
                    selectMessage(msg_key);
                })
            }
            function acceptSuggestion() {
                axios.post('{{ route("trad.accept") }}', { msg_key: msg_key, lang_id: '{{ $lang->id }}', msg_value: document.getElementById("msg_value").value, msg_suggestion: document.getElementById("msg_suggestion").value }).then((response) => {
                    selectMessage(msg_key);
                })
            }
        @endif

        function saveSuggestion() {
            axios.post('{{ route("trad.suggestion") }}', { msg_key: msg_key, lang_id: '{{ $lang->id }}', msg_suggestion: document.getElementById("msg_suggestion").value }).then((response) => {
                selectMessage(msg_key);
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