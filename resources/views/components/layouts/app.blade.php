@props([
    'title' => config('app.name', 'Gambit Arena'),
    'activeNav' => 'home',
    'headerVariant' => 'default',
    'headerTitle' => '',
    'backUrl' => null,
    'showBottomNav' => true,
])

@extends('layouts.app', [
    'title' => $title,
    'activeNav' => $activeNav,
    'headerVariant' => $headerVariant,
    'headerTitle' => $headerTitle,
    'backUrl' => $backUrl,
    'showBottomNav' => $showBottomNav,
])

@section('content')
    {{ $slot }}
@endsection
