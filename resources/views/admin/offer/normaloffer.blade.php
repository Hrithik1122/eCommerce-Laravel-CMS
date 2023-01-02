@extends('admin.index') @section('content')
<div class="breadcrumbs">
    <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
            <div class="page-title">
                <h1>{{__('messages.offers')}}</h1>
            </div>
        </div>
    </div>
    <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
            <div class="page-title">
                <ol class="breadcrumb text-right">
                    <li class="active">{{__('messages.offers')}}</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="content mt-3">
    
        <div class="col-12">
            <div class="card">
                <div class="card-body">@if(Session::has('message'))
                    <div class="col-sm-12">
                        <div class="alert  {{ Session::get('alert-class', 'alert-info') }} alert-dismissible fade show" role="alert">{{ Session::get('message') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>@endif
                    <button onclick="addoffer(2)" class="btn btn-primary btn-flat m-b-30 m-t-30">{{__('messages.add')}} {{__('messages.normal_offer')}}</button>
                    <div id="graphicalview " class="mrgtop30">
                        <div class="container">
                            <div class="slider-section">
                                <div class="col-md-12">
                                <div class="row">
                                       @if(isset($data[0]))
                                        <div class="col-md-4 banner-1 mrg30">
                                            <img src="{{asset('public/upload/offer/image').'/'.$data[0]}}" alt="" class="img-responsive">
                                            <div class="banner-text"></div>
                                        </div>
                                        @endif
                                        @if(isset($data[1]))
                                        <div class=" col-md-4 banner-2 mrg30">
                                            <img src="{{asset('public/upload/offer/image').'/'.$data[1]}}" alt="" class="img-responsive" >
                                            <div class="banner-text"></div>
                                        </div>
                                        @endif
                                        @if(isset($data[2]))
                                        <div class=" col-md-4 banner-2 mrg30">
                                            <img src="{{asset('public/upload/offer/image').'/'.$data[2]}}" alt="" class="img-responsive">
                                            <div class="banner-text"></div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive dtdiv">
                        <table id="normalofferTable" class="table table-striped table-bordered dttablewidth">
                            <thead>
                                <tr>
                                    <th>{{__('messages.id')}}</th>
                                    <th>{{__('messages.banner')}}</th>
                                    <th>{{__('messages.title')}}</th>
                                    <th>{{__('messages.date')}}</th>
                                    <th>{{__('messages.offer_on')}}</th>
                                    <th>{{__('messages.name')}}</th>
                                    <th>{{__('messages.offer_price')}}</th>
                                    <th>{{__('messages.action')}}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    
</div>
@stop