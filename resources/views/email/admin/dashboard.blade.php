@extends('admin.index')
@section('content')
<div class="breadcrumbs">
      <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
         <div class="page-title">
            <h1>{{__('messages.dashboard')}}</h1>
         </div>
      </div>
   </div>
    <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li class="active">{{__('messages.dashboard')}}</li>
            </ol>
         </div>
      </div>
   </div>
</div>
<div class="content mt-3 sale">
   <div class="col-lg-3 col-sm-6">
      <div class="card">
         <div class="card-body">
            <div class="stat-widget-one">
               <div class="stat-icon dib"><i class="ti-money text-success border-success"></i></div>
               <div class="stat-content dib">
                  <div class="stat-text">{{__('messages.total_sale')}}</div>
                  <div class="stat-digit">
                     {{$total_sell}}
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-lg-3 col-sm-6">
      <div class="card">
         <div class="card-body">
            <div class="stat-widget-one">
               <div class="stat-icon dib"><i class="ti-shopping-cart text-success border-success"></i></div>
               <div class="stat-content dib">
                  <div class="stat-text">{{__('messages.total_order')}}</div>
                  <div class="stat-digit">{{$total_order}}</div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-lg-3 col-sm-6">
      <div class="card">
         <div class="card-body">
            <div class="stat-widget-one">
               <div class="stat-icon dib"><i class="ti-bar-chart text-success border-success"></i></div>
               <div class="stat-content dib">
                  <div class="stat-text">{{__('messages.total_product')}}</div>
                  <div class="stat-digit">{{$total_product}}</div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-lg-3 col-sm-6">
      <div class="card">
         <div class="card-body">
            <div class="stat-widget-one">
               <div class="stat-icon dib"><i class="ti-user text-success border-success"></i></div>
               <div class="stat-content dib">
                  <div class="stat-text">{{__('messages.total_customers')}}</div>
                  <div class="stat-digit">{{$total_users}}</div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="col-md-12 flt">
   <div class="row">
   <div class="col-md-12 col-xl-6 col-12 flat-r">
      <div class="card">
         <div class="card-body">
            <h4 class="orderh4">
               <i class="fa fa-shopping-cart" aria-hidden="true"></i>     {{__('messages.latest_order')}}
               <a class="btn btn-primary btn-flat m-b-30 m-t-30 elec textorder" href="{{url('admin/order')}}">{{__('messages.Show All')}} </a>
            </h4>

            <div class="table-responsive dtdiv">
               <table id="latestorderTable" class="table table-striped dttablewidth">
                  <thead>
                     <tr>
                        <th>{{__('messages.order_id')}}</th>
                        <th>{{__('messages.customer')}}</th>
                        <th>{{__('messages.status')}}</th>
                        <th>{{__('messages.total')}}</th>
                     </tr>
                  </thead>
               </table>
            </div>
         </div>
      </div>
   </div>
   <div class="col-md-12 col-xl-6 col-12 flat-r">
      <div class="card">
         <div class="card-body">
            <h4 class="orderh4">
               <i class="fa fa-comments-o" aria-hidden="true"></i>    {{__('messages.latest_review')}}
                <a class="btn btn-primary btn-flat m-b-30 m-t-30 elec textorder" href="{{url('admin/review')}}">{{__('messages.Show All')}} </a>
            </h4>
            <div class="table-responsive dtdiv">
               <table id="myTablereview" class="table table-striped dttablewidth">
                  <thead>
                     <tr>
                        <th>{{__('messages.product')}}</th>
                        <th>{{__('messages.customer')}}</th>
                        <th>{{__('messages.ratting')}}</th>
                     </tr>
                  </thead>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>
</div>

@stop