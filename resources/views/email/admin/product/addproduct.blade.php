@extends('admin.index') @section('content')
<!-- <script src="{{asset('public/js/bootstrap.min.js')}}"></script>-->
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="breadcrumbs">
   <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
         <div class="page-title">
            <h1>{{__('messages.catalog')}}</h1>
         </div>
      </div>
   </div>
   <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li><a href="#">{{__('messages.catalog')}}</a>
               </li>
               <li><a href="{{url('admin/product')}}">{{__('messages.catalog')}}</a>
               </li>
               <li class="active">{{__('messages.add')}} {{__('messages.catalog')}}</li>
            </ol>
         </div>
      </div>
   </div>
</div>
<div class="content mt-3 ">
   <div class="rowset">
      <div class="col-lg-10 orderdiv">
         <div class="card">
            <div class="card-header">
               <h4>{{__('messages.add')}} {{__('messages.catalog')}}</h4>
            </div>
            <div class="card-body">
               <input type="hidden" name="product_id" id="product_id" value="0" />
               <div class="tab-content pl-3 p-1" id="myTabContent">
                  <div class="tab-pane fade active show" id="home" role="tabpanel" aria-labelledby="home-tab">
                     <div class="cmr1">
                        <div class="col-lg-12">
                           <div class="custom-tab">
                              <nav class="col-md-12 tabcatlog">
                                 <div class="nav nav-tabs tabdiv" id="nav-tab" role="tablist"> <a class="nav-item nav-link active" id="custom-nav-general-tab" data-toggle="tab" href="#custom-nav-general" role="tab" aria-controls="custom-nav-general" aria-selected="true">         {{__('messages.general')}}</a>
                                    <a class="nav-item nav-link tabdiv" id="custom-nav-price-tab" data-toggle="tab" href="#custom-nav-price" role="tab" aria-controls="custom-nav-price" aria-selected="false">{{__('messages.price')}}</a>
                                    <a class="nav-item nav-link tabdiv" id="custom-nav-inventory-tab" data-toggle="tab" href="#custom-nav-inventory" role="tab" aria-controls="custom-nav-inventory" aria-selected="false">{{__('messages.inventory')}}</a>
                                    <a class="nav-item nav-link tabdiv" id="custom-nav-imgls-tab" data-toggle="tab" href="#custom-nav-imgls" role="tab" aria-controls="custom-nav-imgls" aria-selected="false">{{__('messages.images')}}</a>
                                    <a class="nav-item nav-link  tabdiv" id="custom-nav-attribute-tab" data-toggle="tab" href="#custom-nav-attribute" role="tab" aria-controls="custom-nav-attribute" aria-selected="true">{{__('messages.attribute')}}</a>
                                    <a class="nav-item nav-link tabdiv" id="custom-nav-option-tab" data-toggle="tab" href="#custom-nav-option" role="tab" aria-controls="custom-nav-option" aria-selected="false">{{__('messages.option')}}</a>
                                    <a onclick="checkrelpro()" class="nav-item nav-link tabdiv" id="custom-nav-rel_pro-tab" data-toggle="tab" href="#custom-nav-rel_pro" role="tab" aria-controls="custom-nav-rel_pro" aria-selected="false">{{__('messages.realted_product')}}</a>     
                                 </div>
                              </nav>
                              <div class="tab-content col-md-12 p-0 " id="nav-tabContent">
                                 <div class="tab-pane fade show active pd10" id="custom-nav-general" role="tabpanel" aria-labelledby="custom-nav-general-tab" >
                                    <h3>{{__('messages.general')}}</h3>
                                    <div class="tabdivcatlog"></div>
                                    <form>
                                       <div class="form-group">
                                          <label for="name" class="control-label mb-1">{{__('messages.name')}}<span class="reqfield">*</span>
                                          </label>
                                          <input id="pro_name" name="pro_name" type="text" class="form-control" aria-required="true" aria-invalid="false" placeholder="{{__('messages.name')}}">
                                       </div>
                                       <div class="form-group">
                                          <label for="description" class="control-label mb-1">{{__('messages.description')}}<span class="reqfield">*</span>
                                          </label>
                                          <textarea name="description" id="description" class="editor"></textarea>
                                       </div>
                                       <div class="row">
                                          <div class="form-group col-md-4">
                                             <label for="category" class="control-label mb-1">{{__('messages.cate_gory')}}<span class="reqfield">*</span>
                                             </label>
                                             <select name="category" required id="catelogcategory" class="form-control" onchange="getsubcategory(this.value)">
                                                <option value="">{{__('messages.select')}} {{__('messages.cate_gory')}}</option>
                                                @foreach($category as $ca)
                                                <option value="{{$ca->id}}">{{$ca->name}}</option>
                                                @endforeach
                                             </select>
                                          </div>
                                          <div class="form-group col-md-4">
                                             <label for="subcategory" class="control-label mb-1">{{__('messages.sub_cat')}}<span class="reqfield">*</span>
                                             </label>
                                             <select name="subcategory" required id="subcategory" class="form-control" onchange="getbrand(this.value)">
                                                <option value="">{{__('messages.select')}} {{__('messages.sub_cat')}}</option>
                                             </select>
                                          </div>
                                          <div class="form-group  col-md-4" >
                                             <label for="brand" class="control-label mb-1">{{__('messages.brands')}}<span class="reqfield">*</span>
                                             </label>
                                             <select name="brand" required id="brand" class="form-control">
                                                <option value="">{{__('messages.select')}} {{__('messages.brands')}}</option>
                                             </select>
                                          </div>
                                          <div class="form-group col-md-6" >
                                             <label for="brand" class="control-label mb-1">{{__('messages.tax_name')}}<span class="reqfield">*</span>
                                             </label>
                                             <select name="texable" required id="texable" class="form-control">
                                                <option value="">{{__('messages.select')}} {{__('messages.tax_name')}}</option>
                                                @foreach($tax as $tz)
                                                <option value="{{$tz->id}}">{{$tz->tax_name}}</option>
                                                @endforeach
                                             </select>
                                          </div>
                                          <div class="form-group col-md-6" style="margin-top: 35px">
                                             <div class="col col-md-3 float-right-1">
                                                <label class=" form-control-label">{{__('messages.status')}}</label>
                                             </div>
                                             <div class="col col-md-9 float-left-1">
                                                <div class="form-check">
                                                   <div class="status">
                                                      <label for="checkbox1" class="form-check-label ">
                                                      <input type="checkbox" id="status" name="status" value="1" class="form-check-input">{{__('messages.pro_status')}}</label>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="form-group col-md-6" >
                                             <label for="brand" class="control-label mb-1">{{__('messages.product_color')}}<span class="reqfield">*</span>
                                             </label>
                                             <input type="color" name="colorpro" id="colorpro" class=" form-control" >
                                          </div>
                                          <div class="form-group col-md-6" >
                                             <label for="brand" class="control-label mb-1">{{__('messages.color_name')}}<span class="reqfield">*</span>
                                             </label>
                                             <input type="text" name="colorname" id="colorname"  value="" class=" form-control" >
                                          </div>
                                          <div class="form-group col-md-12">
                                             <label for="name" class="control-label mb-1 dttablewidth">{{__('messages.meta_keyword')}}</label>
                                             <input id="metakeyword" name="metakeyword" type="text" class="form-control" data-role="tagsinput" aria-invalid="false" placeholder="{{__('messages.meta_keyword')}}">
                                          </div>
                                          <div class="col-md-12 form-group rowset">
                                              @if(Session::get("is_demo")=='1')
                                               <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary btn-flat m-b-30 m-t-30">
                                                    {{__('messages.save')}} 
                                              </button>
                                             @else
                                                 <button class="btn btn-primary btn-flat m-b-30 m-t-30" type="button" onclick="Savegeneralinfo()">{{__('messages.save')}}</button>
                                             @endif 
                                           
                                          </div>
                                       </div>
                                    </form>
                                 </div>
                                 <div class="tab-pane fade pd10" id="custom-nav-price" role="tabpanel" aria-labelledby="custom-nav-price-tab">
                                    <h3>{{__('messages.price')}}</h3>
                                    <div class="tabdivcatlog"></div>
                                    <form>
                                       <div class="row">
                                          <div class="form-group col-md-4" >
                                             <label for="name" class="control-label mb-1">{{__('messages.MRP')}}<span class="reqfield">*</span>
                                             </label>
                                             <input id="mrp" name="mrp" type="text" class="form-control" aria-required="true" aria-invalid="false" placeholder="{{__('messages.MRP')}}" required>
                                          </div>
                                          <div class="form-group col-md-4">
                                             <label for="name" class="control-label mb-1">{{__('messages.selling_price')}}<span class="reqfield">*</span>
                                             </label>
                                             <input id="price" name="price" type="text" class="form-control" aria-required="true" aria-invalid="false" placeholder="{{__('messages.selling_price')}}" required>
                                          </div>
                                          <div class="form-group col-md-4">
                                             <label for="name" class="control-label mb-1">{{__('messages.spe_price')}}</label>
                                             <input id="special_price" name="special_price" type="text" class="form-control" aria-invalid="false" placeholder="{{__('messages.spe_price')}}">
                                          </div>
                                          <div class="form-group col-md-6">
                                             <label for="name" class="control-label mb-1">{{__('messages.spe_price')}} {{__('messages.start')}}</label>
                                             <input id="spe_pri_start" name="spe_pri_start" type="text" class="form-control" aria-required="true" aria-invalid="false">
                                          </div>
                                          <div class="form-group col-md-6">
                                             <label for="name" class="control-label mb-1">{{__('messages.spe_price')}} {{__('messages.to')}}</label>
                                             <input id="spe_pri_to" name="spe_pri_to" type="text" class="form-control" aria-required="true" aria-invalid="false">
                                          </div>
                                       </div>
                                       <div class="row form-group rowset">
                                          <button class="btn btn-primary btn-flat m-b-30 m-t-30" type="button" onclick="saveproductprice()">{{__('messages.save')}}</button>
                                       </div>
                                    </form>
                                 </div>
                                 <div class="tab-pane fade pd10" id="custom-nav-inventory" role="tabpanel" aria-labelledby="custom-nav-inventory-tab">
                                    <h3>{{__('messages.inventory')}}</h3>
                                    <div class="tabdivcatlog"></div>
                                    <form>
                                       <div class="row">
                                          <div class="form-group col-md-4">
                                             <label for="name" class="control-label mb-1">{{__('messages.SKU')}}</label>
                                             <input id="sku" name="sku" type="text" class="form-control" aria-invalid="false" placeholder="{{__('messages.SKU')}}">
                                          </div>
                                          <div class="form-group col-md-4">
                                             <label for="name" class="control-label mb-1">{{__('messages.inventory_mang')}}</label>
                                             <select name="inventory" required id="inventory" class="form-control">
                                                <option value="0">{{__('messages.donot_track_inven')}}</option>
                                                <option value="1">{{__('messages.track_inven')}}</option>
                                             </select>
                                          </div>
                                          <div class="form-group col-md-4">
                                             <label for="name" class="control-label mb-1">{{__('messages.stock_avilable')}}</label>
                                             <select name="stock" required id="stock" class="form-control">
                                                <option value="1">{{__('messages.in_stock')}}</option>
                                                <option value="0">{{__('messages.outstock')}}</option>
                                             </select>
                                          </div>
                                       </div>
                                       <div class="row form-group rowset">
                                          <button class="btn btn-primary btn-flat m-b-30 m-t-30" type="button" onclick="SaveInventory()">{{__('messages.save')}}</button>
                                       </div>
                                    </form>
                                 </div>
                                 <div class="tab-pane fade pd10" id="custom-nav-imgls" role="tabpanel" aria-labelledby="custom-nav-imgls-tab">
                                    <h3>{{__('messages.images')}}</h3>
                                    <div class="tabdivcatlog"></div>
                                    <form id="imageUploadForm" action="javascript:void(0)" enctype="multipart/form-data">
                                       <div class="mar20">
                                          <h4 class="orderdiv">{{__('messages.basic_img')}}</h4>
                                          <div id="uploaded_image">
                                             <div class="upload-btn-wrapper">
                                                <button class="btn imgcatlog">
                                                <img src="{{asset('admin/images/imgplaceholder.png')}}" alt="..." class="img-thumbnail imgsize" id="basic_img" >
                                                </button>
                                                <input type="file" name="upload_image" id="upload_image" />
                                             </div>
                                          </div>
                                       </div>
                                    </form>
                                    <div class="mar20">
                                       <h4 class="orderdiv">{{__('messages.add_img')}}</h4>
                                       <div id="additional_image" class="fleft"></div>
                                       <div class="upload-btn-wrapper">
                                          <button class="btn imgcatlog" >
                                          <img src="{{asset('admin/images/add_image.png')}}" alt="..." class="img-thumbnail imgsize">
                                          </button>
                                          <input type="file" name="add_image" id="add_image" />
                                       </div>
                                    </div>
                                    <input type="hidden" name="add_total_img" id="add_total_img" value="0" />
                                    <div class="row form-group mar20">
                                       <button class="btn btn-primary btn-flat m-b-30 m-t-30" type="button" onclick="saveimages()">{{__('messages.save')}}</button>
                                    </div>
                                    </form>
                                 </div>
                                 <div class="tab-pane fade show  pd10" id="custom-nav-attribute" role="tabpanel" aria-labelledby="custom-nav-attribute-tab">
                                    <h3>{{__('messages.attribute')}}</h3>
                                    <div class="tabdivcatlog"></div>
                                    <form class="new">
                                       <input type="hidden" name="totalrow" id="totalrow" value='0' />
                                       <table id="sortable" class="table table-striped table-bordered cmr1">
                                          <thead>
                                             <tr>
                                                <td class="td2"></td>
                                                <td class="td3">{{__('messages.attribute')}}</td>
                                                <td class="td4">{{__('messages.value')}}</td>
                                                <td class="td2"></td>
                                             </tr>
                                          </thead>
                                          <tbody id="lstable">
                                          </tbody>
                                       </table>
                                       <div id="container"></div>
                                       <div class="col-md-12 p-0">
                                          <button type="button" class="btn btn-outline-secondary fleft" onclick="addrow()">{{__('messages.add_new_row')}}</button>
                                          <button type="button" class="btn btn-primary florig" onclick="saveattibute()">{{__('messages.save')}}</button>
                                       </div>
                                    </form>
                                 </div>
                                 <div class="tab-pane fade pd10" id="custom-nav-option" role="tabpanel" aria-labelledby="custom-nav-option-tab">
                                    <h3>{{__('messages.option')}}</h3>
                                    <div class="tabdivcatlog"></div>
                                    @include('admin.product.optionadd')
                                 </div>
                                 <div class="tab-pane fade pd10" id="custom-nav-rel_pro" role="tabpanel" aria-labelledby="custom-nav-rel_pro-tab">
                                    <h3>{{__('messages.realted_product')}}</h3>
                                    <div class="tabdivcatlog"></div>
                                    <form>
                                    <div class="col-md-12 savi">
                                       <button type="button" class="btn btn-primary florig"  onclick="SaveRelatedproduct()">{{__('messages.save')}}</button>
                                    </div>
                                    <input type="hidden" id="rel_pro" value="" />
                                    <table id="related_product" class="table table-striped table-bordered dttablewidth">
                                       <thead>
                                          <tr>
                                             <th>
                                                <input name="select_all" value="1" id="select-all" type="checkbox" onchange="allselect('related')" />
                                             </th>
                                             <th>{{__('messages.thumbnail')}}</th>
                                             <th>{{__('messages.name')}}</th>
                                             <th>{{__('messages.price')}}</th>
                                          </tr>
                                       <thead>
                                    </table>
                                    <form>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<input type="hidden" id="msgtype" value="{{__('messages.type')}}">
<input type="hidden" id="check_price" value="{{__('messages.check_price')}}">
<input type="hidden" id="special_price_check" value="{{__('messages.special_price_check')}}">
<input type="hidden" id="sepical_price_vaildate" value="{{__('messages_error_success.sepical_price_vaildate')}}">
<input type="hidden" id="selling_mrp_vaildate" value="{{__('messages_error_success.selling_mrp_vaildate')}}">
<input type="hidden" id="up_pro" value="0" />
<input type="hidden" id="cross_pro" value="0" />
<input type="hidden" id="sku_already" value="{{__('messages_error_success.sku_already')}}">
@stop 
@section('footer')
<script type="text/javascript" src="{{asset('public/js/product.js')}}"></script>
<script>
   CKEDITOR.replace('description');
</script>
@stop