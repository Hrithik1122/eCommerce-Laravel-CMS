@extends('admin.index')
@section('content')
<div class="breadcrumbs">
    <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
            <div class="page-title">
                <h1>{{__('messages.orders')}}</h1>
            </div>
        </div>
    </div>
    <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
            <div class="page-title">
                <ol class="breadcrumb text-right">

                    <li class="active">{{__('messages.orders')}}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content mt-3">
 
        <div class="col-12">
            <div class="card">
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

                    <div class="table-responsive dtdiv">
                        <table id="ordercustomerTable" class="table table-striped table-bordered dttablewidth">
                            <thead>
                                <tr>
                                    <th>{{__('messages.order_id')}}</th>
                                    <th>{{__('messages.customer')}} {{__('messages.name')}}</th>
                                    <th>{{__('messages.payment_method')}}</th>
                                    <th>{{__('messages.shipping_method')}}</th>
                                    <th>{{__('messages.total')}}</th>
                                    <th>{{__('messages.view')}}</th>
                                    <th>{{__('messages.action')}}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

@stop