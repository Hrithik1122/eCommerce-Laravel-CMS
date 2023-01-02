@extends('admin.index') @section('content')
<div class="breadcrumbs">
    <div class="col-sm-4">
        <div class="page-header float-left">
            <div class="page-title">
                <h1>{{__('messages.option')}}</h1>
            </div>
        </div>
    </div>
    <div class="col-sm-8">
        <div class="page-header float-right">
            <div class="page-title">
                <ol class="breadcrumb text-right">
                   <li><a href="#">{{__('messages.product')}}</a></li>
                    <li><a href="{{url('admin/options')}}">{{__('messages.option')}}</a></li>
                    <li class="active">{{__('messages.edit')}} {{__('messages.option')}}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content mt-3">
    <div class="row rowset">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header">
                    <h4>{{__('messages.edit')}} {{__('messages.option')}}</h4>
                </div>
                <div class="card-body">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active show" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">{{__('messages.general')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">{{__('messages.values')}}</a>                            
                        </li>
                    </ul>
                    <div class="tab-content pl-3 p-1" id="myTabContent">
                        <div class="tab-pane fade active show" id="home" role="tabpanel" aria-labelledby="home-tab">
                           <div class="cmr1">
                                <div class="row form-group">
                                    <div class="col col-md-3">
                                        <label for="text-input" class=" form-control-label">{{__('messages.name')}}<span class="reqfield">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <input type="text" required id="name" name="name" placeholder="{{__('messages.name')}}" class="form-control" value="{{$option->name}}">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col col-md-3">
                                        <label for="select" class=" form-control-label">{{__('messages.type')}}<span class="reqfield">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <select name="type" required id="type" class="form-control">
                                            <option value="">{{__('messages.select')}} {{__('messages.type')}}</option>
                                            <option value="1" <?=$option->type ==1 ? ' selected="selected"' : '';?>>{{__('messages.dropdown')}}</option>
                                            <option value="2" <?=$option->type ==2 ? ' selected="selected"' : '';?>>{{__('messages.checkbox')}}</option>
                                            <option value="3" <?=$option->type ==3 ? ' selected="selected"' : '';?>>{{__('messages.radiobutton')}}</option>
                                            <option value="4" <?=$option->type ==4 ? ' selected="selected"' : '';?>>{{__('messages.multiple_select')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col col-md-3">
                                        <label class=" form-control-label">{{__('messages.required')}}</label>
                                    </div>
                                    <div class="col col-md-9">
                                        <div class="form-check">
                                            <div class="checkbox">
                                                <label for="checkbox1" class="form-check-label ">
                                                    <input type="checkbox" id="is_required" name="is_required" value="1" class="form-check-input" <?=$option->is_required ==1 ? ' checked="checked"' : '';?>>{{__('messages.req_option_msg')}}
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
                                         <button type="button" onclick="saveoptionall()" class="btn btn-primary btn-sm">
                                            {{__('messages.save')}}
                                         </button>
                                     @endif                                     
                                </div>
                           </div>
                        </div>

                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                             <form action="{{url('admin/updateoption')}}" method="post" class="cmr1">
                                {{csrf_field()}}
                                <input type="hidden" name="option_id" value="{{$option->id}}" />
                                <input type="hidden" name="option_name" id="option_name" value="{{$option->name}}"/>
                                <input type="hidden" name="option_type" id="option_type" value="{{$option->type}}"/>
                                <input type="hidden" name="option_required" id="option_required" value="{{$option->is_required}}"/>
                              
                                <table  id="sortable" class="table table-striped cmr1">
                                    <thead>
                                        <tr>
                                            <td></td>
                                            <td>{{__('messages.label')}}</td>
                                            <td>{{__('messages.price')}}</td>
                                            <td>{{__('messages.price_type')}}</td>
                                            <td></td>
                                        </tr>
                                    </thead>

                                    <tbody id="lstable">
                                        <?php $i=1;?>
                                     @foreach($optionvalue as $op)
                                        <tr id="row{{$i}}">
                                            <td><i class="ti-layout-grid4-alt"></i></td>
                                            <td data-id="{{$i}}">
                                                <input type="text" required id="label_{{$i}}" name="label[]" placeholder="" class="form-control" value="{{$op->label}}">
                                            </td>
                                            <td>
                                                <input type="text"  id="price_{{$i}}" name="price[]" placeholder="" class="form-control" value="{{$op->price}}">
                                            </td>
                                            <td>
                                                <select name="price_type[]" required id="price_type_{{$i}}" class="form-control">
                                                    <option value="1" <?=$op->price_type ==1 ? ' selected="selected"' : '';?>>{{__('messages.Fixed')}}</option>
                                                    <option value="2" <?=$op->price_type ==2 ? ' selected="selected"' : '';?>>{{__('messages.percentage')}}</option>
                                                </select>
                                            </td>
                                            <td><button class="btn btn-danger" onclick="removerow('{{$i}}')"><i class="fa fa-trash f-s-25"></i></button>
                                            </td>
                                        </tr>
                                        <?php $i++;?>
                                    @endforeach
                                    </tbody>

                                </table>
                                 <input type="hidden" name="totalrow" id="totalrow" value='{{$i}}'/>
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-outline-secondary fleft" onclick="addoptionrow()" >{{__('messages.add_new_row')}}</button>
                                       @if(Session::get("is_demo")=='1')
                                        <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary florig">
                                               {{__('messages.save')}}
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