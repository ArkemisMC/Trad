@extends('admin.layouts.admin')

@section('title', 'Traduction')

@section('content')
    <div class="row" id="internalstats">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h3>Temps joués par tout les joueurs</h3>
                    <form action="{{ route('trad.admin.setting', null) }}" name="setting-form" method="POST">
                        @include('trad::admin._form')
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> {{ trans('messages.actions.save') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection