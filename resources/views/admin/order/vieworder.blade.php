@extends('admin.index') @section('content')

<div class="breadcrumbs">
    <div class="col-sm-4">
        <div class="page-header float-left">
            <div class="page-title">
                <h1>{{__('messages.view_order')}}</h1>
            </div>
        </div>
    </div>
    <div class="col-sm-8">
        <div class="page-header float-right">
            <div class="page-title">
                <ol class="breadcrumb text-right">
                    <li>{{__('messages.orders')}}</li>
                    <li class="active">{{__('messages.view_order')}}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content mt-3">

    <div class="row rowset">

        <div class="col-md-12">
            <div class="card">
                <iframe src="{{asset('public/pdf/').'/'.$pdfname}}" name="ifrm" id="ifrm" class="disno"></iframe>
                <div class="card-header">
                    <strong class="card-title">{{__('messages.view_order')}}</strong>

                    <div class="florig">
                        <form action="{{url('admin/sendordermail')}}" method="post" class="ordermail">
                            {{csrf_field()}}
                            <input type="hidden" name="filename" value="{{$pdfname}}" />
                            <input type="hidden" name="user_id" value="{{$order->user_id}}" />
                             @if(Config::get('mail.username')!="")
                                <button class="orderbtn"><i class="fa fa-envelope-o" class="ordericon" onclick="sendemail()"></i></button>
                            @else
                               <button type="button" class="orderbtn"><i class="fa fa-envelope-o" class="ordericon" onclick="setupemail()"></i></button>
                            @endif
                        </form>

                        <button onclick="printDiv()" class="orderbtn"><i class="fa fa-print" class="ordericon"></i></button>
                    </div>

                </div>
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
                    <h3 class="ordermark">{{__('messages.orders')}} & {{__('messages.account_info')}}</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="orderdiv">
                                <h4 class="orderh4">{{__('messages.order_info')}}</h4>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <label for="text-input" class=" form-control-label">{{__('messages.order_date')}}
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    {{$order->orderdate}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="text-input" class=" form-control-label">{{__('messages.order_status')}}
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    <?php if($order->order_status=='6'){
                                                  echo __('message.canceled');
                                            }else if($order->order_status=='5'){
                                                  echo __('messages.completed');
                                            }else if($order->order_status=='2'){ 
                                                  echo __("messages.on_hold");
                                            }else if($order->order_status=='3'){
                                                  echo __('messages.pending');
                                            }else if($order->order_status=='1'){
                                                echo __('messages.processing');
                                            }else if($order->order_status=='7'){
                                                echo __('messages.refunded');
                                            }
                                           ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="text-input" class=" form-control-label">{{__('messages.shipping_method')}}
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    {{$shipping->label}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="text-input" class=" form-control-label">

                                        {{__('messages.payment_method')}}
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    @if($order->payment_method==1) {{__('messages.paypal')}} @endif @if($order->payment_method==2) {{__('messages.stripe')}} @endif @if($order->payment_method==3) {{__('messages.case_on_delivery')}} @endif

                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="orderdiv">
                                <h4 class="orderh4">{{__('messages.account_info')}}</h4>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <label for="text-input" class=" form-control-label">{{__('messages.customer')}} {{__('messages.name')}}
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    {{$user->first_name}} {{$user->last_name}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="text-input" class=" form-control-label">{{__('messages.customer')}} {{__('messages.email')}}
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    {{$user->email}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="text-input" class=" form-control-label">{{__('messages.customer')}} {{__('messages.phone')}}
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    {{$order->phone}}
                                </div>
                            </div>

                        </div>
                    </div>

                    <h3 class="ordermark">{{__('messages.address_info')}}</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="orderdiv">
                                <h4 class="orderh4">{{__('messages.billing_address')}}</h4>
                            </div>
                            <div class="orderdivleft">
                                <div class="row">
                                    <label for="text-input" class=" form-control-label">{{$order->billing_first_name.' '.$order->billing_last_name}}
                                    </label>
                                </div>

                                <div class="row">
                                    <label for="text-input" class=" form-control-label halfwidth">
                                        {{$order->billing_address}}</br>
                                        {{$order->billing_city}}</br>
                                        {{$order->billing_pincode}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="orderdiv">
                                <h4 class="orderh4">{{__('messages.shipping_address')}}</h4>
                            </div>
                            <div class="orderdivleft">
                                <div class="row">
                                    <label for="text-input" class=" form-control-label">
                                        @if($order->to_ship==1) {{$order->shipping_first_name.' '.$order->shipping_last_name}} @else {{$order->billing_first_name.' '.$order->billing_last_name}} @endif

                                    </label>
                                </div>

                                <div class="row">
                                    <label for="text-input" class=" form-control-label halfwidth">
                                        @if($order->to_ship==1) {{$order->shipping_address}}
                                        </br>
                                        {{$order->shipping_city}}</br>
                                        {{$order->shipping_pincode}} @else {{$order->billing_address}}
                                        </br>
                                        {{$order->billing_city}}</br>
                                        {{$order->billing_pincode}} @endif

                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h3 class="ordermark">{{__('messages.item_ordered')}}</h3>
                    <table class="table topnone">
                        <thead>
                            <tr>
                                <th>{{__('messages.product')}}</th>
                                <th>{{__('messages.unit_price')}}</th>
                                <th>{{__('messages.qty')}}</th>
                                <th>{{__('messages.line_total')}}</th>
                            </tr>
                        </thead>
                        <tbody class="borderbot">
                            @foreach($orderdata as $od)
                            <tr>
                                <td>{{$od->productdata->name}}
                                </br>
                                <?php
                                      if($od->option_name!=""&&$od->option_name!="null"){
                                          $opna=explode(",",$od->option_name);
                                          $label=explode(",",$od->label);
                                          for($i=0;$i<count($opna);$i++){
                                              echo "<span style='font-size: small;'>".$opna[$i]."=>".$label[$i]."</span></br>";
                                          }  
                                      }
                                ?>
                                    
                                    </td>
                                <td>{{$currency.number_format((float)$od->price, 2, '.', '')}}
                                     <br>
                                     <?php
                                          if($od->option_name!=""&&$od->option_name!="null"){
                                              $price=explode(",",$od->option_price);
                                              $label=explode(",",$od->label);
                                              for($i=0;$i<count($opna);$i++){
                                                  $t=0;
                                                  if(isset($price[$i])&&$price[$i]!=""&&$price[$i]!="null"){
                                                      $t=trim($price[$i]);
                                                  }else{
                                                      $t=0.00;
                                                  }
                                                  if(empty($t)){
                                                      echo $currency."0.00</br>";
                                                  }else{
                                                      echo $currency.$t."</br>";
                                                  }
                                                  
                                              }
                                          }
                                     ?>
                                </td>
                                <td>{{$od->quantity}}</td>
                                <td>{{$currency.number_format((float)$od->total_amount, 2, '.', '')}}</td>
                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                    <div class="row">
                        <div class="col-md-9"></div>
                        <div class="col-md-3">
                            <table class="table topnone">

                                <tbody class="borderbot">
                                    <tr>

                                        <th>{{__('messages.subtotal')}}</th>
                                        <td>{{$currency.number_format((float)$order->subtotal, 2, '.', '')}}</td>
                                    </tr>

                                    <tr>
                                        <th>{{__('messages.shipping')}}</th>
                                        <td>{{$currency.number_format((float)$order->shipping_charge, 2, '.', '')}}</td>

                                    </tr>
                                     @if($order->is_freeshipping=='1')
                                        <tr>
                                             <th></th>
                                             <td>{{__('messages.free_delivery')}}</td>
                                        </tr>                 
                                     @endif
                                    @if($order->coupon_code!="")
                                    <tr>
                                        <th>{{__('messages.coupon')}}({{$order->coupon_code}})</th>
                                        <td>-{{$currency.number_format((float)$order->coupon_price, 2, '.', '')}}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th>{{__('messages.taxes')}}</th>
                                        <td>{{$currency.number_format((float)$order->taxes_charge, 2, '.', '')}}</td>
                                    </tr>
                                    <tr>
                                        <th>{{__('messages.total')}}</th>
                                        <th>{{$currency.number_format((float)$order->total, 2, '.', '')}}</th>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop