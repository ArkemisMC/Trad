@extends('admin.layouts.admin')

@section('title', 'Traduction')

@section('content')
    <div class="row" id="internalstats">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h3>Temps joués par tout les joueurs</h3>
                    <canvas id="global-time"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection