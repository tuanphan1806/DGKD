<?php
use App\Models\ProductsFilter;
$productFilters = ProductsFilter::productFilters();

?>
<script>
    $(document).ready(function(){
    $("#sort").on("change",function(){
        // this.form.submit();
        var sort =$("#sort").val();
        var url =$("#url").val();
        var price =get_filter('price');
        var color =get_filter('color');
        var size =get_filter('size');
        var brand =get_filter('brand');
        @foreach($productFilters as $filters)
            var {{$filters['filter_column']}} = get_filter('{{$filters['filter_column']}}');
        @endforeach
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            url:url,
            method:'Post',
            data:{
                    @foreach($productFilters as $filters)
                    {{$filters['filter_column']}}:{{$filters['filter_column']}},
                    @endforeach
                    url:url,sort:sort,size:size,color:color,price:price,brand:brand},
            success:function(data){
                $('.filter_products').html(data);
            },error:function(){
                alert("Error");
            }
        });
    });

    //getfiltersize
    $(".size").on("change",function(){
        // this.form.submit();
        var color =get_filter('color');
        var size =get_filter('size');
        var price =get_filter('price');
        var brand =get_filter('brand');
        var sort =$("#sort").val();
        var url =$("#url").val();
        @foreach($productFilters as $filters)
            var {{$filters['filter_column']}} = get_filter('{{$filters['filter_column']}}');
        @endforeach
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            url:url,
            method:'Post',
            data:{
                    @foreach($productFilters as $filters)
                    {{$filters['filter_column']}}:{{$filters['filter_column']}},
                    @endforeach
                    url:url,sort:sort,size:size,color:color,price:price,brand:brand},
            success:function(data){
                $('.filter_products').html(data);
            },error:function(){
                alert("Error");
            }
        });
    });

    //getfiltercolor
    $(".color").on("change",function(){
        // this.form.submit();
        var color =get_filter('color');
        var size =get_filter('size');
        var price =get_filter('price');
        var brand =get_filter('brand');
        var sort =$("#sort").val();
        var url =$("#url").val();
        @foreach($productFilters as $filters)
            var {{$filters['filter_column']}} = get_filter('{{$filters['filter_column']}}');
        @endforeach
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            url:url,
            method:'Post',
            data:{
                    @foreach($productFilters as $filters)
                    {{$filters['filter_column']}}:{{$filters['filter_column']}},
                    @endforeach
                    url:url,sort:sort,size:size,color:color,price:price,brand:brand},
            success:function(data){
                $('.filter_products').html(data);
            },error:function(){
                alert("Error");
            }
        });
    });

    //pricefilter
    $(".price").on("change",function(){
        // this.form.submit();
        var color =get_filter('color');
        var size =get_filter('size');
        var price =get_filter('price');
        var brand =get_filter('brand');
        var sort =$("#sort").val();
        var url =$("#url").val();
        @foreach($productFilters as $filters)
            var {{$filters['filter_column']}} = get_filter('{{$filters['filter_column']}}');
        @endforeach
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            url:url,
            method:'Post',
            data:{
                    @foreach($productFilters as $filters)
                    {{$filters['filter_column']}}:{{$filters['filter_column']}},
                    @endforeach
                    url:url,sort:sort,size:size,color:color,price:price,brand:brand},
            success:function(data){
                $('.filter_products').html(data);
            },error:function(){
                alert("Error");
            }
        });
    });


    //brandfilter
    $(".brand").on("change",function(){
        // this.form.submit();
        var color =get_filter('color');
        var size =get_filter('size');
        var price =get_filter('price');
        var brand =get_filter('brand');
        var sort =$("#sort").val();
        var url =$("#url").val();
        @foreach($productFilters as $filters)
            var {{$filters['filter_column']}} = get_filter('{{$filters['filter_column']}}');
        @endforeach
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            url:url,
            method:'Post',
            data:{
                    @foreach($productFilters as $filters)
                    {{$filters['filter_column']}}:{{$filters['filter_column']}},
                    @endforeach
                    url:url,sort:sort,size:size,color:color,price:price,brand:brand},
            success:function(data){
                $('.filter_products').html(data);
            },error:function(){
                alert("Error");
            }
        });
    });



        @foreach($productFilters as $filter)
            $('.{{ $filter['filter_column'] }}').on('click',function(){
            var url =$("#url").val();
            var color =get_filter('color');
            var size =get_filter('size');
            var price =get_filter('price');
            var brand =get_filter('brand');
            var sort =$("#sort option:selected").val();
            @foreach($productFilters as $filters)
            var {{ $filters['filter_column'] }} = get_filter('{{$filters['filter_column']}}');
            @endforeach
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                url:url,
                method:"Post",
                data:{
                    @foreach($productFilters as $filters)
                    {{$filters['filter_column']}}:{{$filters['filter_column']}},
                    @endforeach
                    url:url,sort:sort,size:size,color:color,price:price,brand:brand},
                success:function(data){
                    $('.filter_products').html(data);
                },error:function(){
                    alert("Error");
                }
            });
        });
        @endforeach
    });
</script>
