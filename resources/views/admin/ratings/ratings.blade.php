@extends('admin.layout.layout')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">

                        <h4 class="card-title">Đánh giá</h4>
                        @if(Session::has('success_message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Thành công: </strong> {{ Session::get('success_message') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif
                        <div class="table-responsive pt-3">
                            <table id="ratings" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>
                                            ID
                                        </th>
                                        <th>
                                            Tên sản phẩm
                                        </th>
                                        <th>
                                            Email người dùng
                                        </th>
                                        <th>
                                            Đánh giá
                                        </th>
                                        <th>
                                            Xếp hạng
                                        </th>

                                        <th>
                                            Hoạt động
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ratings as $rating)
                                    <tr>
                                        <td>
                                            {{ $rating['id'] }}
                                        </td>
                                        <td>
                                            @if(!empty($rating['product']))
                                            <a target="_blank" href="{{url('product/'.$rating['product']['id'])}}">
                                                @if(!empty($rating['user']))
                                                    {{$rating['user']['email']}}
                                                @else
                                                    <span style="color: red;">Người dùng không tồn tại</span>
                                                @endif
                                            </a>
                                        @else
                                            <span style="color: red;">Sản phẩm không tồn tại</span>
                                        @endif

                                        </td>
                                        <td>
                                            @if(!empty($rating['user']) && !empty($rating['user']['email']))
                                            {{$rating['user']['email']}}
                                        @else
                                            <span style="color: red;">Người dùng không tồn tại</span>
                                        @endif


                                        </td>
                                        <td>
                                            {{ $rating['review'] }}
                                        </td>
                                        <td>
                                            {{ $rating['rating'] }}
                                        </td>
                                        <td>
                                            @if($rating['status']==1)
                                            <a class="updateRatingStatus" id="rating-{{ $rating['id'] }}" rating_id="{{ $rating['id'] }}" href="javascript:void(0)"><i style="font-size: 25px;" class="mdi mdi-bookmark-check" status="Active"></i></a>
                                            @else
                                            <a class="updateRatingStatus" id="rating-{{ $rating['id'] }}" rating_id="{{ $rating['id'] }}" href="javascript:void(0)"><i style="font-size: 25px;" class="mdi mdi-bookmark-outline" status="Inactive"></i></a>
                                            @endif
                                            <a href="javascript:void(0)" class="confirmDelete" module="rating" moduleid="{{ $rating['id'] }}" ><i style="font-size: 25px;" class="mdi mdi-file-excel-box"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- content-wrapper ends -->
    <!-- partial:../../partials/_footer.html -->
    <footer class="footer">
        <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2025.  Premium <a href="https://www.bootstrapdash.com/" target="_blank">Bootstrap admin template</a> from BootstrapDash. All rights reserved.</span>
            <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made with <i class="ti-heart text-danger ml-1"></i></span>
        </div>
    </footer>
    <!-- partial -->
</div>
@endsection
