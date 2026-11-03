@extends('layouts.dashboard')

@section('title', 'Profil & Legalitas')

@push('scripts')
    @vite(['resources/js/pages/lks-profile.js'])
@endpush

@section('content')
    @include('shared.lks-profile-body')
    @include('lks.profile-dialog', ['org' => $organization])
@endsection
