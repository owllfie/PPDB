@extends('errors.minimal')

@section('title', __('Access Forbidden'))
@section('code', '403')
@section('message', __('Restricted Area'))
@section('description', __('You don\'t have the necessary permissions to access this page. If you believe this is an error, please contact your administrator.'))
@section('icon')
    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
@endsection
