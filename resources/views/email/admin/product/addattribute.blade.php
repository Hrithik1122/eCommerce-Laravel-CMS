@extends('admin.index') @section('content')
<div class="breadcrumbs">
    <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
            <div class="page-title">
                <h1>{{__('messages.attribute')}}</h1>
            </div>
        </div>
    </div>
    <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
            <div class="page-title">
                <ol class="breadcrumb text-right">
                    <li><a href="#">{{__('messages.product')}}</a></li>
                    <li><a href="{{url('admin/attribute')}}">{{__('messages.attribute')}}</a></li>
                    <li class="active">{{__('messages.add')}} {{__('messages.attribute')}}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content mt-3">
    <div class="rowset">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header">
                    <h4>{{__('messages.add')}} {{__('messages.attribute')}}</h4>
                </div>
                <div class="card-body">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active show" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">{{__('messages.general')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="profile-tab" aria-controls="profile" aria-selected="false">{{__('messages.values')}}</a>
                        </li>

                    </ul>
                    <div class="tab-content pl-3 p-1" id="myTabContent">
                        <div class="tab-pane fade active show" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="cmr1">
                                <div class="row form-group">
                                    <div class="col col-md-3">
                                        <label for="text-input" class=" form-control-label">{{__('messages.attributeset')}}<span class="reqfield">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <select name="set_id" required id="set_id" class="form-control">
                                            <option value="0">{{__('messages.select')}} {{__('messages.attributeset')}}</option>
                                            @foreach($allset as $asl)
                                            <option value="{{$asl->id}}">{{$asl->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col col-md-3">
                                        <label for="text-input" class=" form-control-label">{{__('messages.name')}}<span class="reqfield">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <input type="text" required id="name" name="name" placeholder="{{__('messages.name')}}" class="form-control">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col col-md-3">
                                        <label for="select" class=" form-control-label">{{__('messages.cate_gory')}}<span class="reqfield">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <select name="category" required id="categorydf" class="form-control">
                                            <option value="0">{{__('messages.all_category')}}</option>
                                            @foreach($category as $cat)
                                            <option value="{{$cat->id}}">{{$cat->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col col-md-3 col-12">
                                        <label class=" form-control-label">{{__('messages.filterable')}}</label>
                                    </div>
                                    <div class="col col-md-9">
                                        <div class="form-check">
                                            <div class="checkbox">
                                                <label for="checkbox1" class="form-check-label ">
                                                    <input type="checkbox" id="is_filter" name="is_filter" value="1" class="form-check-input">{{__('messages.filter_checkbox')}}
                                                </label>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group rowset">
                                      @if(Session::get("is_demo")=='1')
                                         <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary btn-sm">
                                            {{__('messages.save')}}
                                        </button>
                                       @else
                                            <button type="button" onclick="saveoption()" class="btn btn-primary btn-sm">
                                                {{__('messages.save')}}
                                            </button>
                                       @endif                                   
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <form action="{{url('admin/saveattribute')}}" method="post" class="cmr1">
                                {{csrf_field()}}
                                <input type="hidden" name="att_set_id" id="att_set_id" />
                                <input type="hidden" name="att_name" id="att_name" />
                                <input type="hidden" name="att_category" id="att_category" />
                                <input type="hidden" name="att_filter" id="att_filter" />
                                <input type="hidden" name="totalrow" id="totalrow" value='1' />
                                <table id="sortable" class="table table-striped cmr1">
                                    <thead> 
                                        <tr>
                                            <td></td>
                                            <td>{{__('messages.values')}}</td>
                                            <td></td>
                                        </tr>
                                    </thead>

                                    <tbody id="lstable">
                                        <tr id="row1">
                                            <td><i class="ti-layout-grid4-alt"></i></td>
                                            <td data-id="1">
                                                <input type="text" required id="value_1" name="values[]" placeholder="" class="form-control">
                                            </td>
                                            <td>
                                                <button class="btn btn-danger" onclick="removerow(1)"><i class="fa fa-trash f-s-25"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-outline-secondary fleft" onclick="addrowattribute()">{{__('messages.add_new_row')}}</button>
                                     @if(Session::get("is_demo")=='1')
                                         <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary florig">
                                              {{__('messages.submit')}}
                                        </button>
                                       @else
                                          <button type="submit" class="btn btn-primary florig">{{__('messages.submit')}}</button>
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