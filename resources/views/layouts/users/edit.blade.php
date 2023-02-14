@extends('layouts/main')
@section('title','Edit User')

@section('content')

<div id="wrapper">

    @include('panels/sidebar')
    @include('panels/navbar')

<div class="clearfix"></div>

<div class="content-wrapper">
    <div class="container-fluid">

    <!--Start Custromer Content-->
    <section id="customer-list">
        <h5>Edit</h5>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                  <hr>
                   <form>
                        <div class="row">
                            <div class="col-md-12 col-sm-6 form-group">
                                <label for="input-1">Customer ID</label>
                                <input type="text" class="form-control" id="input-1" value="CA#123">
                            </div>
                            <div class="col-md-12 col-sm-6 form-group">
                                <label for="input-1">Created Date</label>
                                <input type="text" class="form-control" id="input-1" value="01-02-2023">
                            </div>
                            <div class="col-md-6 col-sm-12 form-group">
                                <label for="input-1">Customer Name</label>
                                <input type="text" class="form-control" id="input-1" value="John Doe">
                            </div>
                            <div class="col-md-6 col-sm-12 form-group">
                                <label for="input-2">Email Address</label>
                                <input type="text" class="form-control" id="input-2" value="john123@gmail.com">
                            </div>
                            <div class="col-md-6 col-sm-12 form-group">
                                <label for="input-3">Phone Number</label>
                                <input type="text" class="form-control" id="input-3" value="+1 457895456">
                            </div>
                            <div class="col-md-6 col-sm-12 form-group">
                                <label for="input-4">User Name</label>
                                <input type="text" class="form-control" id="input-4" value="john_d12">
                            </div>
                            <div class="col-md-12 form-group">
                                <a href="customers.html"><button type="button" class="btn btn-success px-5">Update</button></a> 
                                <a href="#"><button type="button" class="btn btn-danger px-5">Cancel</button></a> 
                            </div>
                        </div>
                 </form>
                </div>
                </div>
             </div>
        </div>
    </section>
<!--End Custromer Content-->
  
<!--start overlay-->
      <div class="overlay toggle-menu"></div>
    <!--end overlay-->
    
</div>
<!-- End container-fluid-->

</div><!--End content-wrapper-->
<!--Start Back To Top Button-->
<a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
<!--End Back To Top Button-->




</div>

    
@endsection