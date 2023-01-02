@extends('admin.index') @section('content')

<div class="breadcrumbs">
    <div class="col-sm-4">
        <div class="page-header float-left">
            <div class="page-title">
                <h1>
                     @if($data->offer_type=='1')
                         {{__('messages.edit')}} {{__('messages.big_offer')}}
                    @endif
                    @if($data->offer_type=='2')
                         {{__('messages.edit')}} {{__('messages.normal_offer')}}
                    @endif
               </h1>
            </div>
        </div>
    </div>
    <div class="col-sm-8">
        <div class="page-header float-right">
            <div class="page-title">
                <ol class="breadcrumb text-right">
                    <li class="active">
                        @if($data->offer_type=='1') {{__('messages.edit')}} {{__('messages.big_offer')}} @endif @if($data->offer_type=='2') {{__('messages.edit')}} {{__('messages.normal_offer')}} @endif
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content mt-3">
    <div class="row rowset">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <strong class="card-title">  @if($data->offer_type=='1')
                        {{__('messages.edit')}} {{__('messages.big_offer')}}
                    @endif
                    @if($data->offer_type=='2')
                         {{__('messages.edit')}} {{__('messages.normal_offer')}}
                    @endif</strong>
                </div>
                <div class="card-body">
                    <div id="pay-invoice">
                        <div class="card-body">
                            @if(Session::has('message'))
                            <div class="col-sm-12">
                                <div class="alert  {{ Session::get('alert-class', 'alert-info') }} alert-dismissible fade show" role="alert">{{ Session::get('message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                            @endif
                            <form action="{{url('admin/updateofferdata')}}" method="post" enctype="multipart/form-data">
                                {{csrf_field()}}
                                <input type="hidden" name="id" id="id" value="{{$data->id}}" />
                                <input type="hidden" name="real_image" id="real_image" value="{{$data->banner}}" />
                                <input type="hidden" name="offer_type" id="offer_type" value="{{$data->offer_type}}" />

                                <input type="hidden" name="offer_on" id="offer_on" value="{{$data->is_product}}" />
                                <div class="row form-group">

                                    <div class="col col-md-6">
                                        <label class=" form-control-label">{{__('messages.offer_on')}}:-</label>
                                        <div class="form-check-inline form-check">
                                            @if($data->is_product=='1') {{__('messages.cate_gory')}} @endif @if($data->is_product=='2') {{__('messages.product')}} @endif @if($data->is_product=='3') {{__('messages.coupon')}} @endif

                                        </div>
                                    </div>
                                </div>
                                @if($data->is_product=='1')
                                <div id="categorydiv">
                                    <div class="form-group col-md-12 paddiv">
                                        <div class="col-md-6 pl0">
                                            <label for="name" class=" form-control-label">
                                                {{__('messages.cate_gory')}}
                                                <span class="reqfield">*</span>
                                            </label>
                                            <select name="category_id" id="category_id" class="form-control" required>
                                                <option value="">
                                                    {{__('messages.select_category')}}
                                                </option>
                                                @foreach($category as $ca)
                                                <option value="{{$ca->id}}" <?=$data->category_id ==$ca->id ? ' selected="selected"' : '';?>>{{$ca->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 pl0">
                                            <div class="form-group">
                                                <label for="name" class=" form-control-label">
                                                    {{__('messages.fixed_up_to')}}(%)
                                                    <span class="reqfield">*</span>
                                                </label>
                                                <input type="text" id="fixed" placeholder="50" class="form-control" name="fixed" value="{{$data->fixed}}" required onchange="checkfixed(this.value)">
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                @endif @if($data->is_product=='2')
                                <div id="productdiv">
                                    <div class="form-group col-md-12 paddiv">
                                        <div class="col-md-9 pl0">
                                            <label for="name" class=" form-control-label">
                                                {{__('messages.product')}}
                                                <span class="reqfield">*</span>
                                            </label>
                                            <select name="product_id" id="product_id" class="form-control" onchange="getproductprice(this.value)">
                                                <option value="">{{__('messages.select')}} {{__('messages.product')}}</option>
                                                @foreach($product as $pro)
                                                <option value="{{$pro->id}}" <?=$data->product_id ==$pro->id ? ' selected="selected"' : '';?>>{{$pro->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12 paddiv">
                                        <div class="col-md-4 pl0">
                                            <label for="name" class=" form-control-label">
                                                {{__('messages.MRP')}}
                                            </label>
                                            <input type="text" id="mrp" class="form-control" name="mrp" readonly value="{{$mrp}}">
                                        </div>
                                        <div class="col-md-4 pl0">
                                            <label for="name" class=" form-control-label">
                                                {{__('messages.selling_price')}}
                                            </label>
                                            <input type="text" id="selling_price" class="form-control" name="selling_price" readonly value="{{$price}}">
                                        </div>
                                        <div class="col-md-4 pl0">
                                            <label for="name" class=" form-control-label">
                                                {{__('messages.offer_price')}}<span class="reqfield">*</span>
                                            </label>
                                            <input type="text" id="offer_price" class="form-control" name="offer_price" onchange="checkofferprice(this.value)" value="{{$data->new_price}}">
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if($data->is_product=='3')
                                 <div id="coupondiv">
                                     <div class="form-group col-md-12 paddiv">
                                        <div class="col-md-9 pl0">
                                            <label for="name" class=" form-control-label">
                                                {{__('messages.coupon')}}
                                                <span class="reqfield">*</span>
                                            </label>
                                            <select name="coupon_id" id="coupon_id" class="form-control" onchange="getcoupondata(this.value)">
                                                <option value="">{{__('messages.select')}}</option>
                                                @foreach($coupon as $pro)
                                                <option value="{{$pro->id}}" <?=$data->coupon_id ==$pro->id ? ' selected="selected"' : '';?>>{{$pro->name}}({{$pro->code}})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                     <div class="col-md-4 pl0">
                                            <label for="name" class=" form-control-label">
                                                {{__('messages.value')}}
                                            </label>
                                            <?php if(isset($coupondata)&&$coupondata->discount_type=='1'){
                                                        $coupondata->value=$coupondata->value."%";
                                                  }

                                            ?>
       
                                            <input type="text" readonly id="coupon_discount_value" class="form-control" name="coupon_discount_value" value="<?=isset($coupondata->name)?$coupondata->name:''; ?>">
                                    </div>
                                </div>
                                @endif
                                <div class="form-group col-md-12 paddiv">
                                    <div class="col-md-6 pl0">
                                        <label for="name" class=" form-control-label">
                                            {{__('messages.offers')}}{{__('messages.start_date')}}
                                            <span class="reqfield">*</span>
                                        </label>
                                        <input type="text" id="start_date" class="form-control" name="start_date" required value="{{$data->start_date}}">
                                    </div>
                                    <div class="col-md-6 pr0">
                                        <div class="form-group">
                                            <label for="name" class=" form-control-label">
                                                {{__('messages.offers')}}{{__('messages.end_date')}}
                                                <span class="reqfield">*</span>
                                            </label>
                                            <input type="text" id="end_date" class="form-control" name="end_date" required value="{{$data->end_date}}">
                                        </div>

                                    </div>
                                </div>

                                <div class="form-group col-md-12 paddiv">
                                    <label for="name" class=" form-control-label">
                                        {{__('messages.title')}}
                                        <span class="reqfield">*</span>
                                    </label>
                                    <input type="text" id="title" placeholder="{{__('messages.title')}}" class="form-control" name="title" required value="{{$data->title}}">
                                </div>

                                <div class="form-group col-md-12 paddiv">
                                    <label for="name" class=" form-control-label">
                                        {{__('messages.main_title')}}
                                        <span class="reqfield">*</span>
                                    </label>
                                    <input type="text" id="main_title" placeholder="Enter Main Title" class="form-control" name="main_title" required value="{{$data->main_title}}">
                                </div>

                                <div class="form-group col-md-12 paddiv">
                                    <label for="name" class=" form-control-label">
                                        {{__('messages.banner')}} @if($data->offer_type=='1') (1110X424) @endif @if($data->offer_type=='2') (445X170) @endif
                                        <span class="reqfield">*</span>
                                    </label>
                                    <img src="{{asset('public/upload/offer/image').'/'.$data->banner}}" class="imgsize" />
                                    <input type="file" id="banner" accept="image/*" class="form-control" name="banner">
                                </div>
                                  @if($data->offer_type==1)
                                         <div class="form-group col-md-12 paddiv">
                                             <label for="name" class=" form-control-label">
                                                 {{__('messages.mobile_banner')}} 
                                           <span class="reqfield">*</span>
                                    </label>
                                    <img src="{{asset('public/upload/offer/image').'/'.$data->mobile_banner}}" class="imgsize" />
                                    <input type="file" id="mobile_banner" class="form-control" accept="image/*" name="mobile_banner" >
                                    <input type="hidden" name="real_mobile_image" id="real_mobile_image" value="{{$data->mobile_banner}}">
                                </div> 
                                @endif
                                <div>
                                     @if(Session::get("is_demo")=='1')
                                 <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary florig">
                                    {{__('messages.submit')}}
                                </button>
                                @else
                                     <button class="btn btn-primary florig" type="submit"> {{__('messages.submit')}}</button>
                                @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop