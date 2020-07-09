@extends('layouts.auth')

@section('content')
    <p class="text-center">{{ json_decode(file_get_contents(storage_path('framework/down')), true)['message'] }}</p>
@endsection
