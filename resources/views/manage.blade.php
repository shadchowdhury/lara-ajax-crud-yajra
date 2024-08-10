@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- <h3 class="text-info mt-2">Add New Product</h3> -->
        <div class="row mt-2">
            <div class="col-md-4">
                <div class="card">
                    <h5 class="card-header text-success text-center">Add New Product</h5>
                    <div class="card-body">
                        <!-- <span class="alert alert-success msg" style="display:none"></span> -->

                        <input value="{{ old('name') }}" type="text" class="mt-3 form-control name" placeholder="Enter Product Name">
                        <span class="text-danger error_name"></span>

                        <textarea class="mt-3 form-control description" cols="30" rows="5" type="text" placeholder="Enter Product Description">{{ old('description') }}</textarea>
                        <span class="text-danger error_description"></span>

                        <button class="btn btn-success form-control mt-3" id="btnSave">Save</button>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-striped table-bordered yajra-datatable">
                            <thead>
                                <tr>
                                    <th>#Sl</th>
                                    <th>Name</th>
                                    <th>Product Description</th>
                                    <th width="150px">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Product</h5>
                </div>

                <div class="modal-body">
                    <input id="name" type="text" class="mt-3 form-control">
                    <span class="text-danger " id="error_name"></span>

                    <textarea id="description" class="mt-3 form-control" cols="30" rows="5" type="text"></textarea>
                    <span class="text-danger " id="error_description"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button value="" type="button" class="btn-update btn btn-success">Update</button>
                </div>

            </div>
        </div>
    </div>

    <!--Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Confirmation Message</h5>
                </div>
                <div class="modal-body">
                    Are you sure want to delete this Product?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <button value="" type="button" class="delete btn btn-danger">Yes</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('body-scripts')
    <script>
        jQuery(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            //Show Product List with Yajra Datatable
            var table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ Route('product.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                columnDefs: [
                    {
                        targets: '_all', // Apply to all columns
                        className: 'text-center' // Add the class to the header cells
                    }
                ]
            });

            //Product Insert
            jQuery("#btnSave").click(function() {
                var name = jQuery(".name").val();
                var description = jQuery(".description").val();

                $.ajax({
                    url: "{{ Route('product.store') }}",
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        name: name,
                        description: description
                    },
                    success: function(response) {
                        // $('.yajra-datatable').DataTable().ajax.reload();
                        table.ajax.reload();
                        jQuery(".error_name").text("");
                        jQuery(".error_description").text("");
                        jQuery(".name").val("");
                        jQuery(".description").val("");
                        // swal("Success!", response.status, "success");
                        swal({
                            title: "Success!",
                            text: response.status,
                            icon: "success",
                            timer: 2000,
                            button: false,
                        });
                    },
                    error: function(error) {
                        if (error) {
                            jQuery(".error_name").text(error.responseJSON.errors.name);
                            jQuery(".error_description").text(error.responseJSON.errors.description);
                        }
                    }
                });
            });

            //Product Edit
            jQuery(document).on("click", ".btn-edit", function (e) {
                var id = jQuery(this).val();
                jQuery(".btn-update").val(id);

                var baseUrl = `{{ url('/') }}`
                var urlEdit = baseUrl + '/product/edit/'+id;

                $.ajax({
                    url: urlEdit,
                    type: "GET",
                    dataType: "JSON",
                    success: function (response) {
                        jQuery("#name").val(response.product.name);
                        jQuery("#description").val(response.product.description);
                    }
                });
            });

            //Product Update
            jQuery(document).on("click", ".btn-update", function (e) {
                var id = jQuery(this).val();

                var name = jQuery("#name").val();
                var description = jQuery("#description").val();

                var baseUrl = `{{ url('/') }}`
                var urlUpdate = baseUrl + '/product/update/'+id;

                $.ajax({
                    url: urlUpdate,
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        name: name,
                        description: description
                    },
                    success: function(response) {
                        table.ajax.reload();
                        jQuery("#editModal").modal("hide");
                        jQuery("#error_name").text("");
                        jQuery("#error_description").text("");
                        jQuery("#name").val("");
                        jQuery("#description").val("");
                        //swal("Success!", response.status, "info");
                        swal({
                            title: "Success!",
                            text: response.status,
                            icon: "info",
                            timer: 2000,
                            button: false,
                        });
                    },
                    error: function(error) {
                        if (error) {
                            jQuery("#error_name").text(error.responseJSON.errors.name);
                            jQuery("#error_description").text(error.responseJSON.errors.description);
                        }
                    }
                });
            });

            // Listen for the modal being hidden
            jQuery("#editModal").on("hidden.bs.modal", function () {
                jQuery("#name").val("");
                jQuery("#description").val("");
                jQuery("#error_name").text("");
                jQuery("#error_description").text("");
            });


            //Product Delete
            jQuery(document).on("click", ".btn-delete", function (e) {
                var id = jQuery(this).val();
                jQuery(".delete").val(id);
            });

            jQuery(document).on("click", ".delete", function (e) {
                var id = jQuery(this).val();

                var baseUrl = `{{ url('/') }}`
                var urlDelete = baseUrl + '/product/delete/'+id;

                $.ajax({
                    url: urlDelete,
                    type: "GET",
                    dataType: "JSON",
                    success: function (response) {
                        if (response.status != "success") {
                            table.ajax.reload();
                            jQuery("#delete").modal("hide");
                            //swal ( "Oops!" ,  response.msg ,  "error" );
                            swal({
                                title: "Oops!",
                                text: response.msg,
                                icon: "error",
                                timer: 2000,
                                button: false,
                            });
                        }
                        else {
                            table.ajax.reload();
                            jQuery("#deleteModal").modal("hide");
                            //swal("Success!", response.msg, "warning");
                            swal({
                                title: "Success!",
                                text: response.msg,
                                icon: "warning",
                                timer: 2000,
                                button: false,
                            });
                        }
                    }
                });
            });

        });
    </script>
@endpush

