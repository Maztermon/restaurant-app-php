@extends('layouts.app')

@section('title', 'Restaurant App')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Restaurant List</div>

                <div class="card-body">
                    <restaurant-list></restaurant-list>
                </div>
            </div>
        </div>
    </div>
@endsection
