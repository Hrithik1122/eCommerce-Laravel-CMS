@extends('admin.index') @section('content')

<div class="breadcrumbs">
    <div class="col-sm-4">
        <div class="page-header float-left">
            <div class="page-title">
                <h1>
                    @if($offer_type==1)
                         {{__('messages.add')}} {{__('messages.big_offer')}}
                    @endif
                    @if($offer_type==2)
                        {{__('messages.add')}} {{__('messages.normal_offer')}}                         
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
                        @if($offer_type==1) {{__('messages.add')}} {{__('messages.big_offer')}} @endif @if($offer_type==2) {{__('messages.add')}} {{__('messages.normal_offer')}} @endif
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
                    <strong class="card-title">@if($offer_type==1)
                         {{__('messages.add')}} {{__('messages.big_offer')}} 
                    @endif
                    @if($offer_type==2)
                        {{__('messages.add')}} {{__('messages.normal_offer')}} 
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

                            <form action="{{url('admin/storeofferdata')}}" method="post" enctype="multipart/form-data">
                                {{csrf_field()}}
                                <input type="hidden" name="offer_type" id="offer_type" value="{{$offer_type}}" />
                                <div class="row form-group">

                                    <div class="col col-md-8">
                                        <label class=" form-control-label">{{__('messages.offer_on')}}:-</label>
                                        <div class="form-check-inline form-check">

                                            <label for="offer_on1" class="form-check-label offernameshow">
                                                <input type="radio" id="offer_on1" name="offer_on" value="1" class="form-check-input" checked onchange="changeofferdiv(this.value)" checked=""> @if($offer_type==1) {{__('messages.cate_gory')}} @else Subcategory @endif
                                            </label>
                                            <label for="offer_on2" class="form-check-label offernameshow">
                                                <input type="radio" id="offer_on2" name="offer_on" value="2" class="form-check-input" onchange="changeofferdiv(this.value)">{{__('messages.product')}}
                                            </label>
                                             @if($offer_type==2) 
                                              <label for="offer_on2" class="form-check-label offernameshow">
                                                <input type="radio" id="offer_on2" name="offer_on" value="3" class="form-check-input" onchange="changeofferdiv(this.value)">{{__('messages.coupon')}}
                                            </label>
                                             @endif
                                        </div>
                                    </div>
                                </div>

                                <div id="categorydiv">
                                    <div class="form-group col-md-12 paddiv">
                                        <div class="col-md-6 pl0">
                                            <label for="name" class=" form-control-label">
                                                @if($offer_type==1) {{__('messages.cate_gory')}} @else {{__('messages.subcategory')}} @endif
                                                <span class="reqfield">*</span>
                                            </label>
                                            <select name="category_id" id="category_id" class="form-control" required>
                                                <option value=""> {{__('messages.select')}}</option>
                                                @foreach($category as $ca)
                                                <option value="{{$ca->id}}" >{{$ca->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 pr0">
                                            <div class="form-group">
                                                <label for="name" class=" form-control-label">
                                                    {{__('messages.fixed_up_to')}}(%)
                                                    <span class="reqfield">*</span>
                                                </label>
                                                <input type="text" id="fixed" placeholder="50" class="form-control" name="fixed" onchange="checkfixed(this.value)" required="">
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div id="productdiv" class="disno">
                                    <div class="form-group col-md-12 paddiv">
                                        <div class="col-md-9 pl0">
                                            <label for="name" class=" form-control-label">
                                                {{__('messages.product')}}
                                                <span class="reqfield">*</span>
                                            </label>
                                            <select name="product_id" id="product_id" class="form-control" onchange="getproductprice(this.value)">
                                                <option value="">{{__('messages.select_product')}}</option>
                                                @foreach($product as $pro)
                                                <option value="{{$pro->id}}">{{$pro->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12 paddiv">
                                        <div class="col-md-4 pl0">
                                            <label for="name" class=" form-control-label">
                                                {{__('messages.MRP')}}
                                            </label>
                                            <input type="text" id="mrp" class="form-control" name="mrp" readonly>
                                        </div>
                                        <div class="col-md-4 pl0">
                                            <label for="name" class=" form-control-label">
                                                {{__('messages.selling_price')}}
                                            </label>
                                            <input type="text" id="selling_price" class="form-control" name="selling_price" readonly>
                                        </div>
                                        <div class="col-md-4 pl0">
                                            <label for="name" class=" form-control-label">
                                                {{__('messages.offer_price')}}<span class="reqfield">*</span>
                                            </label>
                                            <input type="text" id="offer_price" class="form-control" name="offer_price" onchange="checkofferprice(this.value)">
                                        </div>
                                    </div>
                                </div>
                                <div id="coupondiv" class="disno">
                                     <div class="form-group col-md-12 paddiv">
                                        <div class="col-md-9 pl0">
                                            <label for="name" class=" form-control-label">
                                                {{__('messages.coupon')}}
                                                <span class="reqfield">*</span>
                                            </label>
                                            <select name="coupon_id" id="coupon_id" class="form-control" onchange="getcoupondata(this.value)">
                                                <option value="">{{__('messages.select')}}</option>
                                                @foreach($coupon as $pro)
                                                <option value="{{$pro->id}}">{{$pro->name}}({{$pro->code}})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                     <div class="col-md-4 pl0">
                                            <label for="name" class=" form-control-label">
                                                {{__('messages.value')}}
                                            </label>
                                            <input type="text" readonly id="coupon_discount_value" class="form-control" name="coupon_discount_value">
                                    </div>
                                </div>
                                <div class="form-group col-md-12 paddiv">
                                    <div class="col-md-6 pl0">
                                        <label for="name" class=" form-control-label">
                                            {{__('messages.offers')}}{{__('messages.start_date')}}
                                            <span class="reqfield">*</span>
                                        </label>
                                        <input type="text" id="start_date" class="form-control" name="start_date" required>
                                    </div>
                                    <div class="col-md-6 pr0">
                                        <div class="form-group">
                                            <label for="name" class=" form-control-label">
                                                {{__('messages.offers')}}{{__('messages.end_date')}}
                                                <span class="reqfield">*</span>
                                            </label>
                                            <input type="text" id="end_date" class="form-control" name="end_date" required>
                                        </div>

                                    </div>
                                </div>

                                <div class="form-group col-md-12 paddiv">
                                    <label for="name" class=" form-control-label">
                                        {{__('messages.title')}}
                                        <span class="reqfield">*</span>
                                    </label>
                                    <input type="text" id="title" placeholder="Enter Title" class="form-control" name="title" required>
                                </div>
                                <div class="form-group col-md-12 paddiv">
                                    <label for="name" class=" form-control-label">
                                        {{__('messages.main_title')}}
                                        <span class="reqfield">*</span>
                                    </label>
                                    <input type="text" id="main_title" placeholder="Enter Main Title" class="form-control" name="main_title" required>
                                </div>

                                <div class="form-group col-md-12 paddiv">
                                    <label for="name" class=" form-control-label">
                                        {{__('messages.banner')}} @if($offer_type==1) (635X370) @endif @if($offer_type==2) (445X170) @endif

                                        <span class="reqfield">*</span>
                                    </label>
                                    <input type="file" id="banner" accept="image/*" class="form-control" name="banner" required>
                                </div>
                                
                                @if($offer_type==1)
                                         <div class="form-group col-md-12 paddiv">
                                             <label for="name" class=" form-control-label">
                                                 {{__('messages.mobile_banner')}} 
                                           <span class="reqfield">*</span>
                                    </label>
                                    @if($offer_type==1)
                                       <input type="file" id="mobile_banner" accept="image/*" class="form-control" name="mobile_banner" required>
                                    @else
                                       <input type="file" id="mobile_banner" accept="image/*" class="form-control" name="mobile_banner" >
                                    @endif
                                    
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