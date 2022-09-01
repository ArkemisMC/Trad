@extends('layouts.app')

@section('title', trans('trad::public.langs.title'))

@section('content')
    <div class="row" id="trad">
        <div class="col-12">
            <div class="card">
                <div class="card-body rounded">
                    <h3 class="mb-3">{{ trans("trad::public.langs.information") }}</h3>
                    <div style="display: flex;">
                        @foreach($langs as $lang)
                            <a class="col-2 text-center btn btn-primary" href="{{ route('trad.message', ['lang_id' => $lang->id]) }}">
                                {{ $lang->lang_name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection