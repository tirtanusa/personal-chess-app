@props([
    'title' => config('app.name', 'Gambit Arena'),
    'activeNav' => 'home',
    'headerVariant' => 'default',
    'headerTitle' => '',
    'backUrl' => null,
    'showBottomNav' => true,
    'avatarUrl' => null,
    'profileName' => 'Alexey V.',
    'profileHandle' => '@alexey_v',
    'profileRating' => '1842',
])

@extends('layouts.app', [
    'title' => $title,
    'activeNav' => $activeNav,
    'headerVariant' => $headerVariant,
    'headerTitle' => $headerTitle,
    'backUrl' => $backUrl,
    'showBottomNav' => $showBottomNav,
    'avatarUrl' => $avatarUrl,
    'profileName' => $profileName,
    'profileHandle' => $profileHandle,
    'profileRating' => $profileRating,
])

@section('content')
    {{ $slot }}
@endsection
