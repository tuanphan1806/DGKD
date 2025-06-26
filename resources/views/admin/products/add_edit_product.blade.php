@extends('admin.layout.layout')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">Quản lý sản phẩm</h3>
                        <h6 class="font-weight-normal mb-0">Cập nhật sản phẩm</h6>
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
                  <h4 class="card-title">{{ $title }}</h4>
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
                  <form class="forms-sample" @if(empty($product['id'])) action="{{ url('admin/add-edit-product') }}" @else action="{{ url('admin/add-edit-product/'.$product['id']) }}" @endif method="post" enctype="multipart/form-data" >@csrf

                  <div class="form-group">
                      <label for="category_id">Chọn phân loại</label>
                      <select name="category_id" id="category_id" class="form-control" style="color:darkcyan">
                        <option value="">Chọn</option>
                        @foreach($categories as $section)
                        <optgroup label="{{ $section['name'] }}"></optgroup>
                        @foreach($section['categories'] as $category)
                        <option @if(!empty($product['category_id']==$category['id'])) selected=""@endif value="{{$category['id']}}">&nbsp;&nbsp;&nbsp;-->&nbsp;{{$category['category_name']}}</option>
                        @foreach($category['subcategories'] as $subcategory)
                        <option @if(!empty($product['category_id']==$subcategory['id'])) selected=""@endif value="{{$subcategory['id']}}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;{{$subcategory['category_name']}}</option>
                        @endforeach
                        @endforeach
                        @endforeach
                      </select>
                    </div>
                    <div class="loadFilters">
                      @include('admin.filters.category_filters')
                    </div>
                    <div class="form-group">
                      <label for="brand_id">Chọn thương hiệu</label>
                      <select name="brand_id" id="brand_id" class="form-control" style="color:darkcyan">
                        <option value="">Chọn</option>
                        @foreach($brands as $brand)
                        <option @if(!empty($product['brand_id']==$brand['id'])) selected=""@endif  value="{{ $brand['id'] }}">{{ $brand['name'] }}</option>
                        @endforeach
                      </select>
                    </div>


                    <div class="form-group">
                      <label for="product_name">Tên sản phẩm</label>
                      <input type="text" class="form-control" id="product_name" placeholder="Nhập tên sản phẩm" name="product_name" @if(!empty($product['product_name'])) value="{{ $product['product_name'] }}" @else value="{{ old('product_name') }}" @endif>
                    </div>

                    <div class="form-group">
                      <label for="product_code">Mã sản phẩm</label>
                      <input type="text" class="form-control" id="product_code" placeholder="Nhập mã sản phẩm" name="product_code" @if(!empty($product['product_code'])) value="{{ $product['product_code'] }}" @else value="{{ old('product_code') }}" @endif>
                    </div>

                    <div class="form-group">
                      <label for="product_color">Màu sản phẩm</label>
                      <input type="text" class="form-control" id="product_color" placeholder="Nhập màu sản phẩm" name="product_color" @if(!empty($product['product_color'])) value="{{ $product['product_color'] }}" @else value="{{ old('product_color') }}" @endif>
                    </div>

                    <div class="form-group">
                      <label for="product_price">Giá sản phẩm</label>
                      <input type="text" class="form-control" id="product_price" placeholder="Nhập giá sản phẩm" name="product_price" @if(!empty($product['product_price'])) value="{{ $product['product_price'] }}" @else value="{{ old('product_price') }}" @endif>
                    </div>

                    <div class="form-group">
                      <label for="product_discount">Giảm giá (%)</label>
                      <input type="text" class="form-control" id="product_discount" placeholder="Nhập mã giảm giá" name="product_discount" @if(!empty($product['product_discount'])) value="{{ $product['product_discount'] }}" @else value="{{ old('product_discount') }}" @endif>
                    </div>

                    <div class="form-group">
                      <label for="product_weight">Trọng lượng</label>
                      <input type="text" class="form-control" id="product_weight" placeholder="Nhập trọng lượng" name="product_weight" @if(!empty($product['product_weight'])) value="{{ $product['product_weight'] }}" @else value="{{ old('product_weight') }}" @endif>
                    </div>

                    <div class="form-group">
                      <label for="group_code">Nhóm màu</label>
                      <input type="text" class="form-control" id="group_code" placeholder="Nhập mã nhóm" name="group_code" @if(!empty($product['group_code'])) value="{{ $product['group_code'] }}" @else value="{{ old('group_code') }}" @endif>
                    </div>


                    <div class="form-group">
                      <label for="product_image">Hình sản phẩm (1000x1000)</label>
                      <input type="file" class="form-control" id="product_image"  name="product_image"   >
                     @if(!empty($product['product_image']))
                     <a target="_blank" href="{{ url('front/images/product_images/large/' . $product['product_image']) }}">Xem hình</a>&nbsp;|&nbsp;
                     <a href="javascript:void(0)" class="confirmDelete" module="product-image" moduleid="{{ $product['id'] }}" >Xóa hình</a>
                     @endif
                    </div>

                    <div class="form-group">
                      <label for="product_video">Video sản phẩm (Không quá 2 MB)</label>
                      <input type="file" class="form-control" id="product_video"  name="product_video"   >
                     @if(!empty($product['product_video']))
                     <a target="_blank" href="{{ url('front/videos/product_videos/' . $product['product_video']) }}">Xem video</a>&nbsp;|&nbsp;
                     <a href="javascript:void(0)" class="confirmDelete" module="product-video" moduleid="{{ $product['id'] }}" >Xóa video</a>
                     @endif
                    </div>

                    <div class="form-group">
                      <label for="description">Mô tả sản phẩm</label>
                      <textarea name="description" id="description" class="form-control"  rows="3">{{ $product['description'] }}</textarea>
                    </div>

                    <div class="form-group">
                      <label for="meta_title">Tiêu đề</label>
                      <input type="text" class="form-control" id="meta_title"  name="meta_title" @if(!empty($product['meta_title'])) value="{{ $product['meta_title'] }}" @else value="{{ old('meta_title') }}" @endif>
                    </div>
                    <div class="form-group">
                      <label for="meta_description">Mô tả</label>
                      <input type="text" class="form-control" id="meta_description"  name="meta_description" @if(!empty($product['meta_description'])) value="{{ $product['meta_description'] }}" @else value="{{ old('meta_description') }}" @endif>
                    </div>
                    <div class="form-group">
                      <label for="meta_keywords">Từ khóa</label>
                      <input type="text" class="form-control" id="meta_keywords"  name="meta_keywords" @if(!empty($product['meta_keywords'])) value="{{ $product['meta_keywords'] }}" @else value="{{ old('meta_keywords') }}" @endif>
                    </div>

                    <div class="form-group">
                      <label for="is_featured">Mặt hàng nổi bật</label>
                      <input type="checkbox" name="is_featured" id="is_featured" value="Yes" @if(!empty($product['is_featured']) && $product['is_featured']=="Yes") checked="" @endif>
                    </div>
                    <div class="form-group">
                      <label for="is_bestseller">Bán chạy nhất</label>
                      <input type="checkbox" name="is_bestseller" id="is_bestseller" value="Yes" @if(!empty($product['is_bestseller']) && $product['is_bestseller']=="Yes") checked="" @endif>
                    </div>
                    <button type="submit" class="btn btn-primary mr-2">Xác nhận</button>
                    <button type="reset" class="btn btn-light">Hủy</button>
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
