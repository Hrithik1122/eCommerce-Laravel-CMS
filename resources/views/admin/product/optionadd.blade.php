
<form action="{{url('admin/saveproductoption')}}" method="post">
  {{csrf_field()}}
  <input type="hidden" name="product_id" id="product1" value="{{$product_id}}"/>
   <div class="row">
      <div class="col-md-12">
         <div class="categories-accordion mrg30" uk-accordion="targets: > div > .category-wrap">
            <div class="categories-sort-wrap uk-sortable uk-margin-top" uk-sortable="handle: .sort-categories" id="optionlist">
              <?php $i=0;?>
              @if(!empty($data->optionls))
                 <?php    $i=0;
                             $name=array();
                             $index=1;
                            if(!empty($data->optionls)){
                                $name=explode(",",$data->optionls->name);
                                $type=explode(",",$data->optionls->type);
                                $isrequired=explode(",", $data->optionls->is_required);
                                $label=explode("#",$data->optionls->label);
                                $price=explode("#",$data->optionls->price);
                                $price_type=explode("#",$data->optionls->price_type);
                            }

                    ?>
                        @foreach($name as $po)
                        <?php $index=$i;?>
                            <div class="category-wrap" data-id="{{$index}}" id="mainoption{{$index}}">
                                <h3 class="uk-accordion-title uk-background-secondary uk-light uk-padding-small">
                <div class="uk-sortable-handle sort-categories uk-display-inline-block ti-layout-grid4-alt" ></div> {{__('messages.new_option')}}
            </h3>
                                <div class="uk-accordion-content categories-content ">
                                    <ul class="ulinine edit-p-list-u">
                                        <li class="ulliinine">
                                            <label for="name" class="control-label mb-1">{{__('messages.name')}}</label>
                                            <input id="option_name_{{$index}}" name="options[{{$i}}][name]" type="text" class="form-control" aria-required="true" aria-invalid="false" value="{{$name[$i]}}">
                                        </li>
                                        <li class="ulliinine">
                                            <label for="name" class="control-label mb-1">{{__('messages.type')}}</label>
                                            <select name="options[{{$i}}][type]" required id="option_type_{{$index}}" class="form-control" onchange="addoptionvalue('{{$index}}')">
                                                <option value="">{{__('messages.select')}} {{__('messages.type')}}</option>
                                                <option value="1" <?=$type[$i]==1 ? ' selected="selected"' : '';?>>{{__('messages.dropdown')}}</option>
                                                <option value="2" <?=$type[$i]==2 ? ' selected="selected"' : '';?>>{{__('messages.checkbox')}}</option>
                                                <option value="3" <?=$type[$i]==3 ? ' selected="selected"' : '';?>>{{__('messages.radiobutton')}}</option>
                                                <option value="4" <?=$type[$i]==4 ? ' selected="selected"' : '';?>>{{__('messages.multiple_select')}}</option>
                                            </select>
                                        </li>
                                        <li class="ulliinine3">
                                           <input type="hidden" name="options[{{$i}}][required]" id="optionreq{{$index}}" value="{{$isrequired[$i]}}"/>
                                            <input type="checkbox" id="is_required_{{$index}}" onclick="changeoptiondata('{{$index}}')" value="1" class="form-check-input" <?=$isrequired[$i]==1 ? ' checked="checked"' : '';?>>{{__('messages.required')}}</li>
                                        <li class="ulliinine3">
                                            <button type="button" class="btn btn-danger" onclick="removeoption('{{$index}}')"><i class="fa fa-trash f-s-25"></i>
                                            </button>
                                        </li>
                                    </ul>
                                    <?php $lb=explode(",",$label[$i]);
                                          $pr=explode(",",$price[$i]);
                                          $j=0;
                                          $indexj=$j+1;
                                     ?>
                                        <div id="valuesection{{$index}}">
                                            <ul class="valul">
                                                <li class="td2"></li>
                                                <li class="td6">{{__("messages.label")}}</li>
                                                <li class="td6">{{__("messages.price")}}</li>
                                                <li class="td2"></li>
                                            </ul>

                                            <div class="uk-sortable " uk-sortable="handle: .sort-questions" id="option{{$index}}">
                                                @foreach($lb as $l)
                                                <div class="questions-row" id="row_{{$index}}_{{$indexj}}">
                                                    <div class="edit-p-small uk-grid-small uk-margin-small-bottom uk-margin-small-top" uk-grid>
                                                        <div class="edit-p-width-lc-2 edit-p-padding ">
                                                            <span class="edit-p-lcm uk-sortable-handle sort-questions uk-margin-small-right" uk-icon="icon: table"></span>
                                                        </div>
                                                        <div class="edit-p-width-lco edit-p-padding " style="width:40%">
                                                            <input class="form-control" type="text" id="label_{{$index}}_{{$indexj}}" name="options[{{$index}}][label][]" value="{{$l}}" />
                                                        </div>
                                                        <div class="edit-p-width-lco edit-p-padding " style="width:40%">
                                                            <input class="form-control" type="text" id="price_{{$index}}_{{$indexj}}" name="options[{{$index}}][price][]" value="<?=isset($pr[$j])?$pr[$j]:0; ?>" />
                                                        </div>
                                                       
                                                        <div class="edi-p-width-lc edit-p-padding">
                                                            <button type="button" class="btn btn-danger" onclick="removeoptionrow('{{$index}}','{{$indexj}}')"><i class="fa fa-trash f-s-25"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php $j++;$indexj++;?>
                                                    @endforeach
                                            </div>
                                            
                                            <button type="button" class="edit-p-lcb btn btn-primary" onclick="addnewoptionvalue('{{$index}}')">{{__("messages.add_new_row")}}</button>
                                            <input type="hidden" name="total_option{{$index}}" id="total_option{{$index}}" value="{{$index}}" />
                                        </div>
                                </div>
                            </div>
                            <?php $i++;?>
                            @endforeach
              @else
               <div class="category-wrap" data-id="{{$i}}" id="mainoption{{$i}}">
                  <h3 class="uk-accordion-title uk-background-secondary uk-light uk-padding-small">
                     <div class="uk-sortable-handle sort-categories uk-display-inline-block ti-layout-grid4-alt" ></div>
                     {{__('messages.new_option')}}
                  </h3>
                  <div class="uk-accordion-content categories-content ">
                       <div class="edit-p-list-u">
                     <ul class="ulinine">
                        <li class="ulliinine">
                           <label for="name" class="control-label mb-1">{{__('messages.name')}}</label>
                           <input id="option_name_{{$i}}" required name="options[{{$i}}][name]" type="text" class="form-control" aria-required="true" aria-invalid="false">
                        </li>
                        <li class="ulliinine">
                           <label for="name" class="control-label mb-1">{{__('messages.type')}}</label>
                           <select name="options[{{$i}}][type]" required id="options{{$i}}" class="form-control" onchange="addoptionvalue({{$i}})">
                              <option value="">{{__('messages.select')}} {{__('messages.type')}}</option>
                              <option value="1">{{__('messages.dropdown')}}</option>
                              <option value="2">{{__('messages.checkbox')}}</option>
                              <option value="3">{{__('messages.radiobutton')}}</option>
                              <option value="4">{{__('messages.multiple_select')}}</option>
                           </select>
                        </li>
                        <input type="hidden" name="options[{{$i}}][required]" id="optionreq{{$i}}" value="0"/>
                        <li class="ulliinine3">
                           <input type="checkbox" id="is_required_{{$i}}"  onclick="changeoptiondata('{{$i}}')"  value="1" class="form-check-input">{{__('messages.required')}}
                        </li>
                        <li class="ulliinine3">
                           <button type="button" class="btn btn-danger" onclick="removeoption({{$i}})"><i class="fa fa-trash f-s-25"></i>
                           </button>
                        </li>
                     </ul>
                  </div>
                     <div id="valuesection{{$i}}"></div>
                  </div>
               </div>
              @endif
            </div>
         </div>
         <input type="hidden" name="totaloption" id="totaloption" value="{{$i}}" />
          <div class="edit-p-blc">
         <div class="col-md-12 p-0 orderdiv">
            <div class="row">
               <div class="col-md-5 fleft" >
                  <button type="button" class="btn btn-outline-secondary fleft" onclick="addoption()" >{{__('messages.add_new_option')}}</button>
               </div>
               <div class="col-md-7 florig">
                  <select name="globaloptiontype" id="globaloptiontype" class="form-control col-md-6 fleft">
                     <option value="">{{__('messages.select')}} {{__('messages.option')}}</option>
                     @foreach($optionvalues as $opval)
                     <option value="{{$opval->id}}">{{$opval->name}}</option>
                     @endforeach
                  </select>
                  <button type="button" class="btn btn-primary col-md-6 fleft"  onclick="addglobaloption()">{{__('messages.add_global_option')}}</button>
               </div>
            </div>
         </div>
      </div>
       @if(Session::get("is_demo")=='1')
                                        <button type="button" onclick="return alert('This function is currently disable as it is only a demo website, in your admin it will work perfect')" class="btn btn-primary fleft">
                                               {{__('messages.save')}}
                                        </button>
                                     @else
                                      <button type="submit" class="btn btn-primary fleft">{{__('messages.save')}}</button>
                                     @endif 
         
      </div>
   </div>
