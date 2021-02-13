@extends('layouts.app')

@section('content')
    <h2 class="my-4 bg-success text-light p-2 text-center">User List</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Sl</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Photo</th>
                    <th>Option</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Abdul Majid</td>
                    <td>abdulmajid@gmail.com</td>
                    <td><img src="" alt="Photo"></td>
                    <td>
                        <a href="#" class="btn btn-flat btn-sm btn-success">View</a>
                        <a href="#" class="btn btn-flat btn-sm btn-primary">Edit</a>
                        <a href="#" class="btn btn-flat btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection