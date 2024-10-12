@extends('layouts.app')

@section('content')
<div class="container">
    <div id="app">
        <customer-list></customer-list>
    </div>    
</div>

@endsection

@section('scripts')
@vite(['resources/js/app.js', 'resources/css/app.css']) <!-- Correct Vite directive for JS and CSS -->
@endsection
