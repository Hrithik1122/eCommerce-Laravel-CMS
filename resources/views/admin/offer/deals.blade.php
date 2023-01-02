@extends('admin.index') @section('content')

<div class="breadcrumbs">
    <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
            <div class="page-title">
                <h1>{{__('messages.current_deals')}}</h1>
            </div>
        </div>
    </div>
    <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
            <div class="page-title">
                <ol class="breadcrumb text-right">

                    <li class="active">{{__('messages.current_deals')}}</li>
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
                    <div class="col-md-12 mrg30">
                        <input type="hidden" id="currentdiv" value="1" />
                    </div>
                    <div id="tableview">
                        <div id="editdeal" class="disno col-md-12 p-0">

                            <input type="hidden" name="id" id="id">
                            <div class="form-group col-lg-3 col-md-5 col-sm-6 paddiv float-right-1">
                                <label for="name" class=" form-control-label">
                                    {{__('messages.offers')}}
                                    <span class="reqfield">*</span>
                                </label>
                                <select name="offer_id" id="offer_id" class="form-control">
                                    <option></option>
                                </select>
                            </div>

                            <div class="col-md-3 col-sm-3 all-b float-right-1">
                                 @if(Session::get("is_demo")=='1')
                                 <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary mrgtop30">
                                    {{__('messages.submit')}}
                                </button>
                                @else
                                      <button class="btn btn-primary mrgtop30" type="button" onclick="changedeals()"> {{__('messages.submit')}}</button>
                                @endif
                              
                            </div>

                        </div>
                    </div>
                    <div class="table-responsive dtdiv">
                        <table id="dealsTable" class="table table-striped table-bordered dttablewidth">
                            <thead>
                                <tr>
                                    <th>{{__('messages.id')}}</th>
                                    <th>{{__('messages.banner')}}</th>
                                    <th>{{__('messages.title')}}</th>
                                    <th>{{__('messages.date')}}</th>
                                    <th>{{__('messages.deals')}}</th>
                                    <th>{{__('messages.action')}}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

</div>
<input type="hidden" id="offer_deal_lang" value="{{__('messages.offer_deal_lang')}}">
@stop