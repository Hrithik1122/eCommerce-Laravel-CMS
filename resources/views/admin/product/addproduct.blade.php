@extends('admin.index') @section('content')
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
               <li><a href="{{url('admin/product')}}">{{__('messages.catalog')}}</a></li>
               <li class="active">{{__('messages.save')}} {{__('messages.catalog')}}</li>
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
               <h4>{{__('messages.save')}} {{__('messages.catalog')}}</h4>
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
               <div class="tab-content pl-3 p-1" id="myTabContent">
                  <div class="tab-pane fade active show" id="home" role="tabpanel" aria-labelledby="home-tab">
                     <div class="cmr1">
                        <div class="col-lg-12">
                           <div class="custom-tab">
                              <nav class="col-md-12 tabcatlog">
                                 <div class="nav nav-tabs tabdiv" id="nav-tab" role="tablist">
                                    <a class="nav-item nav-link <?= $tab==1?"active":"tabdiv" ?>" id="custom-nav-general-tab" data-toggle="tab" href="#custom-nav-general" role="tab" aria-controls="custom-nav-general" aria-selected="true">{{__('messages.general')}}</a>
                                    <a class="nav-item nav-link <?= $tab==2?"active":"tabdiv" ?>" id="custom-nav-price-tab" data-toggle="tab" href="#custom-nav-price" role="tab" aria-controls="custom-nav-price" aria-selected="false">{{__('messages.price')}}</a>
                                    <a class="nav-item nav-link <?= $tab==3?"active":"tabdiv" ?>" id="custom-nav-inventory-tab" data-toggle="tab" href="#custom-nav-inventory" role="tab" aria-controls="custom-nav-inventory" aria-selected="false">{{__('messages.inventory')}}</a>
                                    <a class="nav-item nav-link <?= $tab==4?"active":"tabdiv" ?>" id="custom-nav-imgls-tab" data-toggle="tab" href="#custom-nav-imgls" role="tab" aria-controls="custom-nav-imgls" aria-selected="false">{{__('messages.images')}}</a>
                                    <a class="nav-item nav-link <?= $tab==5?"active":"tabdiv" ?>" id="custom-nav-attribute-tab" data-toggle="tab" href="#custom-nav-attribute" role="tab" aria-controls="custom-nav-attribute" aria-selected="true">{{__('messages.attribute')}}</a>
                                    <a class="nav-item nav-link <?= $tab==6?"active":"tabdiv" ?>" id="custom-nav-option-tab" data-toggle="tab" href="#custom-nav-option" role="tab" aria-controls="custom-nav-option" aria-selected="false">{{__('messages.option')}}</a>
                                    <a class="nav-item nav-link tabdiv <?= $tab==7?"active":"tabdiv" ?>" id="custom-nav-rel_pro-tab" data-toggle="tab" href="#custom-nav-rel_pro" role="tab" aria-controls="custom-nav-rel_pro" aria-selected="false">{{__('messages.realted_product')}}</a>     
                                 </div>
                              </nav>
                              <div class="tab-content col-md-12 p-0 " id="nav-tabContent">
                                 <div class="tab-pane fade <?= $tab==1?"show active":"" ?> pd10" id="custom-nav-general" role="tabpanel" aria-labelledby="custom-nav-general-tab" >
                                    <h3>{{__('messages.general')}}</h3>
                                    <div class="tabdivcatlog"></div>
                                    <form action="{{url('admin/saveproduct')}}" method="post">
                                       {{csrf_field()}}
                                       <input type="hidden" name="product_id" id="product1" value
                                       ="{{$product_id}}"/>
                                       <div class="form-group">
                                          <label for="name" class="control-label mb-1">{{__('messages.name')}}<span class="reqfield">*</span>
                                          </label>
                                          <input id="pro_name" name="pro_name" value="<?= isset($data->name)?$data->name:""?>" type="text" class="form-control" aria-required="true" aria-invalid="false" placeholder="{{__('messages.name')}}">
                                       </div>
                                       <div class="form-group">
                                          <label for="description" class="control-label mb-1">{{__('messages.description')}}<span class="reqfield">*</span>
                                          </label>
                                          <textarea name="description" id="description" class="editor"><?= isset($data->description)?$data->description:""?></textarea>
                                       </div>
                                       <div class="row">
                                          <div class="form-group col-md-4">
                                             <label for="category" class="control-label mb-1">{{__('messages.cate_gory')}}<span class="reqfield">*</span>
                                             </label>
                                             <select name="category" required id="catelogcategory" class="form-control" onchange="getsubcategory(this.value)">
                                                <option value="">{{__('messages.select')}} {{__('messages.cate_gory')}}</option>
                                                @foreach($category as $ca)
                                                <option value="{{$ca->id}}" <?= isset($data->category)&&$data->category==$ca->id?"selected='selected'":""?> >{{$ca->name}}</option>
                                                @endforeach
                                             </select>
                                          </div>
                                          <div class="form-group col-md-4">
                                             <label for="subcategory" class="control-label mb-1">{{__('messages.sub_cat')}}<span class="reqfield">*</span>
                                             </label>
                                             <select name="subcategory" required id="subcategory" class="form-control" onchange="getbrand(this.value)">
                                                <option value="">{{__('messages.select')}} {{__('messages.sub_cat')}}</option>
                                                 @if(isset($subcategory))
                                                   @foreach($subcategory as $sub)
                                                      <option value="{{$sub->id}}" <?= isset($data->subcategory)&&$data->subcategory==$sub->id?"selected='selected'":""?>>{{$sub->name}}</option>
                                                   @endforeach
                                                 @endif
                                             </select>
                                          </div>
                                          <div class="form-group  col-md-4" >
                                             <label for="brand" class="control-label mb-1">{{__('messages.brands')}}<span class="reqfield">*</span>
                                             </label>
                                             <select name="brand" required id="brand" class="form-control">
                                                @if(isset($brand))
                                                  @foreach($brand as $br)
                                                     <option value="{{$br->id}}" <?= isset($data->brand)&&$data->brand==$br->id?"selected='selected'":""?>>{{$br->brand_name}}</option>
                                                  @endforeach
                                                @endif
                                                <option value="">{{__('messages.select')}} {{__('messages.brands')}}</option>
                                             </select>
                                          </div>
                                          <div class="form-group col-md-6" >
                                             <label for="brand" class="control-label mb-1">{{__('messages.tax_name')}}<span class="reqfield">*</span>
                                             </label>
                                             <select class="form-control" name="texable" id="texable" required>
                                                <option value="">{{__('messages.select').' '.__('messages.tax_name')}}</option>
                                                @foreach($taxes as $t)
                                                   <option value="{{$t->id}}" <?= isset($data->tax_class)&&$data->tax_class==$t->id?"selected='selected'":""?>>{{$t->tax_name}}</option>
                                                @endforeach
                                             </select>
                                          </div>
                                          <div class="form-group col-md-6">
                                             <label for="name" class="control-label mb-1 dttablewidth">{{__('messages.meta_keyword')}}</label>
                                             <input id="metakeyword" value="<?= isset($data->meta_keyword)?$data->meta_keyword:""?>" name="metakeyword" type="text" class="form-control" data-role="tagsinput" aria-invalid="false" placeholder="{{__('messages.meta_keyword')}}">
                                          </div>
                                          <div class="form-group col-md-6" >
                                             <label for="brand" class="control-label mb-1">{{__('messages.product_color')}}<span class="reqfield">*</span>
                                             </label>
                                             <input type="color" name="colorpro" value="<?= isset($data->product_color)?$data->product_color:""?>" id="colorpro" class=" form-control" >
                                          </div>
                                          <div class="form-group col-md-6" >
                                             <label for="brand" class="control-label mb-1">{{__('messages.color_name')}}<span class="reqfield">*</span>
                                             </label>
                                             <input type="text" name="colorname" id="colorname"  value="<?= isset($data->color_name)?$data->color_name:""?>" value="" class=" form-control" >
                                          </div>
                                          
                                          <div class="col-md-12 form-group rowset">
                                             @if(Session::get("is_demo")=='1')
                                             <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary btn-flat m-b-30 m-t-30">
                                             {{__('messages.save')}} 
                                             </button>
                                             @else
                                             <button class="btn btn-primary btn-flat m-b-30 m-t-30" type="submit">{{__('messages.save')}}</button>
                                             @endif 
                                          </div>
                                       </div>
                                    </form>
                                 </div>
                                 <div class="tab-pane fade <?= $tab==2?"show active":"" ?> pd10" id="custom-nav-price" role="tabpanel" aria-labelledby="custom-nav-price-tab">
                                    <h3>{{__('messages.price')}}</h3>
                                    <div class="tabdivcatlog"></div>
                                    <form action="{{url('admin/saveprice')}}" method='post'>
                                        {{csrf_field()}}
                                        <input type="hidden" name="product_id" id="product1" value
                                       ="{{$product_id}}"/>
                                       <div class="row">
                                          <div class="form-group col-md-4" >
                                             <label for="name" class="control-label mb-1">{{__('messages.MRP')}}<span class="reqfield">*</span>
                                             </label>
                                             <input id="mrp" name="mrp" type="text" class="form-control" value="<?= isset($data->MRP)?$data->MRP:""?>" aria-required="true" aria-invalid="false" placeholder="{{__('messages.MRP')}}" required>
                                          </div>
                                          <div class="form-group col-md-4">
                                             <label for="name" class="control-label mb-1">{{__('messages.selling_price')}}<span class="reqfield">*</span>
                                             </label>
                                             <input id="price" name="price" type="text" class="form-control" aria-required="true" value="<?= isset($data->price)?$data->price:""?>" aria-invalid="false" placeholder="{{__('messages.selling_price')}}" required>
                                          </div>
                                          <div class="form-group col-md-4">
                                             <label for="name" class="control-label mb-1">{{__('messages.spe_price')}}</label>
                                             <input id="special_price" name="special_price" type="text" class="form-control" aria-invalid="false" value="<?= isset($data->special_price)?$data->special_price:""?>" placeholder="{{__('messages.spe_price')}}">
                                          </div>
                                          <div class="form-group col-md-6">
                                             <label for="name" class="control-label mb-1">{{__('messages.spe_price')}} {{__('messages.start')}}</label>
                                             <input id="spe_pri_start" name="spe_pri_start" type="text" class="form-control" aria-required="true" value="<?= isset($data->special_price_start)?$data->special_price_start:""?>" aria-invalid="false">
                                          </div>
                                          <div class="form-group col-md-6">
                                             <label for="name" class="control-label mb-1">{{__('messages.spe_price')}} {{__('messages.to')}}</label>
                                             <input id="spe_pri_to" name="spe_pri_to" type="text" class="form-control" aria-required="true" value="<?= isset($data->special_price_to)?$data->special_price_to:""?>" aria-invalid="false">
                                          </div>
                                       </div>
                                       <div class="row form-group rowset">
                                          <button class="btn btn-primary btn-flat m-b-30 m-t-30" type="submit" >{{__('messages.save')}}</button>
                                       </div>
                                    </form>
                                 </div>
                                 <div class="tab-pane fade <?= $tab==3?"show active":"" ?> pd10" id="custom-nav-inventory" role="tabpanel" aria-labelledby="custom-nav-inventory-tab">
                                    <h3>{{__('messages.inventory')}}</h3>
                                    <div class="tabdivcatlog"></div>
                                    <form action="{{url('admin/saveinventory')}}" method='post'>
                                        {{csrf_field()}}
                                        <input type="hidden" name="product_id" id="product1" value
                                       ="{{$product_id}}"/>
                                       <div class="row">
                                          <div class="form-group col-md-4">
                                             <label for="name" class="control-label mb-1">{{__('messages.SKU')}}</label>
                                             <input id="sku" name="sku" type="text" value="<?= isset($data->sku)?$data->sku:""?>" class="form-control" aria-invalid="false" placeholder="{{__('messages.SKU')}}">
                                          </div>
                                          <div class="form-group col-md-4">
                                             <label for="name" class="control-label mb-1">{{__('messages.inventory_mang')}}</label>
                                             <select name="inventory" required id="inventory" class="form-control">
                                                <option value="0" <?= isset($data->inventory)&&$data->inventory==0?"selected='selected'":""?>>{{__('messages.donot_track_inven')}}</option>
                                                <option value="1" <?= isset($data->inventory)&&$data->inventory==1?"selected='selected'":""?>>{{__('messages.track_inven')}}</option>
                                             </select>
                                          </div>
                                          <div class="form-group col-md-4">
                                             <label for="name" class="control-label mb-1">{{__('messages.stock_avilable')}}</label>
                                             <select name="stock" required id="stock" class="form-control">
                                                <option value="1" <?= isset($data->stock)&&$data->stock==1?"selected='selected'":""?>>{{__('messages.in_stock')}}</option>
                                                <option value="0" <?= isset($data->stock)&&$data->stock==0?"selected='selected'":""?>>{{__('messages.outstock')}}</option>
                                             </select>
                                          </div>
                                       </div>
                                       <div class="row form-group rowset">
                                          <button class="btn btn-primary btn-flat m-b-30 m-t-30" type="submit">{{__('messages.save')}}</button>
                                       </div>
                                    </form>
                                 </div>
                                 <div class="tab-pane fade <?= $tab==4?"show active":"" ?> pd10" id="custom-nav-imgls" role="tabpanel" aria-labelledby="custom-nav-imgls-tab">
                                    <h3>{{__('messages.images')}}</h3>
                                    <div class="tabdivcatlog"></div>
                                    <form action="{{url('admin/saveproductimage')}}" method="post">                                   
                                       {{csrf_field()}}
                                        <input type="hidden" name="product_id" id="product1" value
                                       ="{{$product_id}}"/>
                                       <div class="mar20">
                                          <h4 class="orderdiv">{{__('messages.basic_img')}}</h4>
                                          <div id="uploaded_image">
                                             <div class="upload-btn-wrapper">
                                                <button class="btn imgcatlog">
                                                   <input type="hidden" name="real_basic_img" id="real_basic_img" value="<?= isset($data->basic_image)?$data->basic_image:""?>"/>
                                                   <?php 
                                                         if(isset($data->basic_image)){
                                                             $path=asset('public/upload/product')."/".$data->basic_image;
                                                         }
                                                         else{
                                                             $path=asset('public/admin/images/imgplaceholder.png');
                                                         }
                                                   ?>
                                                <img src="{{$path}}" alt="..." class="img-thumbnail imgsize"  id="basic_img" >
                                                </button>
                                                <input type="hidden" name="basic_img" id="basic_img1"/>
                                                <input type="file" name="upload_image" id="upload_image" />
                                             </div>
                                          </div>
                                       </div>
                                   
                                    <div class="mar20">
                                       <h4 class="orderdiv">{{__('messages.add_img')}}</h4>
                                     
                                             <div id="additional_image" class="fleft">
                                                <?php $i=0;?>
                                                @if(isset($data->additional_image))
                                                  <?php $imagels=explode(",",$data->additional_image);;?>
                                                   @foreach($imagels as $imls)
                                                      <div id="imgid{{$i}}" class="add-img">
                                                         <input type="hidden" name="add_real_img[]" value="{{$imls}}"/>
                                                         <img src="{{asset('public/upload/product').'/'.$imls}}" class="img-thumbnail imgsize" id="additional_img{{$i}}" name="arrimg[]" />
                                                            <div class="add-box">
                                                               <input type="hidden" id="additionalimg{{$i}}" name="additional_img[]" value="{{asset('public/upload/product').'/'.$imls}}"/>
                                                               <input type="button" id="removeImage1" value="x" class="btn-rmv1" onclick="removeimg('{{$i}}')" />
                                                            </div>
                                                      </div>
                                                      <?php $i++;?>
                                                   @endforeach
                                                @endif                                                                                           
                                             </div>
                                             <div class="upload-btn-wrapper">
                                                      <button class="btn imgcatlog">
                                                      <img src="{{asset('public/admin/images/add_image.png')}}" alt="..." class="img-thumbnail imgsize">
                                                      </button>
                                                      <input type="file" name="add_image" id="add_image" />
                                                   </div>
                                      </div>       
                                    <input type="hidden" name="add_total_img" id="add_total_img" value="{{$i}}" />
                                    <div class="row form-group col-md-12" style="margin-top: 15px;margin-left: 10px;">
                                          <button class="btn btn-primary btn-flat m-b-30 m-t-30" type="submit">{{__('messages.save')}}</button>
                                    </div>
                                    </form>
                                 </div>
                                 <div class="tab-pane fade <?= $tab==5?"show active":"" ?>  pd10" id="custom-nav-attribute" role="tabpanel" aria-labelledby="custom-nav-attribute-tab">
                                    <h3>{{__('messages.attribute')}}</h3>
                                    <div class="tabdivcatlog"></div>
                                     <form action="{{url('admin/saveproductattibute')}}" method="post">                                   
                                       {{csrf_field()}}
                                        <input type="hidden" name="product_id" id="product1" value
                                       ="{{$product_id}}"/>
                                       <div class="categories-accordion mrg30" uk-accordion="targets: > div > .category-wrap">
                                          <div class="categories-sort-wrap uk-sortable uk-margin-top" uk-sortable="handle: .sort-categories" id="attributelist">
                                             <?php $i=0;?>
                                             @if(isset($data->attributels)&&count($data->attributels)>0)
                                                @foreach($data->attributels as $da)
                                                  <div class="category-wrap" data-id="{{$i}}" id="mainattr{{$i}}">
                                                <h3 class="uk-accordion-title uk-background-secondary uk-light uk-padding-small">
                                                   <div class="uk-sortable-handle sort-categories uk-display-inline-block ti-layout-grid4-alt" ></div>
                                                   {{__('messages.New Attributes')}}
                                                </h3>
                                                <div class="uk-accordion-content categories-content " style="margin-top: 0px;padding:0px">
                                                   <table class="table table-striped table-bordered">
                                                      <tbody>
                                                         <tr>
                                                            <td>
                                                               <input type="text" required name="attributeset[{{$i}}][set]" class="form-control" value="{{$da->attributeset}}" placeholder="Enter Attribute Set">
                                                               <table class="table table-striped table-bordered cmr1">
                                                                  <thead>
                                                                     <tr>
                                                                        <th>Attribute</th>
                                                                        <th>Value</th>
                                                                        <th></th>
                                                                     </tr>
                                                                  </thead>
                                                                  <tbody id="morerow{{$i}}">
                                                                     <?php 
                                                                           $label=explode(',',$da->attribute);
                                                                           $value=explode(",",$da->value);
                                                                     ?>
                                                                     <?php for($j=0;$j<count($label);$j++){
                                                                             $index=$j+1;
                                                                        ?>
                                                                     <tr id="attrrow{{$i.$index}}">
                                                                        <td><input required class="form-control" type="text" value="{{$label[$j]}}" name="attributeset[{{$i}}][label][]"></td>
                                                                        <td><input required class="form-control" type="text" value="{{$value[$j]}}" name="attributeset[{{$i}}][value][]"></td>
                                                                        <td><button onclick="removeattrrow({{$i}},{{$index}})" class="btn btn-danger"><i class="fa fa-trash f-s-25"></i></button></td>
                                                                     </tr>
                                                                     <?php }?>
                                                                  </tbody>
                                                               </table>
                                                               <input type="hidden" name="totalattr{{$i}}" id="totalattr{{$i}}" value="{{$j+1}}"/>
                                                               <button type="button" class="btn btn-primary fleft" onclick="addattrrow({{$i}})"><i class="fa fa-plus"></i>Add New Row</button>
                                                            </td>
                                                            <td>
                                                               <button onclick="removerowmain({{$i}})" class="btn btn-danger"><i class="fa fa-trash f-s-25"></i></button>
                                                            </td>
                                                         </tr>
                                                      </tbody>
                                                   </table>
                                                </div>
                                             </div>
                                                 <?php $i++;?>
                                                @endforeach
                                                 <input type="hidden" name="totalrow" id="totalrow" value='<?= $i-1?>' />
                                             @else
                                             <div class="category-wrap" data-id="0" id="mainattr0">
                                                <h3 class="uk-accordion-title uk-background-secondary uk-light uk-padding-small">
                                                   <div class="uk-sortable-handle sort-categories uk-display-inline-block ti-layout-grid4-alt" ></div>
                                                   {{__('messages.New Attributes')}}
                                                </h3>
                                                <div class="uk-accordion-content categories-content " style="margin-top: 0px;padding:0px">
                                                   <table class="table table-striped table-bordered">
                                                      <tbody>
                                                         <tr>
                                                            <td>
                                                               <input type="text" required name="attributeset[0][set]" class="form-control" placeholder="Enter Attribute Set">
                                                               <table class="table table-striped table-bordered cmr1">
                                                                  <thead>
                                                                     <tr>
                                                                        <th>Attribute</th>
                                                                        <th>Value</th>
                                                                        <th></th>
                                                                     </tr>
                                                                  </thead>
                                                                  <tbody id="morerow0">
                                                                     <tr id="attrrow01">
                                                                        <td><input required class="form-control" type="text" name="attributeset[0][label][]"></td>
                                                                        <td><input required class="form-control" type="text" name="attributeset[0][value][]"></td>
                                                                        <td><button onclick="removeattrrow(0,1)" class="btn btn-danger"><i class="fa fa-trash f-s-25"></i></button></td>
                                                                     </tr>
                                                                    
                                                                  </tbody>
                                                               </table>
                                                               <input type="hidden" name="totalattr0" id="totalattr0" value="0"/>
                                                               <button type="button" class="btn btn-primary fleft" onclick="addattrrow(0)"><i class="fa fa-plus"></i>Add New Row</button>
                                                            </td>
                                                            <td>
                                                               <button onclick="removerowmain(0)" class="btn btn-danger"><i class="fa fa-trash f-s-25"></i></button>
                                                            </td>
                                                         </tr>
                                                      </tbody>
                                                   </table>
                                                </div>
                                             </div>
                                              <input type="hidden" name="totalrow" id="totalrow" value='<?= $i?>' />
                                             @endif
                                          </div>
                                       </div>
                                     
                                       <div id="container"></div>
                                       <div class="col-md-12 p-0">
                                          <button type="button" class="btn btn-outline-secondary fleft" onclick="addrow()">{{__('messages.add_new_row')}}</button>
                                          <button type="submit" class="btn btn-primary florig">{{__('messages.save')}}</button>
                                       </div>
                                    </form>
                                 </div>
                                 <div class="tab-pane fade <?= $tab==6?"show active":"" ?> pd10" id="custom-nav-option" role="tabpanel" aria-labelledby="custom-nav-option-tab">
                                    <h3>{{__('messages.option')}}</h3>
                                    <div class="tabdivcatlog"></div>
                                    @include('admin.product.optionadd')
                                 </div>
                                 <div class="tab-pane fade <?= $tab==7?"show active":"" ?> pd10" id="custom-nav-rel_pro" role="tabpanel" aria-labelledby="custom-nav-rel_pro-tab">
                                    <h3>{{__('messages.realted_product')}}</h3>
                                    <div class="tabdivcatlog"></div>
                                   <form action="{{url('admin/saverealtedprice')}}" method="post">                                   
                                       {{csrf_field()}}
                                        <input type="hidden" name="product_id" id="product1" value
                                       ="{{$product_id}}"/>
                                    <div class="col-md-12 savi">
                                       <button type="submit" class="btn btn-primary florig"  onclick="SaveRelatedproduct()">{{__('messages.save')}}</button>
                                    </div>
                                    <input type="hidden" id="rel_pro" value="{{isset($data->related_product)?$data->related_product:""}}" />
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
<script type="text/javascript" src="{{asset('public/js/product.js').'?v=wewe3'}}"></script>
<script>
   CKEDITOR.replace('description');

   
 
</script>
@stop