
@extends('master')
@section('title','Manage Backups')
@section('content')
    <!-- BEGIN: Subheader -->
    <div class="m-subheader ">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <ul class="m-subheader__breadcrumbs m-nav m-nav--inline">
                    <li class="m-nav__item m-nav__item--home">
                        <a href="#" class="m-nav__link m-nav__link--icon">
                            <i class="m-nav__link-icon la la-home"></i>
                        </a>
                    </li>
                    <li class="m-nav__separator">-</li>
                    <li class="m-nav__item">
                        <a href="" class="m-nav__link">
                            <span class="m-nav__link-text">Advanced</span>
                        </a>
                    </li>
                    <li class="m-nav__separator">-</li>
                    <li class="m-nav__item">
                        <a href="" class="m-nav__link">
                            <span class="m-nav__link-text">Backups</span>
                        </a>
                    </li>
                    <li class="m-nav__separator">-</li>
                    <li class="m-nav__item">
                        <a href="" class="m-nav__link">
                            <span class="m-nav__link-text">@yield('title')</span>
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </div>

    <div class="br-for-mobile-break m--visible-mobile-inline-block">
        <br>
    </div>

    <!-- END: Subheader -->
    <div class="m-content">
        <div class="m-portlet m-portlet--mobile">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <h3 class="m-portlet__head-text">
                            @yield('title')
                        </h3>
                    </div>
                </div>
                <div class="m-portlet__head-tools">
                    <a href="{{ URL::to('backup-create') }}" onclick="return confirm('Are you sure? It will take some time...')" class="btn btn-sm btn-brand m-btn--icon m-btn--wide m-btn--sm m--margin-right-10 m-btn--air">
                        <span>
                            <i class="fa fa-plus"></i>
                            <span>Create a new backup</span>
                        </span>
                    </a>                     
                </div>
            </div>
            <div class="m-portlet__body">
                @if (Session::has('msg_backup_create'))
                    <div class="alert alert-info" role="alert">
                      <strong>{{Session::get('msg_backup_create')}}</strong>
                    </div>                
                @endif
                <!--begin: Datatable -->
                <table class="table table-striped- table-bordered table-hover " id="m_table_1">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Location</th>
                        <th>Date</th>
                        <th class="text-right">File size</th>
                        <th class="text-right">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($backups as $k => $b)
                      <tr class="{{$k}}">
                        <th scope="row">{{ $k+1 }}</th>
                        <td>{{ $b['disk'] }}</td>
                        <td>
                            {{ \Carbon\Carbon::createFromTimeStamp($b['last_modified'])->formatLocalized('%d %B %Y, %H:%M') }}
                        </td>
                        <td class="text-right">{{ round((int)$b['file_size']/1048576, 2).' MB' }}</td>
                        <td class="text-right">
                            @if ($b['download'])
                            <a class="btn btn-sm btn-link" href="{{ url('/backup/download/') }}?disk={{ $b['disk'] }}&path={{ urlencode($b['file_path']) }}&file_name={{ urlencode($b['file_name']) }}"><i class="la la-cloud-download"></i> Download</a>
                            @endif
                            <a class="deleteBtn btn btn-sm btn-danger" data-token="{!! csrf_token() !!}" data-id="{!! $k !!}" href="{{ url('/backup/delete?file_name='.$b['file_name']) }}&disk={{ $b['disk'] }}"><i class="la la-trash-o"></i> Delete</a>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- END EXAMPLE TABLE PORTLET-->
    </div>
@stop

@push('custom_scripts')
<script>
  jQuery(document).ready(function($) {

    // capture the delete button
    $(document).on('click', '.deleteBtn', function (e) {
        e.preventDefault();
        var actionTo=$(this).attr('href');
        var token=$(this).attr('data-token');
        var id=$(this).attr('data-id');

        swal({
                title: "Are you sure?",
                text: "You will not be able to recover this information!",
                type: "success",
                showCancelButton: true,
                confirmButtonClass: 'btn-danger',
                confirmButtonColor: "#DD6B55",
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: "Cancel",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url:actionTo,
                        type: 'post',
                        data: {_method: 'delete',_token:token},
                        success: function (data) {

                            if (data == 'hasForeignKey') {
                                swal({
                                    title: "Oops!",
                                    text: "This data is used anywhere",
                                    type: "error"
                                });
                            } else if(data == 'success'){
                                swal({
                                        title: "Deleted!",
                                        text: "Your information delete successfully.",
                                        type: "success"
                                    },
                                    function (isConfirm) {
                                        if (isConfirm) {
                                            $('.' + id).fadeOut();
                                        }
                                    });
                            }else{
                                swal({
                                    title: "Fail to Delete!",
                                    text: "Something Error Found !, Please try again.",
                                    type: "error"
                                });
                            }
                        }

                    });
                } else {
                    swal("Cancelled", "Your data is safe .", "error");
                }
            });
        return false;
    });

  });
</script>
@endpush


