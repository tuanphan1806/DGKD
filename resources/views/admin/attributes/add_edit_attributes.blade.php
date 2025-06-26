@extends('admin.layout.layout')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">Quản lý thuộc tính</h3>
                        <h6 class="font-weight-normal mb-0">Cập nhật thuộc tính</h6>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="justify-content-end d-flex">
                            <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
                                <button class="btn btn-sm btn-light bg-white dropdown-toggle" type="button" id="dropdownMenuDate2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <i class="mdi mdi-calendar"></i> Today (27 Feb 2025)
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuDate2">
                                    <a class="dropdown-item" href="#">January - March</a>
                                    <a class="dropdown-item" href="#">March - June</a>
                                    <a class="dropdown-item" href="#">June - August</a>
                                    <a class="dropdown-item" href="#">August - November</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Thêm thuộc tính</h4>
                    @if(Session::has('error_message'))
                      <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Lỗi: </strong> {{ Session::get('error_message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                    @endif

                    @if(Session::has('success_message'))
                      <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Thành công: </strong> {{ Session::get('success_message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                    @endif
                    @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">

                @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                            @endforeach
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif
                  <form class="forms-sample"  action="{{ url('admin/add-edit-attributes/'.$product['id']) }}"  method="post"  >@csrf

                    <div class="form-group">
                      <label for="product_name">Tên sản phẩm</label>
                      &nbsp; {{ $product['product_name'] }}
                    </div>

                    <div class="form-group">
                      <label for="product_code">Mã sản phẩm</label>
                      &nbsp; {{ $product['product_code'] }}
                    </div>

                    <div class="form-group">
                      <label for="product_color">Màu sản phẩm</label>
                      &nbsp; {{ $product['product_color'] }}
                    </div>

                    <div class="form-group">
                      <label for="product_price">Giá sản phẩm</label>
                      &nbsp; {{ $product['product_price'] }}
                    </div>

                    <div class="form-group">
                     @if(!empty($product['product_image']))
                     <img style="width: 100px;" src="{{ url('front/images/product_images/large/' . $product['product_image']) }}">
                     @else
                     <img style="width: 100px;" src="{{ url('front/images/product_images/small/no_image.png') }}">
                     @endif
                    </div>

                    <div class="form-group">
                    <div class="field_wrapper">
                        <div>
                            <input type="text" name="size[]" placeholder="Size" style="width: 100px;" required="" />
                            <input type="text" name="sku[]" placeholder="SKU" style="width: 100px;" required="" />
                            <input type="text" name="price[]" placeholder="Giá" style="width: 100px;" required="" />
                            <input type="text" name="stock[]" placeholder="Stock" style="width: 100px;" required="" />
                            <a href="javascript:void(0);" class="add_button" title="Thêm thuộc tính">Thêm</a>
                        </div>
                    </div>
                    </div>

                    <button type="submit" class="btn btn-primary mr-2">Xác nhận</button>
                    <button type="reset" class="btn btn-light">Hủy</button>
                  </form>
                  <br><br><h4 class="card-title">Thuộc tính sản phẩm</h4>
                  <form method="post" action="{{url('admin/edit-attributes/'.$product['id']) }}">@csrf
                  <table id="products" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>
                                            ID
                                        </th>

                                        <th>
                                            Size
                                        </th>

                                        <th>
                                            SKU
                                        </th>

                                        <th>
                                            Giá
                                        </th>

                                        <th>
                                            Số lượng
                                        </th>

                                        <th>
                                            Hoạt động
                                        </th>


                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product['attributes'] as $attribute)
                                    <input style="display: none;" type="text" name="attributeId[]" value="{{ $attribute['id'] }}" >
                                    <tr>
                                        <td>
                                            {{ $attribute['id'] }}
                                        </td>

                                        <td>
                                            {{ $attribute['size'] }}
                                        </td>

                                        <td>
                                            {{ $attribute['sku'] }}
                                        </td>

                                        <td>
                                            <input type="number" name="price[]" value="{{ $attribute['price'] }}" required="" style="width: 70px;">
                                        </td>

                                        <td>

                                            <input type="number" name="stock[]" value="{{ $attribute['stock'] }}" required="" style="width: 70px;">
                                        </td>
                                        <td>
                                            @if($attribute['status']==1)
                                            <a class="updateAttributeStatus" id="attribute-{{ $attribute['id'] }}" attribute_id="{{ $attribute['id'] }}" href="javascript:void(0)"><i style="font-size: 25px;" class="mdi mdi-bookmark-check" status="Active"></i></a>
                                            @else
                                            <a class="updateAttributeStatus" id="attribute-{{ $attribute['id'] }}" attribute_id="{{ $attribute['id'] }}" href="javascript:void(0)"><i style="font-size: 25px;" class="mdi mdi-bookmark-outline" status="Inactive"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                  </form>
                </div>
              </div>
            </div>



          </div>
    </div>
    <!-- content-wrapper ends -->
    <!-- partial:partials/_footer.html -->
    @include('admin.layout.footer')
    <!-- partial -->
</div>

@endsection
