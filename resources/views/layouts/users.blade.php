@extends('layouts/main')

@section('title', 'Dashboard')


@section('content')

<body class="bg-theme bg-theme1">
    <div id="wrapper">
       
            @include('panels/sidebar')
            @include('panels/navbar')


        <div class="content-wrapper">
            <div class="container-fluid">
        
            <!--Start Custromer Content-->
                <section id="customer-list">
                    <h5>Customer Listing</h5>
                    @foreach ($users as $user)
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                              <div class="card-body">
                                <div class="table-responsive">
                                  <table class="table">
                                    <thead>
                                      <tr>
                                        <th scope="col">Sr. No.</th>
                                        <th scope="col">Customer ID</th>
                                        <th scope="col">Created Date</th>
                                        <th scope="col">Customer Name</th>
                                        <th scope="col">Email Address</th>
                                        <th scope="col">Phone Number</th>
                                        <th scope="col">User Name</th>
                                        <th scope="col">Action</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <tr>
                                        <th scope="row">1</th>
                                        <td>{{$user->customer_id}}</td>
                                        <td>{{$user->created_at}}</td>
                                        <td>{{$user->first_name." ".$user->last_name}}</td>
                                        <td>{{$user->email}}</td>
                                        <td>{{$user->mobile_no}}</td>
                                        <td>{{$user->username}}</td>
                                        <td>
                                            <a href="{{route('view_user')}}" class="btn btn-success">View</a>
                                            <a href="{{route('edit_user')}}" class="btn btn-warning">Edit</a>
                                            <a href="" class="btn btn-danger" type="button" data-toggle="modal" data-target="#exampleModalCenter">Delete</a>
                                        </td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                            </div>
                          </div>
                    </div>
                    @endforeach
                </section>
            <!--End Custromer Content-->
              
            <!--start overlay-->
                  <div class="overlay toggle-menu"></div>
                <!--end overlay-->
                
            </div>
            <!-- End container-fluid-->
            
            </div>

            <!--Start Back To Top Button-->
            <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
            <!--End Back To Top Button-->

            @include('modals/delete_users')    
    
        </div>
    
        
    @endsection  