</form>
<input type="hidden" id="fixed" value='{{__("messages.Fixed")}}'>
<input type="hidden" id="percentage" value="{{__('messages.percentage')}}">
<input type="hidden" id="label" value='{{__("messages.label")}}'>
<input type="hidden" id="pricemsg" value='{{__("messages.price")}}'>
<input type="hidden" id="price_type" value='{{__("messages.price_type")}}'>
<input type="hidden" id="add_new_row" value='{{__("messages.add_new_row")}}'>
<input type="hidden" id="new_option" value='{{__("messages.new_option")}}'>
<input type="hidden" id="namedis" value='{{__("messages.name")}}'>
<input type="hidden" id="select_type" value='{{__("messages.select")}} {{__("messages.type")}}'>
<input type="hidden" id="dropdown" value='{{__("messages.dropdown")}}'>
<input type="hidden" id="checkbox" value='{{__("messages.checkbox")}}'>
<input type="hidden" id="radiobutton" value='{{__("messages.radiobutton")}}'>
<input type="hidden" id="multiple_select" value='{{__("messages.multiple_select")}}'>
<input type="hidden" id="requireddis" value='{{__("messages.required")}}'>
<input type="hidden" id="ple_sel_option" value="{{__('messages_error_success.ple_sel_option')}}">