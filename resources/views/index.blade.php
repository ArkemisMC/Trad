@extends('admin.layouts.admin')

@section('title', 'Traduction')

@section('content')
    <div class="row" id="internalstats">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h3>Temps joués par tout les joueurs</h3>
                    <p>Per page: {{ setting('trad.per_page') }}</p>
                    <p>Lang par defaut: {{ setting('trad.default_lang_id') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection