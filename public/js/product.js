"use strict"
 
    $(function() {
            $("#sortable tbody").sortable({
                cursor: "move",
                placeholder: "sortable-placeholder",
                helper: function(e, tr) {
                    var $originals = tr.children();
                    var $helper = tr.clone();
                    $helper.children().each(function(index) {
                        $(this).width($originals.eq(index).width());
                    });
                    return $helper;
                }
            }).disableSelection();
        });
    function addrow(){
    var totalrow=$("#totalrow").val();    
    var newrow=parseInt(totalrow)+parseInt(1);
    var txt=' <div class="category-wrap" data-id="'+newrow+'" id="mainattr'+newrow+'"><h3 class="uk-accordion-title uk-background-secondary uk-light uk-padding-small"><div class="uk-sortable-handle sort-categories uk-display-inline-block ti-layout-grid4-alt" ></div>New Attributes</h3><div class="uk-accordion-content categories-content " style="margin-top: 0px;padding:0px"><table class="table table-striped table-bordered"><tbody><tr><td><input type="text" name="attributeset['+newrow+'][set]" required class="form-control" placeholder="Enter Attribute Set"><table class="table table-striped table-bordered cmr1"><thead><tr><th>Attribute</th><th>Value</th><th></th></tr></thead><tbody id="morerow'+newrow+'"><tr id="attrrow'+newrow+'1"><td><input required class="form-control" type="text" name="attributeset['+newrow+'][label][]"></td><td><input class="form-control" type="text" required name="attributeset['+newrow+'][value][]"></td><td><button onclick="removeattrrow('+newrow+',1)" class="btn btn-danger"><i class="fa fa-trash f-s-25"></i></button></td></tr></tbody></table><button type="button" class="btn btn-primary fleft" onclick="addattrrow('+newrow+')"><i class="fa fa-plus"></i>Add New Row</button></td><td><button onclick="removerowmain('+newrow+')" class="btn btn-danger"><i class="fa fa-trash f-s-25"></i></button></td></tr></tbody></table></div></div>';
    $("#attributelist").append(txt);
    $("#totalrow").val(newrow);
}   

function addattrrow(val){    
    var lastrow=$("#totalattr"+val).val();
    var newrow=parseInt(lastrow)+parseInt(1);
    var txt='<tr id="attrrow'+val+newrow+'"><td><input class="form-control" type="text" required name="attributeset['+val+'][label][]"></td><td><input class="form-control" type="text" required name="attributeset['+val+'][value][]"></td><td><button onclick="removeattrrow('+val+','+newrow+')" class="btn btn-danger"><i class="fa fa-trash f-s-25"></i></button></td></tr>';
    $("#totalattr"+val).val(newrow);
    $("#morerow"+val).append(txt);
 }

 function removeattrrow(val,row){
      if(row!=1){
          $("#attrrow"+val+row).remove();
      }
      
 }

 function removerowmain(val){
      if(val!=0){
          $("#mainattr"+val).remove();
      }
 }
      
function getsubcategory(val) {
            $('#subcategory').empty();
            $.ajax({
                url: $("#url_path").val()+"/admin/getsubcategory" + "/" + val,
                data: {},
                success: function(data) {
                    var elm = document.getElementById("subcategory"),
                        df = document.createDocumentFragment();
                    var stringify = JSON.parse(data);
                    for (var i = 0; i < stringify.length; i++) {
                        var option = document.createElement('option');
                        option.value = stringify[i]["id"];
                        var name = stringify[i]["name"];
                        option.appendChild(document.createTextNode(name));
                        df.appendChild(option);
                    }
                    elm.appendChild(df);
                    getbrand(stringify[0]["id"]);
                }
            });
        }  
function getbrand(val) {
            $('#brand').empty();
            $.ajax({
                url: $("#url_path").val()+"/admin/getbrandbyid" + "/" + val,
                data: {},
                success: function(data) {
                    var elm = document.getElementById("brand"),
                        df = document.createDocumentFragment();
                    var stringify = JSON.parse(data);
                    for (var i = 0; i < stringify.length; i++) {
                        var option = document.createElement('option');
                        option.value = stringify[i]["id"];
                        var name = stringify[i]["brand_name"];
                        option.appendChild(document.createTextNode(name));
                        df.appendChild(option);
                    }
                    elm.appendChild(df);
                }
            });
        }  
 


        
 function removerow(val) {
            $('#row' + val).remove();
}      
 $(document).ready(function() {
            $('#upload_image').on('change', function(e) {
                    readURL(this,"basic_img");
            });
     });
     function readURL(input,field) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                $("#basic_img1").val(e.target.result);
                $('#'+field).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    function readaddURL(input,field){
         if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $("#additionalimg"+field).val(e.target.result);
                $('#additional_img'+field).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
     $(document).ready(function() {
            $('#add_image').on('change', function(e) {
                var add_total_img=$("#add_total_img").val();
                var txt='<div id="imgid'+add_total_img+'" class="add-img"><input type="hidden" name="add_real_img[]"/><img class="img-thumbnail" id="additional_img'+add_total_img+'" name="arrimg[]" style="width: 150px;height: 150px;" /><div class="add-box"><input type="hidden" id="additionalimg'+add_total_img+'" name="additional_img[]"/><input type="button" id="removeImage1" value="x" class="btn-rmv1" onclick="removeimg('+add_total_img+')"/></div></div>';
                $("#additional_image").append(txt);
                    readaddURL(this,add_total_img);
                    var newtotal=parseInt(add_total_img)+1;
                    $("#add_total_img").val(newtotal);
            });
     });

   
     function removeimg(val){
            $("#imgid"+val).remove();
        }                        
 $(document).ready(function() {
          var related_cat=0;
          var product_id=$("#product_id").val();
          related_cat=$("#subcategory").val();
           if(related_cat==""){
                  related_cat=0;
             }

          var rel_pro=$("#rel_pro").val();
          var strCopy = rel_pro.split(",");
           if(product_id!=0){
             $("#related_product").dataTable().fnDestroy()
         }
           var example=$('#related_product').DataTable({
                processing: true,
                serverSide: true,
                ajax: $("#url_path").val()+'/admin/productlist'+'/'+related_cat+'/'+product_id,
                columns: [{
                    data: 'id',
                    name: 'id',
                }, {
                    data: 'thumbnail',
                    name: 'thumbnail'
                }, {
                    data: 'name',
                    name: 'name'
                }, {
                    data: 'price',
                    name: 'price'
                }, 
                ],
                columnDefs: [{
                    'targets': 0,
                    'searchable': false,
                    'orderable': false,
                    'className': 'dt-body-center',
                    'render': function(data, type, full, meta) {
                        if(strCopy.indexOf(data)==0||strCopy.indexOf(data)==1){
                             return '<input type="checkbox" class="checkbox" checked name="related_id[]" value="' + $('<div/>').text(data).html() + '">';
                        }
                        else{
                             return '<input type="checkbox" class="checkbox" name="related_id[]" value="' + $('<div/>').text(data).html() + '">';
                        }
                       

                    }
                },
                    { targets: 1,
                          render: function(data) {
                          return '<img src="'+data+'" style="height:50px">';
                     }
                 } 
                ],
                order: [1, 'asc']
            });
           
            $.ajax( {
                         url: $("#url_path").val()+"/admin/checktotalproduct",
                         method:"get",
                         success: function( data ) {
                             if(data==1){
                                console.log($("#no_realted_msg").val());  
                             }
                           }
                       });  
        });
  function allselect(val){            
             if($('#select-all').prop('checked')==true){
                     $("input[name='related_id[]']").prop('checked',true);
             }
             else{
                 $("input[name='related_id[]']").prop('checked',false);
             }
         }


      
  
 $(document).ready(function() {
            $('#review_product').DataTable({
                processing: true,
                serverSide: true,
                ajax: $("#url_path").val()+'/admin/reviewdatatable'+"/"+$("#product_id").val(),
                columns: [{
                    data: 'id',
                    name: 'id'
                }, {
                    data: 'pro_name',
                    name: 'pro_name'
                }, {
                    data: 'rev_name',
                    name: 'rev_name'
                }, {
                    data: 'rating',
                    name: 'rating'
                }, {
                    data: 'review',
                    name: 'review'
                }, {
                    data: 'action',
                    name: 'action'
                }],
            });
        });
$(function() {
    
        $('#spe_pri_start, #spe_pri_to').datepicker({
            showOn: "both",
            beforeShow: customRange,
            dateFormat: "M dd,yy",
        });
    
    });
    
    function customRange(input) {
    
        if (input.id == 'spe_pri_to') {
            var minDate = new Date($('#spe_pri_start').val());
            minDate.setDate(minDate.getDate() + 1)
    
            return {
                minDate: minDate
    
            };
        }
    
        return {}
    
    }
       $(function() {
    
        $('#new_pri_start, #new_pri_to').datepicker({
            showOn: "both",
            beforeShow: customRangenew,
            dateFormat: "M dd,yy",
        });
    
    });
    
    function customRangenew(input) {
    
        if (input.id == 'new_pri_to') {
            var minDate = new Date($('#new_pri_start').val());
            minDate.setDate(minDate.getDate() + 1)
    
            return {
                minDate: minDate
    
            };
        }
    
        return {}
    
    }  


             
         var util = UIkit.util;
      
                  util.ready(function () {
      
                      util.on(document.body, 'start moved added removed stop', function (e, sortable, el) {
                          console.log(e.type, sortable, el);
                      });
      
                  }); 
                  function addnewoptionvalue(opval){
          var lastrow=$("#total_option"+opval).val();
          var nextrow=parseInt(lastrow)+1;
          var txt='<div class="questions-row" id="row_'+opval+'_'+nextrow+'"><div class="uk-grid-small uk-margin-small-bottom uk-margin-small-top" uk-grid><div class="uk-width-auto"> <span class="uk-sortable-handle sort-questions uk-margin-small-right" uk-icon="icon: table"></span></div><div class="uk-width-auto" style="width:40%"><input class="form-control" type="text" id="label_'+opval+'_'+nextrow+'" required  name="options['+opval+'][label][]" value=""/></div><div class="uk-width-auto" style="width:40%"><input class="form-control" type="text" id="price_'+opval+'_'+nextrow+'" name="options['+opval+'][price][]" value=""/></div><div class="uk-width-auto"><button type="button" class="btn btn-danger" onclick="removeoptionrow('+opval+','+nextrow+')"><i class="fa fa-trash f-s-25"></i></button></div></div></div>';
          $("#total_option"+opval).val(nextrow);
          $('#option'+opval).append(txt);
      }
      function removeoptionrow(opval,valrow){
          $("#row_"+opval+"_"+valrow).remove();
          
      }
      function addoptionvalue(opval){       
         var txt='<ul class="valul"><li class="td2"></li><li class="td6">'+$("#label").val()+'</li><li class="td6">'+$("#pricemsg").val()+'</li><li class="td2"></li></ul><input type="hidden" name="total_option'+opval+'" id="total_option'+opval+'" value="1"/><div class="uk-sortable " uk-sortable="handle: .sort-questions" id="option'+opval+'"><div class="questions-row" id="row_'+opval+'_1"><div class="uk-grid-small uk-margin-small-bottom uk-margin-small-top" uk-grid><div class="uk-width-auto"> <span class="uk-sortable-handle sort-questions uk-margin-small-right" uk-icon="icon: table"></span></div><div class="uk-width-auto" style="width:40%"><input class="form-control" type="text" id="label_'+opval+'_1" required name="options['+opval+'][label][]" value=""/></div><div class="uk-width-auto" style="width:40%"><input class="form-control" type="text" id="price_'+opval+'_1" name="options['+opval+'][price][]" value=""/></div><div class="uk-width-auto"><button type="button" class="btn btn-danger" onclick="removeoptionrow('+opval+',1)"><i class="fa fa-trash f-s-25"></i></button></div></div></div></div><button type="button" class="btn btn-primary" onclick="addnewoptionvalue('+opval+')">'+$("#add_new_row").val()+'</button>';
         document.getElementById("valuesection"+opval).innerHTML=txt;
      }
      function removeoption(opval){
         $("#mainoption"+opval).remove();
      }
  
      function addglobaloption(){
          var optionid=$("#globaloptiontype").val();
          if(optionid!=""){
             $.ajax({
              url: $("#url_path").val()+"/admin/getoptionvalues" + "/" +optionid,
              data: {},
              success: function(data) {
                 var str=JSON.parse(data);
                 var lastoption=$("#totaloption").val();
                 console.log(str);
                 var nextoption=parseInt(lastoption)+1;
                 var txt='<div class="category-wrap" data-id="'+nextoption+'" id="mainoption'+nextoption+'"><h3 class="uk-accordion-title uk-background-secondary uk-light uk-padding-small"><div class="uk-sortable-handle sort-categories uk-display-inline-block ti-layout-grid4-alt"></div>'+$("#new_option").val()+'</h3><div class="uk-accordion-content categories-content "><ul class="ulinine"><li class="ulliinine"><label for="name" class="control-label mb-1">'+$("#namedis").val()+'</label><input id="option_name_'+nextoption+'" name="options['+nextoption+'][name]" required type="text" class="form-control" aria-required="true" aria-invalid="false" value="'+str["name"]+'"></li><li class="ulliinine"><label for="name" class="control-label mb-1">'+$("#msgtype").val()+'</label><select name="options['+nextoption+'][type]"  required id="option_type_'+nextoption+'" class="form-control" onchange="addoptionvalue('+nextoption+')"><option value="">'+$("#select_type").val()+'</option><option value="1">'+$("#dropdown").val()+'</option><option value="2">'+$("#checkbox").val()+'</option><option value="3">'+$("#radiobutton").val()+'</option><option value="4">'+$("#multiple_select").val()+'</option></select></li><li class="ulliinine3">  <input type="hidden" name="options['+nextoption+'][required]" id="optionreq'+nextoption+'" value="'+str["is_required"]+'"/><input type="checkbox" onclick="changeoptiondata('+nextoption+')" id="is_required_'+nextoption+'" name="optionrequired[]" value="1" class="form-check-input">'+$("#requireddis").val()+'</li><li class="ulliinine3"><button type="button" class="btn btn-danger" onclick="removeoption('+nextoption+')"><i class="fa fa-trash f-s-25"></i></button></li></ul><div id="valuesection'+nextoption+'"></div></div</div>';
                  var optiondata=str["optionlist"];
                   var valsec='<ul class="valul"><li class="td2"></li><li class="td6">'+$("#label").val()+'</li><li class="td6">'+$("#pricemsg").val()+'</li><li class="td2"></li></ul><input type="hidden" name="total_option'+nextoption+'" id="total_option'+nextoption+'" value="'+optiondata+'"/><div class="uk-sortable " uk-sortable="handle: .sort-questions" id="option'+nextoption+'">';
                   
                   for(var i=1;i<=optiondata.length;i++){
                              var index=parseInt(i)-1;
                              if(optiondata[index]["price"]==null){
                                  var price="";
                              }
                              else{
                                  var price=optiondata[index]["price"];
                              }
                          valsec=valsec+'<div class="questions-row" id="row_'+nextoption+'_'+i+'"><div class="uk-grid-small uk-margin-small-bottom uk-margin-small-top" uk-grid><div class="uk-width-auto"> <span class="uk-sortable-handle sort-questions uk-margin-small-right" uk-icon="icon: table"></span></div><div class="uk-width-auto" style="width:40%"><input class="form-control" type="text" id="label_'+nextoption+'_'+i+'" name="options['+nextoption+'][label][]" value="'+optiondata[index]["label"]+'"/></div><div class="uk-width-auto" style="width:40%"><input class="form-control" type="text" id="price_'+nextoption+'_1" name="options['+nextoption+'][price][]" value="'+price+'"/></div><div class="uk-width-auto"><button type="button" class="btn btn-danger" onclick="removeoptionrow('+nextoption+','+i+')"><i class="fa fa-trash f-s-25"></i></button></div></div></div>';
                          $("#option_type_"+nextoption+'_'+i).val(optiondata[index]["price_type"]);
                   }
                   valsec=valsec+'</div><div><button type="button" class="btn btn-primary" onclick="addnewoptionvalue('+nextoption+')">'+$("#add_new_row").val()+'</button></div>';
                 $("#totaloption").val(nextoption);
                 $("#optionlist").append(txt);
                 $("#option_type_"+nextoption).val(str["type"]);              
                 if(str["is_required"]=="1"){
                    $("#is_required_"+nextoption).attr('checked', 'checked');
                 }
                 $("#valuesection"+nextoption).append(valsec);
              }
          });
          }
          else{
            alert($("#ple_sel_option").val());
          }
         
          
      }
  
      function addoption(){
          var lastoption=$("#totaloption").val();
          var nextoption=parseInt(lastoption)+1;
          var txt='<div class="category-wrap" data-id="'+nextoption+'" id="mainoption'+nextoption+'"><h3 class="uk-accordion-title uk-background-secondary uk-light uk-padding-small"><div class="uk-sortable-handle sort-categories uk-display-inline-block ti-layout-grid4-alt" ></div>'+$("#new_option").val()+'</h3><div class="uk-accordion-content categories-content "><ul class="ulinine"><li class="ulliinine"><label for="name" class="control-label mb-1">'+$("#namedis").val()+'</label><input id="option_name_'+nextoption+'" name="options['+nextoption+'][name]" required type="text" class="form-control" aria-required="true" aria-invalid="false"></li><li class="ulliinine"><label for="name" class="control-label mb-1">'+$("#msgtype").val()+'</label><select name="options['+nextoption+'][type]" required id="option_type_'+nextoption+'" class="form-control" onchange="addoptionvalue('+nextoption+')"><option value="">'+$("#select_type").val()+'</option><option value="1">'+$("#dropdown").val()+'</option><option value="2">'+$("#checkbox").val()+'</option><option value="3">'+$("#radiobutton").val()+'</option><option value="4">'+$("#multiple_select").val()+'</option></select></li><li class="ulliinine3"> <input type="hidden" name="options['+nextoption+'][required]" id="optionreq'+nextoption+'" value="0"/><input type="checkbox"  onclick="changeoptiondata('+nextoption+')"  id="is_required_'+nextoption+'"  value="1" class="form-check-input">'+$("#requireddis").val()+'</li><li class="ulliinine3"><button type="button" class="btn btn-danger" onclick="removeoption('+nextoption+')"><i class="fa fa-trash f-s-25"></i></button></li></ul><div id="valuesection'+nextoption+'"></div></div</div>';
          $("#totaloption").val(nextoption);
          $("#optionlist").append(txt);
      }
   

    $(document).ready(function() {
         var product_id=$("#product_id").val();
         if(product_id!=0){
             $("#review_product").dataTable().fnDestroy()
         }
        
            $('#review_product').DataTable({
                processing: true,
                serverSide: true,
                ajax: $("#url_path").val()+'/admin/reviewdatatable'+"/"+"abc",
                columns: [{
                    data: 'id',
                    name: 'id'
                }, {
                    data: 'pro_name',
                    name: 'pro_name'
                }, {
                    data: 'rev_name',
                    name: 'rev_name'
                }, {
                    data: 'rating',
                    name: 'rating'
                }, {
                    data: 'review',
                    name: 'review'
                }, {
                    data: 'action',
                    name: 'action'
                }],
            });
        });                                                                                          