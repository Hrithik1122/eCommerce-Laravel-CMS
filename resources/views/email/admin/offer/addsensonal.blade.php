@extends('admin.index') @section('content')

<div class="breadcrumbs">
    <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
            <div class="page-title">
                <h1>{{__('messages.seasonal_offer')}}</h1>
            </div>
        </div>
    </div>
    <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
            <div class="page-title">
                <ol class="breadcrumb text-right">
                    <li>
                        <a href="{{url('admin/sensonal_offer')}}">
                         {{__('messages.seasonal_offer')}}
                        </a>
                    </li>
                    <li class="active">
                        {{__('messages.add_sen')}}
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content mt-3">
    <div class="rowset">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <strong class="card-title">{{__('messages.add_sen')}}</strong>
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
                            <form action="{{url('admin/storesensonal')}}" method="post" enctype="multipart/form-data">
                                {{csrf_field()}}

                                <div class="form-group">
                                    <label for="name" class=" form-control-label">
                                        {{__('messages.title')}}
                                        <span class="reqfield">*</span>
                                    </label>
                                    <input type="text" id="title" placeholder="{{__('messages.title')}}" class="form-control" name="title" required>
                                </div>
                                <div class="form-group row paddiv">
                                    <div class="col-md-4">
                                        <label for="name" class=" form-control-label ">
                                            {{__('messages.cate_gory')}}
                                            <span class="reqfield">*</span>
                                        </label>
                                        <select name="category" id="categorydiv" class="form-control" required>
                                            <option value="">{{__('messages.select_category')}}</option>
                                             <option value="0">{{__('messages.all')}}</option>
                                            @foreach($category as $ca)
                                            <option value="{{$ca->id}}">{{$ca->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="name" class=" form-control-label">
                                            {{__('messages.fixed_from')}}
                                            <span class="reqfield">*</span>
                                        </label>
                                        <input type="text" name="fixed_form" id="fixed_form" class="form-control" placeholder="0" required />
                                    </div>
                                    <div class="col-md-4">
                                        <label for="name" class=" form-control-label">
                                            {{__('messages.fixed_to')}}
                                            <span class="reqfield">*</span>
                                        </label>
                                        <input type="text" name="fixed_to" id="fixed_to" class="form-control" required placeholder="100" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="name" class=" form-control-label">
                                        {{__('messages.banner')}}
                                        <span class="reqfield">*</span>
                                    </label>
                                    <input type="file" id="banner" class="form-control" name="banner" required>
                                </div>

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