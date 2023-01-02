@extends('admin.index') @section('content')
<div class="breadcrumbs">
    <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
            <div class="page-title">
                <h1>{{__('messages.feature_product')}}</h1>
            </div>
        </div>
    </div>
    <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
            <div class="page-title">
                <ol class="breadcrumb text-right">
                    <li class="active">{{__('messages.feature_product')}}</li>
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
                    <div class="row mrg30">
                        <form action="{{url('admin/addfeatureproduct')}}" method="post">
                            {{csrf_field()}}
                            <div class="col-md-6 col-sm-6 col-12 float-right-1">
                                <select class="form-control" name="product_id" id="product_id" required>
                                    <option value="">{{__('messages.select_product')}}</option>
                                    @foreach($product as $pro)
                                    <option value="{{$pro->id}}">{{$pro->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 col-sm-6 col-12">
                                   @if(Session::get("is_demo")=='1')
                                        <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="florig btn btn-primary">
                                                {{__('messages.add_fetureproduct')}}
                                       </button>
                                  @else
                                         <button type="submit" name="btnsubmit" class="btn btn-primary fea">{{__('messages.add_fetureproduct')}}</button>
                                  @endif 
                            </div>
                        </form>
                    </div>
                    <div id="tableview">
                        <div class="table-responsive dtdiv">
                            <table id="featuretable" class="table table-striped table-bordered dttablewidth">
                                <thead>
                                    <tr>
                                        <th>{{__('messages.id')}}</th>
                                        <th>{{__('messages.image')}}</th>
                                        <th>{{__('messages.product')}}</th>
                                        <th>{{__('messages.action')}}</th>
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