@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Manajemen User</h1>
        <a href="{{ route('superadmin.users.create') }}" class="btn btn-primary mb-3">Tambah User</a>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
