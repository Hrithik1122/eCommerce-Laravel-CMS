"use strict"
 $(document).ready(function() {
         var parent_id = $("#parent_id").val();
        $('#brandTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: $("#url_path").val()+'/admin/branddatatable' + "/" + parent_id,
            columns: [{
                data: 'id',
                name: 'id'
            }, {
                data: 'name',
                name: 'name'
            }, {
                data: 'action',
                name: 'action'
            }],
             order:[[0,"DESC"]]
        });
    });
  function isNumberKey(evt)
      {
         var charCode = (evt.which) ? evt.which : event.keyCode
         if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;

         return true;
      }
      
      $(document).ready(function() {
           var _URL = window.URL || window.webkitURL;
            $('#upload_image_logo').on('change', function(e) {
                 var file, img;
        if ((file = this.files[0])) {
            img = new Image();
            var flag=0;
            img.onload = function() {
                
                if(this.width == 128 && this.height == 61)
                {
                     flag=1;

                }
                else
                {
                    alert($("#image_invaild").val());
                    window.location.reload();
                }
            };
              img.src = _URL.createObjectURL(file);
            readURL(this,"img_logo");
        }


                   
            });
     });
     function readURL(input,field) {  
        
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                $("#logo").val(e.target.result);
                $('#'+field).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
 $(document).ready(function() {
        $('#Complaintable').DataTable({
            processing: true,
            serverSide: true,
            ajax: $("#url_path").val()+'/admin/complaindatatable',
            columns: [{
                data: 'id',
                name: 'id'
            }, {
                data: 'email',
                name: 'email'
            },
            {
                data: 'description',
                name: 'description'
            },
            {
                data: 'complain_type',
                name: 'complain_type'
            },
            {
                data: 'action',
                name: 'action'
            }
            ],
             order:[[0,"DESC"]]
        });
    });
  function delete_record(url) {
        if (confirm($("#delete_data").val())) {
            if($("#demo_lang").val()==1){
                alert('This function is currently disable as it is only a demo website, in your admin it will work perfect');
            }
            else{
                 window.location.href =url;
            }
          
        } else {
            window.location.reload();
        }
    }
  function editbrand(id) {
        $.ajax({
            url: $("#url_path").val()+"/admin/getbrandbyname" + "/" + id,
            data: {},
            success: function(data) {
                $('#id').val(id);
                $("#edit_brand").val(data);
            }
        });
    }
$(document).ready(function() {
        $('#categoryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: $("#url_path").val()+'/admin/categorydatatable',
            columns: [{
                data: 'id',
                name: 'id'
            }, {
                data: 'name',
                name: 'name'
            }, {
                data: 'image',
                name: 'image'
            }, {
                data: 'action',
                name: 'action'
            }],
                columnDefs: [{
                    targets: 2,
                    render: function(data) {
                        return '<img src="' + data + '" style="height:50px">';
                    }
                }],
        });
    });
$(document).ready(function() {
        $('#notificationTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: $("#url_path").val()+'/admin/notificationTable',
            columns: [{
                data: 'id',
                name: 'id'
            }, {
                data: 'msg',
                name: 'msg'
            }],
        });
    });


 function editcategory(id) {
        $.ajax({
            url: $("#url_path").val()+"/admin/getcategorybyid" + "/" + id,
            data: {},
            success: function(data) {
                $('#id').val(id);
                console.log(data);
                $("#edit_category_name").val(data.name);
                $("#edit_image").attr("src",$("#url_path").val()+"/public/upload/category/image"+"/"+data.image);
            }
        });
}
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
$(function() {
        $('.tree li:has(ul)').addClass('parent_li').find(' > span').attr('title', 'Collapse this branch');
        $('.tree li.parent_li > span').on('click', function(e) {
            var children = $(this).parent('li.parent_li').find(' > ul > li');
            if (children.is(":visible")) {
                children.hide('fast');
                $(this).attr('title', 'Expand this branch').find(' > i').addClass('fa-plus-square').removeClass('fa-minus-square');
            } else {
                children.show('fast');
                $(this).attr('title', 'Collapse this branch').find(' > i').addClass('fa-minus-square').removeClass('fa-plus-square');
            }
            e.stopPropagation();
        });
});
$(document).ready(function() {
        var parent_id = $("#parent_id").val();
        $('#subCategoryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: $("#url_path").val()+'/admin/subcategorydatatable' + "/" + parent_id,
            columns: [{
                data: 'id',
                name: 'id'
            }, {
                data: 'name',
                name: 'name'
            }, {
                data: 'action',
                name: 'action'
            }],
        });
    });

 $.ajaxSetup({
                headers: {

                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                }
            });
            $(function() {

                $('#start_date, #end_date').datepicker({
                    showOn: "both",
                    beforeShow: customRange,
                    dateFormat: "MM dd,yy",
                });

            });

            function customRange(input) {

                if (input.id == 'end_date') {
                    var minDate = new Date($('#start_date').val());
                    minDate.setDate(minDate.getDate() + 1)

                    return {
                        minDate: minDate

                    };
                }
                return {}
            }
            function Savecoupon() {
                var coupon_id = $("#coupon_id").val();
                var name = $("#name").val();
                var code = $("#code").val();
                var discount_type = $("#discount_type").val();
                var value = $("#value").val();
                var start_date = $("#start_date").val();
                var end_date = $("#end_date").val();
                var status = 0;
                var free_shipping = 0;
                if (document.getElementById("status").checked == true) {
                    status = 1;
                }
                if (document.getElementById("free_shipping").checked == true) {
                    free_shipping = 1;
                }
                if (name != "" && code != "") {
                    $.ajax({
                        url: $("#url_path").val()+"/admin/savecoupon",
                        method: "post",
                        data: {
                            id: coupon_id,
                            name: name,
                            code: code,
                            discount_type: discount_type,
                            value: value,
                            start_date: start_date,
                            end_date: end_date,
                            free_shipping: free_shipping,
                            status: status
                        },
                        success: function(data) {
                            if (data != "Code") {
                                $("#coupon_id").val(data);
                                alert($("#data_save_success").val());
                                $("#home").removeClass('in show active');
                                $('a[href="#home"]').removeClass('active');
                                $('a[href="#profile"]').addClass('active');
                                $("#profile").addClass('in show active');
                            } else {
                                alert($("#coupon_code_use").val());
                            }
                        }
                    });
                } else {
                    alert($("#something").val());
                }
            }
 function SaveCouponstep2() {
                var coupon_id = $("#coupon_id").val();
                var minmum_send = $("#minmum_send").val();
                var maximum_spend = $("#maximum_spend").val();
                var product = $("#product").val();
                var category = $("#category").val();
                  if(document.getElementById("coupon_on").checked == true){
                     var coupon_on='0';
                  }else{
                     var coupon_on='1';
                  }
                if (coupon_id == "" || coupon_id == 0) {
                    alert($("#generalmsg").val());
                } else {
                    $.ajax({
                        url: $("#url_path").val()+"/admin/savecouponsecondstep",
                        method: "post",
                        data: {
                            id: coupon_id,
                            minmum_send: minmum_send,
                            maximum_spend: maximum_spend,
                            product: product,
                            category: category,
                            coupon_on:coupon_on
                        },
                        success: function(data) {
                            $("#coupon_id").val(data);
                            alert($("#data_save_success").val());
                            $("#profile").removeClass('in show active');
                            $('a[href="#profile"]').removeClass('active');
                            $('a[href="#coupon"]').addClass('active');
                            $("#coupon").addClass('in show active');
                        }
                    });
                }
            }    
 function Savecouponstep3() {
                var coupon_id = $("#coupon_id").val();
                var per_coupon = $("#per_coupon").val();
                var per_customer = $("#per_customer").val();

                if (coupon_id == "" || coupon_id == 0) {
                    alert($("#generalmsg").val());
                } else {
                    if(parseInt(per_customer)>parseInt(per_coupon)){
                        alert($("#error_coupon_limit").val());
                        $("#per_customer").val("");
                    }
                    else{
                        $.ajax({
                        url: $("#url_path").val()+"/admin/savecouponstepthree",
                        method: "post",
                        data: {
                            id: coupon_id,
                            per_coupon: per_coupon,
                            per_customer: per_customer
                        },
                            success: function(data) {
                                $("#coupon_id").val(data);
                                alert($("#data_save_success").val());
                                window.location.href = $("#url_path").val()+"/admin/coupon";
                            }
                       }); 
                    }
                   
                }
            }   
var element = jQuery("#product");
            $.ajax({
                url: $("#url_path").val()+"/admin/getallproduct",
                data: {},
                success: function(data) {
                    var stringify = JSON.parse(data);
                    $("#product").selectize({
                        plugins: ['remove_button'],
                        persist: false,
                        maxItems: null,
                        valueField: 'id',
                        labelField: 'name',
                        searchField: ['name'],
                        options: stringify,
                        render: {
                            item: function(item, escape) {
                                return '<div>' +
                                    (item.name ? '<span class="name">' + escape(item.name) + '</span>' : '') +
                                    '</div>';
                            },
                            option: function(item, escape) {
                                var label = item.name || item.id;
                                return '<div>' +
                                    '<span class="label">' + escape(label) + '</span>' +
                                    '</div>';
                            }
                        },
                        createFilter: function(input) {
                            var match, regex;
                            regex = new RegExp('^' + REGEX_EMAIL + '$', 'i');
                            match = input.match(regex);
                            if (match) return !this.options.hasOwnProperty(match[0]);
                            regex = new RegExp('^([^<]*)\<' + REGEX_EMAIL + '\>$', 'i');
                            match = input.match(regex);
                            if (match) return !this.options.hasOwnProperty(match[2]);
                            return false;
                        },
                    });
                }
            });
           
            var element = jQuery("#category");
            $.ajax({
                url:$("#url_path").val()+"/admin/getallsubcategory",
                data: {},
                success: function(data) {
                    var stringify = JSON.parse(data);
                    $("#category").selectize({
                        plugins: ['remove_button'],
                        persist: false,
                        maxItems: null,
                        valueField: 'id',
                        labelField: 'name',
                        searchField: ['name'],
                        options: stringify,
                        render: {
                            item: function(item, escape) {
                                return '<div>' +
                                    (item.name ? '<span class="name">' + escape(item.name) + '</span>' : '') +
                                    '</div>';
                            },
                            option: function(item, escape) {
                                var label = item.name || item.id;
                                return '<div>' +
                                    '<span class="label">' + escape(label) + '</span>' +
                                    '</div>';
                            }
                        },
                        createFilter: function(input) {
                            var match, regex;
                            regex = new RegExp('^' + REGEX_EMAIL + '$', 'i');
                            match = input.match(regex);
                            if (match) return !this.options.hasOwnProperty(match[0]);
                            regex = new RegExp('^([^<]*)\<' + REGEX_EMAIL + '\>$', 'i');
                            match = input.match(regex);
                            if (match) return !this.options.hasOwnProperty(match[2]);
                            return false;
                        },
                    });
                }
            });
          function changeproductdiv(){
          
              if(document.getElementById("coupon_on").checked == true){
                document.getElementById("productcoupon").style.display="block";
                document.getElementById("categorycoupon").style.display="none";
              }else{
                document.getElementById("productcoupon").style.display="none";
                document.getElementById("categorycoupon").style.display="block";
              }
          } 
            $(document).ready(function() {
            $('#couponmainTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: $("#url_path").val()+'/admin/coupondatatable',
                columns: [{
                    data: 'id',
                    name: 'id'
                }, {
                    data: 'name',
                    name: 'name'
                }, {
                    data: 'code',
                    name: 'code'
                }, {
                    data: 'date',
                    name: 'date'
                }, {
                    data: 'value',
                    name: 'value'
                }, {
                    data: 'action',
                    name: 'action'
                }],

            });
        });
        function addcoupon() {
            window.location.href = $("#url_path").val()+"/admin/addcoupon";
        }   
$(document).ready(function() {
            $('#featuretable').DataTable({
                processing: true,
                serverSide: true,
                ajax: $("#url_path").val()+'/admin/featureproductdatatable',
                columns: [{
                    data: 'id',
                    name: 'id'
                }, {
                    data: 'image',
                    name: 'image'
                }, {
                    data: 'product',
                    name: 'product'
                }, {
                    data: 'action',
                    name: 'action'
                }],
                columnDefs: [{
                    targets: 1,
                    render: function(data) {
                        return '<img src="' + data + '" style="height:50px">';
                    }
                }],
                 "order": [[ 0, "desc" ]]
            });
        });
 $(document).ready(function() {
            $('#taxTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: $("#url_path").val()+'/admin/taxesdatatable',
                columns: [{
                    data: 'id',
                    name: 'id'
                }, {
                    data: 'tax_class',
                    name: 'tax_class'
                }, {
                    data: 'rate',
                    name: 'rate'
                }, {
                    data: 'action',
                    name: 'action'
                }, ],
                 order:[[0,"DESC"]]
            });
        });
  function addtax() {
            window.location.href = $("#url_path").val()+"/admin/addtaxes";
        }
 $(document).ready(function() {
            $('#transalteTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: $("#url_path").val()+'/admin/translationdatatable',
                columns: [{
                    data: 'id',
                    name: 'id'
                }, {
                    data: 'key',
                    name: 'key'
                }, {
                    data: 'value',
                    name: 'value'
                }, ],
                columnDefs: [{
                    targets: 2,
                    render: function(data) {

                        var str = data.split(",");
                        return '<p id="editrow' + str[1] + '"><label onclick="chnagevalue(' + str[1] + ')">' + str[0] + '</label></p>';
                    }
                }],
                 order:[[0,"DESC"]]
            });
        });  
function chnagevalue(val) {
               
                        var totalrow = $("#totalrow").val();
                        for (var i = 1; i <= totalrow; i++) {
                            if ($("#editval" + i).length != 0) {
                                
                                $.ajax({
                                    url: $("#url_path").val()+"/admin/getdatatranslation" + "/" + i,
                                    data: {},
                                    success: function(data) {
                                        var str = JSON.parse(data);
                                        var txt = '<label onclick="chnagevalue(' + str.id + ')">' + str.value + '</label>';
                                        document.getElementById("editrow" + str.id).innerHTML = txt;
                                    }
                                });
                            }

                        }
                        $.ajax({
                            url: $("#url_path").val()+"/admin/getdatatranslation" + "/" + val,
                            data: {},
                            success: function(data) {
                                var str = JSON.parse(data);
                                var txt = '<div class="col-md-12" style="padding-left:0px"><div class="col-md-9" style="padding-left:0px"><input type="hidden" name="id" id="id" value="' + str.id + '"/><input type="text" name="editval" id="editval' + val + '" value="' + str.value + '" class="form-control"/></div><div class="col-md-3"><div class="editable-buttons"><button type="button" onclick="edittext(' + str.id + ')" style="margin-right:5px" class="btn btn-primary btn-sm editable-submit"><i class="fa fa-edit"></i></button><button type="button" style="background: #007bff;color: white;" onclick="closeedit()" class="btn btn-default btn-sm editable-cancel"><i class="fa fa-close"></i></button></div</div></div>';
                                document.getElementById("editrow" + val).innerHTML = txt;
                            }
                        });

               
            
        }

        function closeedit() {
            $("#transalteTable").dataTable().fnDestroy();
            $('#transalteTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: $("#url_path").val()+'/admin/translationdatatable',
                columns: [{
                    data: 'id',
                    name: 'id'
                }, {
                    data: 'key',
                    name: 'key'
                }, {
                    data: 'value',
                    name: 'value'
                }, ],
                columnDefs: [{
                    targets: 2,
                    render: function(data) {
                        var str = data.split(",");
                        return '<p id="editrow' + str[1] + '"><label onclick="chnagevalue(' + str[1] + ')">' + str[0] + '</label></p>';
                    }
                }],
                 order:[[0,"DESC"]]
            });
        }

        function edittext(val) {
                 if($("#demo_lang").val()=='0'){
                            var id = $("#id").val();
                            var value = $("#editval" + val).val();
                            $.ajax({
                                url: $("#url_path").val()+"/admin/updatetranslation",
                                method: "post",
                                data: {
                                    id: id,
                                    value: value
                                },
                                success: function(data) {
                                    closeedit();
                                }
                            });
                 }
                else{
                       alert('This function is currently disable as it is only a demo website, in your admin it will work perfect'); 
                        closeedit();    
                }          
        }      
$('#fixed_form').keypress(function(event) {
        var keycode = event.which;
        if (!(event.shiftKey == false && (keycode == 46 || keycode == 8 || keycode == 37 || keycode == 39 || (keycode >= 48 && keycode <= 57)))) {
            event.preventDefault();
        }
    });
    $('#fixed_to').keypress(function(event) {
        var keycode = event.which;
        if (!(event.shiftKey == false && (keycode == 46 || keycode == 8 || keycode == 37 || keycode == 39 || (keycode >= 48 && keycode <= 57)))) {
            event.preventDefault();
        }
    });    
function editdeal(deal_id, offer_id) {
            console.log(offer_id);
            document.getElementById("editdeal").style.display = "block";
            $('#offer_id').empty();
            $.ajax({
                url: $("#url_path").val()+"/admin/getofferfordeal" + "/" + deal_id,
                success: function(data) {
                    var elm = document.getElementById("offer_id"),
                        df = document.createDocumentFragment();
                    var stringify = JSON.parse(data);
                    for (var i = 0; i < stringify.length; i++) {
                        var option = document.createElement('option');
                        option.value = stringify[i]["id"];
                        var name = stringify[i]["title"];
                        option.appendChild(document.createTextNode(name));
                        df.appendChild(option);
                    }
                    elm.appendChild(df);
                    $('#offer_id').val(offer_id);
                    $('#id').val(deal_id);
                }
            });
        }
 $(document).ready(function() {
            $('#dealsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: $("#url_path").val()+'/admin/dealdatatable',
                columns: [{
                    data: 'id',
                    name: 'id'
                }, {
                    data: 'banner',
                    name: 'banner'
                }, {
                    data: 'title',
                    name: 'title'
                }, {
                    data: 'date',
                    name: 'date'
                }, {
                    data: 'deal',
                    name: 'deal'
                }, {
                    data: 'action',
                    name: 'action'
                }],
                columnDefs: [{
                    targets: 1,
                    render: function(data) {
                         if(data!=null){
                             return '<img src="' + data + '" style="height:50px">';
                         }else{
                                return "";
                         }
                       
                    }
                }, {
                    targets: 2,
                    render: function(data) {
                        if(data!=null){
                            console.log(data);
                             var str = data.split(",");
                             var url = $("#url_path").val()+'/admin/editoffer' + "/" + str[1];
                             return '<a href=' + url + ' style="text-decoration: underline;color: blue;">' + str[0] + '</a>';
                        }   
                        else{
                            return "";
                        }                    
                    }
                }],
                 order:[[0,"DESC"]]
            });
        });

        function changedeals() {
            var offer_id = $('#offer_id').val();
            var deal_id = $('#id').val();
            console.log(offer_id);
            if(offer_id!=null&&offer_id!="0"){
                $.ajax({
                url: $("#url_path").val()+"/admin/updatedeal" + "/" + deal_id + "/" + offer_id,
                success: function(data) {
                    if (data = "done") {
                        $("#dealsTable").dataTable().fnDestroy()
                        $('#dealsTable').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: $("#url_path").val()+'/admin/dealdatatable',
                            columns: [{
                                data: 'id',
                                name: 'id'
                            }, {
                                data: 'banner',
                                name: 'banner'
                            }, {
                                data: 'title',
                                name: 'title'
                            }, {
                                data: 'date',
                                name: 'date'
                            }, {
                                data: 'deal',
                                name: 'deal'
                            }, {
                                data: 'action',
                                name: 'action'
                            }],
                            columnDefs: [{
                    targets: 1,
                    render: function(data) {
                         if(data!=null){
                             return '<img src="' + data + '" style="height:50px">';
                         }else{
                                return "";
                         }
                       
                    }
                }, {
                    targets: 2,
                    render: function(data) {
                        if(data!=null){
                            console.log(data);
                             var str = data.split(",");
                             var url = $("#url_path").val()+'/admin/editoffer/' + "/" + str[1];
                             return '<a href=' + url + ' style="text-decoration: underline;color: blue;">' + str[0] + '</a>';
                        }   
                        else{
                            return "";
                        }                    
                    }
                }],
                 order:[[0,"DESC"]]
                        });
                        document.getElementById("editdeal").style.display = "none";

                    }

                }
              });
            }
            else{
                  alert($("#offer_deal_lang").val());
            }
            
        }    
 function addoffer(val) {
            window.location.href = $("#url_path").val()+"/admin/addoffersection" + "/" + val;
        }
        $(document).ready(function() {
            $('#bigofferTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: $("#url_path").val()+'/admin/offerdatatable' + "/" + 1,
                columns: [{
                    data: 'id',
                    name: 'id'
                }, {
                    data: 'banner',
                    name: 'banner'
                }, {
                    data: 'title',
                    name: 'title'
                }, {
                    data: 'date',
                    name: 'date'
                }, {
                    data: 'offer_on',
                    name: 'offer_on'
                }, {
                    data: 'offer',
                    name: 'offer'
                }, {
                    data: 'price',
                    name: 'price'
                }, {
                    data: 'action',
                    name: 'action'
                }],
                columnDefs: [{
                    targets: 1,
                    render: function(data) {
                        return '<img src="' + data + '" style="height:50px">';
                    }
                }],
                 order:[[0,"DESC"]]
            });
        });
  $('#fixed').keypress(function(event) {
        var keycode = event.which;
        if (!(event.shiftKey == false && (keycode == 46 || keycode == 8 || keycode == 37 || keycode == 39 || (keycode >= 48 && keycode <= 57)))) {
            event.preventDefault();
        }
    });
    $('#offer_price').keypress(function(event) {
        var keycode = event.which;
        if (!(event.shiftKey == false && (keycode == 46 || keycode == 8 || keycode == 37 || keycode == 39 || (keycode >= 48 && keycode <= 57)))) {
            event.preventDefault();
        }
    });        

    function getproductprice(val){
         $.ajax({
                url: $("#url_path").val()+"/admin/getproductprice" + "/" + val,
                success: function(data) {
                   var stringify = JSON.parse(data);
                   $("#mrp").val(stringify.MRP);
                   $("#selling_price").val(stringify.price);
                }
            });
    }
    function changeofferdiv(val){
      
        if(val==1){
           
            $("#category_id").attr("required",true);
            $("#fixed").attr("required",true);
            $("#product_id").attr("required",false);
            $("#offer_price").attr("required",false);
            document.getElementById("categorydiv").style.display="block";
            document.getElementById("productdiv").style.display="none";
            document.getElementById("coupondiv").style.display="none";
            
        }
        if(val==3){
            $("#category_id").attr("required",false);
            $("#fixed").attr("required",false);
            $("#product_id").attr("required",false);
            $("#offer_price").attr("required",false);
            document.getElementById("categorydiv").style.display="none";
            document.getElementById("productdiv").style.display="none";
            document.getElementById("coupondiv").style.display="block";
        }
        if(val==2){
            $("#category_id").attr("required",false);
            $("#fixed").attr("required", false);
            $("#product_id").attr("required",true);
            $("#offer_price").attr("required",true);
            document.getElementById("categorydiv").style.display="none";
            document.getElementById("productdiv").style.display="block";
            document.getElementById("coupondiv").style.display="none";
        }
    }
    function checkfixed(val){
        if(val>100){
            alert($("#fixerror").val());
            $("#fixed").val("");
        }
    }

    function getcoupondata(val){
         $.ajax( {
                     url: $("#url_path").val()+"/admin/getcoupondata"+"/"+val,
                     method:"GET",
                     data: { },
                     success: function( str ) {
                         var data = JSON.parse(str); 
                         $("#coupon_discount_value").val(data.value);                          
                         $("#start_date").val(data.start_date);
                         $("#end_date").val(data.end_date);
                       }
                    }); 
    }
    function checkofferprice(val){
        var sel_price=$("#mrp").val();
        if(sel_price!=""){
            if(parseInt(val)>parseInt(sel_price)){
                if(parseInt(val)>parseInt($("#selling_price").val())){
                    alert($("#check_price").val());
                     $("#offer_price").val("");
                }else{
                    alert($("#check_price").val());
                    $("#offer_price").val("");
                }
            }
        }
        else{
          alert($("#offer_price_error").val());
           $("#offer_price").val("");
        }
    }   
 $(document).ready(function () {
           $('#normalofferTable').DataTable({
              processing: true,
              serverSide: true,
              ajax: $("#url_path").val()+'/admin/offerdatatable'+"/"+2,
              columns: [
                {data: 'id'    , name: 'id'},
                {data: 'banner'  , name: 'banner'},
                {data: 'title'  , name: 'title'},
                {data: 'date'  , name: 'date'},
                {data: 'offer_on'  , name: 'offer_on'},
                {data: 'offer'  , name: 'offer'},
                {data: 'price'  , name: 'price'},
                {data: 'action', name:'action'}
             ],
             columnDefs: [
                { targets: 1,
                  render: function(data) {
                        return '<img src="'+data+'" style="height:50px">';
                  }
                }   
            ],
             order:[[0,"DESC"]]
          });
       });  
         
         function addsensonal() {
            window.location.href = $("#url_path").val()+"/admin/add_sensonal_offer";
        }
        $(document).ready(function() {
            $('#sensonalTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: $("#url_path").val()+'/admin/sensonaldatatable',
                columns: [{
                    data: 'id',
                    name: 'id'
                }, {
                    data: 'banner',
                    name: 'banner'
                }, {
                    data: 'title',
                    name: 'title'
                }, {
                    data: 'category',
                    name: 'category'
                }, {
                    data: 'action',
                    name: 'action'
                }],
                columnDefs: [{
                    targets: 1,
                    render: function(data) {
                        return '<img src="' + data + '" style="height:50px">';
                    }
                }],
                 order:[[0,"DESC"]]
            });
        });           
function change_record(data) {
            if (confirm($("#confirm_alert").val())) {
                window.location.href =data;
            } else {
                window.location.reload();
            }
        }
$(document).ready(function() {
            $('#ordercustomerTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: $("#url_path").val()+'/admin/orderdatatable',
                columns: [{
                    data: 'id',
                    name: 'id'
                }, {
                    data: 'name',
                    name: 'name'
                }, {
                    data: 'payment_method',
                    name: 'payment_method'
                }, {
                    data: 'shipping_method',
                    name: 'shipping_method'
                }, {
                    data: 'total',
                    name: 'total'
                },
                 {
                    data: 'view',
                    name: 'view'
                }, {
                    data: 'action',
                    name: 'action'
                }],
                columnDefs: [{
                    targets: 0,
                    render: function(data) {
                        var url = $("#url_path").val()+"/admin/vieworder" + "/" + data;
                        return '<a href="' + url + '" style="color: #007bff;text-decoration: underline;">' + data + '</a>';
                    }},
                    {
                    targets: 5,
                    render: function(data) {
                        var url = $("#url_path").val()+"/admin/vieworder" + "/" + data;
                        return '<a href="' + url + '" style="color: #007bff;text-decoration: underline;">'+$("#vieworder_lang").val()+'</a>';
                    }

                }],
                 order:[[0,"DESC"]]
            });
        });


        function savestatusorder(order_id, status_id) {
            if($("#demo_lang").val()==1){
                alert('This function is currently disable as it is only a demo website, in your admin it will work perfect');
            }
            else{
                window.location.href = $("#url_path").val()+"/admin/changeorderstatus" + "/" + order_id + "/" + status_id;
            }
            
        }    
  $(document).ready(function() {
            $('#order2myTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: $("#url_path").val()+'/admin/transactiondatatable',
                columns: [{
                    data: 'id',
                    name: 'id'
                }, {
                    data: 'transaction',
                    name: 'transaction'
                }, {
                    data: 'payment_method',
                    name: 'payment_method'
                }],
                columnDefs: [{
                    targets: 0,
                    render: function(data) {
                        var url =$("#url_path").val()+"/admin/vieworder" + "/" + data;
                        return '<a href="' + url + '" style="color: #007bff;text-decoration: underline;">' + data + '</a>';
                    }
                }],
                 order:[[0,"DESC"]]
            });
        });
          function printDiv() {
        window.frames['ifrm'].print();
    }   
     function addrowattribute() {
        var lastrow=$("#totalrow").val();
        var newrow=parseInt(lastrow)+1;
        var txt = '<tr id="row'+newrow+'"><td><i class="ti-layout-grid4-alt"></i></td><td data-id="'+newrow+'"><input type="text" required id="value_'+newrow+'" name="values[]" placeholder="" class="form-control"></td><td><button onclick="removerow('+newrow+')" class="btn btn-danger"><i class="fa fa-trash f-s-25"></i></button></td></tr>';
        $('#lstable').append(txt);
        $("#totalrow").val(newrow);
    }

   
     function saveoption(){
         var set_id=$("#set_id").val();
         var name=$("#name").val();
         var category=$("#categorydf").val();

         var is_required=0;
         if ($("#is_filter").prop("checked")) {
            is_required=1;
         }
         if(name!=""&&category!=""&&set_id!=""){
             $("#att_set_id").val(set_id);
             $("#att_name").val(name);
             $("#att_category").val(category);
             $("#att_filter").val(is_required);

            $("#home-tab").removeClass("active");
            $("#profile-tab").addClass("active");
            $("#home").removeClass("active");
            $("#profile").addClass("active show");
         }
         else{
             alert($("#requiredfields").val());
         }
         
    }  
    function addoptionrow() {
        var lastrow=$("#totalrow").val();
        var newrow=parseInt(lastrow)+1;
        var txt = '<tr id="row'+newrow+'"><td><i class="ti-layout-grid4-alt"></i></td><td data-id="'+newrow+'"><input type="text" required id="label_'+newrow+'" name="label[]" placeholder="" class="form-control"></td><td><input type="text"  id="price_'+newrow+'" name="price[]" placeholder="" class="form-control"></td><td><button onclick="removerow('+newrow+')" class="btn btn-danger"><i class="fa fa-trash f-s-25"></i></button></td></tr>';
        $('#lstable').append(txt);
        $("#totalrow").val(newrow);
    }

    function saveoptionall(){
         var name=$("#name").val();
         var type=$("#type").val();
         var is_required=0;
         if ($("#is_required").prop("checked")) {
            is_required=1;
         }
         if(name!=""&&type!=""){
             $("#option_name").val(name);
             $("#option_type").val(type);
             $("#option_required").val(is_required);

            $("#home-tab").removeClass("active");
            $("#profile-tab").addClass("active");
            $("#home").removeClass("active");
            $("#profile").addClass("active show");
         }
         else{
             alert($("#requiredfields").val());
         }
         
    }   

 $(document).ready(function () {
       $('#attributeTable').DataTable({
          processing: true,
          serverSide: true,
          ajax: $("#url_path").val()+'/admin/attributedatatable',
          columns: [
            {data: 'id'    , name: 'id'},
            {data: 'set_id'  , name: 'set_id'},
            {data: 'name'  , name: 'name'},
            {data: 'is_fill'  , name: 'is_fill'},
            {data: 'action', name:'action'}
         ],
          order:[[0,"DESC"]]
      });
   });
    
   
   function addattribute(){
      window.location.href=$("#url_path").val()+"/admin/addattribute";
   }
    $(document).ready(function () {
       $('#setTable').DataTable({
          processing: true,
          serverSide: true,
          ajax: $("#url_path").val()+'/admin/AttributeSetdatatable',
          columns: [
            {data: 'id'    , name: 'id'},
            {data: 'name'  , name: 'name'},
            {data: 'action', name:'action'}
         ],
          order:[[0,"DESC"]]
      });
   });
  

    function editset(id){
       $.ajax( {
         url: $("#url_path").val()+"/admin/getattrsetbyid"+"/"+id,
         data: { },
         success: function( data ) {
             $('#id').val(id);
             $("#edit_set").val(data);
           }
        }); 
    }
      $(document).ready(function () {
       $('#optionsTable').DataTable({
          processing: true,
          serverSide: true,
          ajax: $("#url_path").val()+'/admin/Optiondatatable',
          columns: [
            {data: 'id'    , name: 'id'},
            {data: 'name'  , name: 'name'},
            {data: 'type'  , name: 'type'},
            {data: 'action', name:'action'}
         ],
          order:[[0,"DESC"]]
      });
   });
  
   
   function addoption(){
      window.location.href=$("#url_path").val()+"/admin/addoption";
   }
   $(document).ready(function () {
       $('#productdataTable').DataTable({
          processing: true,
          serverSide: true,
          ajax: $("#url_path").val()+'/admin/productdatatable',
          columns: [
            {data: 'id'    , name: 'id'},
            {data: 'thumbnail'  , name: 'thumbnail'},
            {data: 'name'  , name: 'name'},
            {data: 'price'  , name: 'price'},
            {data: 'action', name:'action'}
         ],
           columnDefs: [
            { targets: 1,
              render: function(data) {
                    return '<img src="'+data+'" style="height:50px">';
              }
            }   
        ],
         order:[[0,"DESC"]]
      });
   });
   
   function addcatlog(){
     window.location.href=$("#url_path").val()+"/admin/savecatalog/0/1";
   }
    $(document).ready(function () {
       $('#productreviewTable').DataTable({
          processing: true,
          serverSide: true,
          ajax: $("#url_path").val()+'/admin/reviewdatatable'+"/"+0,
          columns: [
            {data: 'id'    , name: 'id'},
            {data: 'pro_name'  , name: 'pro_name'},
            {data: 'rev_name'  , name: 'rev_name'},
            {data: 'rating'  , name: 'rating'},
            {data: 'review'  , name: 'review'},
            {data: 'action', name:'action'}
         ],
          order:[[0,"DESC"]]
      });
   });
  function addsepical(){
        window.location.href=$("#url_path").val()+"/admin/addsepicalcategory";
     }
  $(document).ready(function () {
       $('#specialcategoryTable').DataTable({
          processing: true,
          serverSide: true,
          ajax: $("#url_path").val()+'/admin/sepicalcategorytable',
          columns: [
            {data: 'id'    , name: 'id'},
            {data: 'image'  , name: 'image'},
            {data: 'title'  , name: 'title'},
            {data: 'category'  , name: 'category'},
            {data: 'description'  , name: 'description'},
            {data: 'action', name:'action'}
         ],
         columnDefs: [
            { targets: 1,
              render: function(data) {
                    return '<img src="'+data+'" style="height:50px">';
              }
            }   
        ],
         order:[[0,"DESC"]]
      });
   });
    
    $(document).ready(function () {
       $('#pageTable').DataTable({
          processing: true,
          serverSide: true,
          ajax: $("#url_path").val()+'/admin/pagedatatable',
          columns: [
            {data: 'id'    , name: 'id'},
            {data: 'name'  , name: 'name'},
            {data: 'action', name:'action'}
         ],
          order:[[0,"DESC"]]
      });
   });
  function savegeneralinfo(){
                  var email=$("#email").val();
                  var logo=$("#logo").val();
                  var phone=$("#phone").val();
                  var address=$("#address").val();
                  var default_country=$("#default_country").val();
                  var default_locale=$("#default_locale").val();
                  var timezone=$("#timezone").val();
                  var currency=$("#currency").val();
                  var working_day=$("#working_day").val();
                  var helpline=$("#helpline").val();
                  var main_feature=$("#main_feature").val();
                  var newsletter=$("#newsletter").val();
                  var company_name=$("#company_name").val();
                  var is_customer_order=0;
                  if ($("#is_customer_order").prop("checked")) {
                            is_customer_order=1;
                  }
                  var is_email_confirm=0;
                  if ($("#is_email_confirm").prop("checked")) {
                            is_email_confirm=1;
                  }
                   var is_admin_send_mail=0;
                  if ($("#is_admin_send_mail").prop("checked")) {
                            is_admin_send_mail=1;
                  }
                  if(email!=""&&phone!=""&&address!=""&&default_country!=""&&default_locale!=""&&timezone!=""&&currency!=""&&main_feature!=""&&newsletter!=""){
                         $.ajax( {
                         url: $("#url_path").val()+"/admin/savegeneralsetting",
                         method:"post",
                         data: { 
                            email:email,
                            phone:phone,
                            address:address,
                            default_country:default_country,
                            default_locale:default_locale,
                            timezone:timezone,
                            currency:currency,
                            is_customer_order:is_customer_order,
                            is_email_confirm:is_email_confirm,
                            is_admin_send_mail:is_admin_send_mail,
                            working_day:working_day,
                            helpline:helpline,
                            main_feature:main_feature,
                            newsletter:newsletter,
                            company_name:company_name,
                            logo:logo
                         },
                         success: function( data ) {
                            $("#general").removeClass('in show active');
                            $('a[href="#general"]').removeClass('active');
                            $('a[href="#login"]').addClass('active');
                            $("#login").addClass('in show active');
    
                         }
                       });  
                  }
                  else{
                        alert($("#requiredfields").val());
                  }
               }

               function savesoicallogin(){
                 var facebook_id=$("#facebook_id").val();
                 var facebook_secret=$("#facebook_secret").val();
                 var google_id=$("#google_id").val();
                 var google_secret=$("#google_secret").val();                
                 var is_facebook_required=0;
                 var is_google_required=0;
                 if ($("#is_google_required").prop("checked")) {
                            is_google_required=1;
                 }
                 if ($("#is_facebook_required").prop("checked")) {
                            is_facebook_required=1;
                 }
                //  if(facebook_id!=""&&facebook_secret!=""&&google_id!=""&&google_secret!=""&&is_facebook_required!=""&&is_google_required!=""){
                         $.ajax( {
                         url: $("#url_path").val()+"/admin/savesoicalsetting",
                         method:"post",
                         data: { 
                            facebook_id:facebook_id,
                            facebook_secret:facebook_secret,
                            google_id:google_id,
                            google_secret:google_secret,
                            is_facebook_required:is_facebook_required,
                            is_google_required:is_google_required
                         },
                         success: function( data ) {
                            $("#login").removeClass('in show active');
                            $('a[href="#login"]').removeClass('active');
                            $('a[href="#shipping"]').addClass('active');
                            $("#shipping").addClass('in show active');
    
                         }
                       });  
                 // }
                 // else{
                  //      alert($("#requiredfields").val());
                 // }
               }
                  $(document).ready(function () {
                       $('#shippingTable').DataTable({
                          processing: true,
                          serverSide: true,
                          ajax: $("#url_path").val()+'/admin/shippingdatatable',
                          columns: [
                            {data: 'id'    , name: 'id'},
                            {data: 'label'  , name: 'label'},
                            {data: 'cost', name:'cost'},
                            {data: 'action', name:'action'},
                         ],
                      });
                 });

                function editshipping(id){
                   $.ajax( {
                     url: $("#url_path").val()+"/admin/editshipping"+"/"+id,
                     data: { },
                     success: function( str ) {
                         var data = JSON.parse(str);                           
                         $('#id').val(id);
                         $("#label").val(data.label);
                         $("#cost").val(data.cost);
                       }
                    }); 
                }

                function changepayment(val){
                    var payment_id=val;
                    var label=$("#pay"+val+"_label").val();
                    var description=$("#pay"+val+"_desc").val();                    
                    var status=0;
                    var key="";
                    var secret="";
                    var paymentmode=0;
                    if ($("#is_enable"+val).prop("checked")) {
                            status=1;
                    }
                    if(val==1){
                         if ($("#is_paymentmode").prop("checked")) {
                            paymentmode=1;
                         }
                         else{
                            paymentmode=2;
                         }
                    }
                    if(val==1||val==2){
                        key=$("#pay"+val+"_key").val();
                        secret=$("#pay"+val+"_secret_key").val();
                    }
                    console.log(status);
                    if(label!=""&&description!=""&&payment_id!=""){
                         $.ajax( {
                                         url: $("#url_path").val()+"/admin/savepaymentdata",
                                         method:"post",
                                         data: { 
                                            id:payment_id,
                                            label:label,
                                            description:description,
                                            status:status,
                                            key:key,
                                            secret:secret,
                                            paymentmode:paymentmode
                                         },
                                         success: function( data ) {

                                             var fd=val;
                                             if(val==3){
                                                 var sd=val;
                                                 alert($("#data_save_success").val());
                                             }
                                             else{
                                                 var sd=parseInt(val)+1;
                                             }
                                            $("#pay"+fd).removeClass('in show active');
                                            $('a[href="#pay'+fd+'"]').removeClass('active');
                                            $('a[href="#pay'+sd+'"]').addClass('active');
                                            $("#pay"+sd).addClass('in show active');
                    
                                         }
                       });  
                  }
                  else{
                        alert($("#requiredfields").val());
                  }
                }   
 $(document).ready(function () {
       $('#QuestionTable').DataTable({
          processing: true,
          serverSide: true,
          ajax: $("#url_path").val()+'/admin/topicdatatable'+"/"+$("#page_id").val(),
          columns: [
            {data: 'id'    , name: 'id'},
            {data: 'topic'  , name: 'topic'},
            {data: 'action', name:'action'}
         ],         
      });
   });
   
   function editsupport(id){
       $.ajax( {
                     url: $("#url_path").val()+ "/admin/editsupport"+"/"+id,
                     data: { },
                     success: function( str ) {
                        var data=JSON.parse(str);
                         $("#edit_id").val(id);
                         $("#edit_topicname").val(data.topic);
                     }
                 });
   }   
    $(document).ready(function () {
       $('#quesdatatable').DataTable({
          processing: true,
          serverSide: true,
          ajax: $("#url_path").val()+'/admin/quesdatatable'+"/"+$("#topic_id").val(),
          columns: [
            {data: 'id'    , name: 'id'},
            {data: 'ques'  , name: 'ques'},
            {data: 'ans'  , name: 'ans'},
            {data: 'action', name:'action'}
         ],  
          order:[[0,"DESC"]]
      });
   });
   
   function editques(id){
       $.ajax( {
                     url: $("#url_path").val()+"/admin/editques"+"/"+id,
                     data: { },
                     success: function( str ) {
                          var data=JSON.parse(str);
                         $("#edit_id").val(id);
                         $("#edit_ques").val(data.question);
                         $("#edit_ans").val(data.answer);
                     }
                 });
   }             
     function checkbothpwd(val){
        var password=$("#password").val();
        if(val!=password){
            alert($("#pass_mus").val());
            $("#password").val("");
            $("#confirm_password").val("");
        }
     }
      $(document).ready(function () {
       $('#adminTable').DataTable({
          processing: true,
          serverSide: true,
          ajax: $("#url_path").val()+'/admin/userdatatable'+"/"+2,
          columns: [
            {data: 'id'    , name: 'id'},
            {data: 'name'  , name: 'name'},
            {data: 'email'  , name: 'email'},
            {data: 'phone'  , name: 'phone'},
            {data: 'action', name:'action'}
         ],   
          order:[[0,"DESC"]]
      });
   });
   
   function edituser(id){
       $.ajax( {
                     url: $("#url_path").val()+"/admin/edituser"+"/"+id,
                     data: { },
                     success: function( str ) {
                       var data=JSON.parse(str);
                         $("#id").val(id);
                         $("#edit_first_name").val(data.first_name);
                         $("#edit_email").val(data.email);
                         $("#edit_phone").val(data.phone);
                         $("#edit_address").val(data.address);
                     }
                 });
   }
    $(document).ready(function () {
       $('#userTable').DataTable({
          processing: true,
          serverSide: true,
          ajax: $("#url_path").val()+'/admin/userdatatable'+"/"+1,
          columns: [
            {data: 'id'    , name: 'id'},
            {data: 'name'  , name: 'name'},
            {data: 'email'  , name: 'email'},
            {data: 'phone'  , name: 'phone'},
            {data: 'action', name:'action'}
         ],  
          order:[[0,"DESC"]]
      });
   });
    function changecheckboth(val){
                      var npwd=$("#npwd").val();
                      if(npwd!=val){
                        alert($("#pass_mus").val());
                        $("#npwd").val("");
                        $("#rpwd").val("");
                      }
                    }
                    function checkcurrentpwd(val){
                         $.ajax( {
                             url: $("#url_path").val()+"/admin/samepwd"+"/"+val,
                             data: { },
                             success: function( data ) {
                                if(data==0){
                                    alert($("#error_cur_pwd").val());
                                    $("#cpwd").val("");
                                }
                             }
                         });
                    }
   $(document).ready(function () {
       $('#contactTable').DataTable({
          processing: true,
          serverSide: true,
          ajax: $("#url_path").val()+'/admin/contactdatatable',
          columns: [
            {data: 'id'    , name: 'id'},
            {data: 'name'  , name: 'name'},
            {data: 'email'  , name: 'email'},
            {data: 'phone'  , name: 'phone'},
            {data: 'subject'  , name: 'subject'},
            {data: 'message'  , name: 'message'},
            {data: 'action', name:'action'}
         ],    
          order:[[0,"DESC"]]
      });
   });
   $(document).ready(function () {
       $('#roleTable').DataTable({
        
      });
   }); 
      $(document).ready(function () {
                               $('#latestorderTable').DataTable({
                                  processing: true,
                                  serverSide: true,
                                  pageLength:5 ,
                                  bLengthChange: false,
                                  searching: false,
                                  ajax: $("#url_path").val()+'/admin/latestorder',
                                  columns: [
                                    {data: 'id'    , name: 'id'},
                                    {data: 'customer'  , name: 'customer'},
                                    {data: 'status', name:'status'},
                                    {data: 'total', name:'total'},
                                 ],
                                 order:[[0,"DESC"]]
                              });
                           });
                              $(document).ready(function () {
                               $('#myTablereview').DataTable({
                                  processing: true,
                                  serverSide: true,
                                   pageLength:5 ,
                                   bLengthChange: false,
                                   searching: false,
                                  ajax: $("#url_path").val()+'/admin/latestreview',
                                  columns: [
                                    {data: 'product_id'    , name: 'product_id'},
                                    {data: 'customer'  , name: 'customer'},
                                    {data: 'ratting', name:'ratting'}
                                 ],
                                 order:[[0,"DESC"]]
                              });
                           });                 
   $('#coupon_report').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: $("#url_path").val()+'/admin/couponreport'+"/"+"abc"+"/"+"abc"+"/"+0+"/"+0,
                        columns: [
                          {data: 'date'  , name: 'date'},
                          {data: 'code'  , name: 'code'},
                          {data: 'name'  , name: 'name'},
                          {data: 'order', name:'order'},
                          {data: 'total', name:'total'},
                       ],  
                        dom: 'Bfrtip',
                        buttons: [
                           'excel', 'pdf'
                        ]       
                });
   
    function filterreport(report_id){
       var txt="";
       $("#coupon_report").dataTable().fnDestroy()
       $("#customer_order_report").dataTable().fnDestroy()
       $("#product_purchase_report").dataTable().fnDestroy()
       $("#product_stock_report").dataTable().fnDestroy()
       $("#sales_report").dataTable().fnDestroy()
       $("#shipping_report").dataTable().fnDestroy()
       $("#tax_report").dataTable().fnDestroy()
       $("#add_product_report").dataTable().fnDestroy()
       $("#top_seller_report").dataTable().fnDestroy()
       $("#add_customer_report").dataTable().fnDestroy()
       $("#add_coupon_report").dataTable().fnDestroy()
        if(report_id==1){
              txt=txt+'<div class="form-group has-success"><label for="start_date" class="control-label mb-1">'+$("#start_date_txt").val()+'</label><input type="text" class="form-control" name="start_date" id="start_date" ></div><div class="form-group has-success" ><label for="start_date" class="control-label mb-1">'+$("#end_date_txt").val()+'</label><input type="text" class="form-control" name="end_date" id="end_date"></div><div class="form-group has-success"><label for="status" class="control-label mb-1">'+$("#order_status_txt").val()+'</label><select name="order_status" id="order_status" class="form-control"><option value="">'+$("#select_txt").val()+'</option><option value="6">'+$("#canceled_txt").val()+'</option><option value="5">'+$("#completed_txt").val()+'</option><option value="2">'+$("#on_hold_txt").val()+'</option><option value="3">'+$("#pending_txt").val()+'</option><option value="1">'+$("#processing_txt").val()+'</option><option value="7">'+$("#refunded_txt").val()+'</option><option value="4">'+$("#out_of_delivery_txt").val()+'</option></select></div><div class="form-group has-success"><label for="coupon_code" class="control-label mb-1"> '+$("#coupon_code_txt").val()+'</label><input type="text" name="coupon_code" id="coupon_code" class="form-control"></div>';
                
                $('#coupon_report').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: $("#url_path").val()+'/admin/couponreport'+"/"+"abc"+"/"+"abc"+"/"+0+"/"+0,
                        columns: [
                          {data: 'date'  , name: 'date'},
                          {data: 'code'  , name: 'code'},
                          {data: 'name'  , name: 'name'},
                          {data: 'order', name:'order'},
                          {data: 'total', name:'total'},
                       ], 
                       dom: 'Bfrtip',
                        buttons: [
                           'excel', 'pdf'
                        ]         
                });
            
        }
        else if(report_id==2){
              txt=txt+'<div class=" form-group has-success"><label for="start_date" class="control-label mb-1">'+$("#start_date_txt").val()+'</label><input type="text" class="form-control" name="start_date" id="start_date" ></div><div class=" form-group has-success" ><label for="start_date" class="control-label mb-1">'+$("#end_date_txt").val()+'</label><input type="text" class="form-control" name="end_date" id="end_date"></div> <div class=" form-group has-success"><label for="status" class="control-label mb-1">'+$("#order_status_txt").val()+'</label><select name="order_status" id="order_status" class="form-control"><option value="">'+$("#select_txt").val()+'</option><option value="6">'+$("#canceled_txt").val()+'</option><option value="5">'+$("#completed_txt").val()+'</option><option value="2">'+$("#on_hold_txt").val()+'</option><option value="3">'+$("#pending_txt").val()+'</option><option value="1">'+$("#processing_txt").val()+'</option><option value="7">'+$("#refunded_txt").val()+'</option><option value="4">'+$("#out_of_delivery_txt").val()+'</option></select></div><div class="form-group has-success"><div class=" form-group has-success"><label for="customer_name" class="control-label mb-1">'+$("#cus_name_txt").val()+'</label><input type="text" name="customer_name" id="customer_name" class="form-control"></div> <div class=" form-group has-success"><label for="status" class="control-label mb-1">'+$("#cus_email_txt").val()+'</label><input type="text" name="customer_email" id="customer_email" class="form-control"></div>';
                 $('#customer_order_report').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: $("#url_path").val()+'/admin/customer_order_report'+"/"+"abc"+"/"+"abc"+"/"+0+"/"+0+"/"+0,
                        columns: [
                          {data: 'date'  , name: 'date'},
                          {data: 'name'  , name: 'name'},
                          {data: 'email' , name: 'email'},
                          {data: 'order' , name:  'order'},
                          {data: 'total' , name:'total'},
                       ],  
                       dom: 'Bfrtip',
                        buttons: [
                           'excel', 'pdf'
                        ]  

                });
               
        }
        else if(report_id==3){
              txt=txt+'<div class=" form-group has-success"><label for="start_date" class="control-label mb-1">'+$("#start_date_txt").val()+'</label><input type="text" class="form-control" name="start_date" id="start_date" ></div><div class=" form-group has-success" ><label for="start_date" class="control-label mb-1">'+$("#end_date_txt").val()+'</label><input type="text" class="form-control" name="end_date" id="end_date"></div> <div class=" form-group has-success"><label for="status" class="control-label mb-1">'+$("#order_status_txt").val()+'</label><select name="order_status" id="order_status" class="form-control"><option value="">'+$("#select_txt").val()+'</option><option value="6">'+$("#canceled_txt").val()+'</option><option value="5">'+$("#completed_txt").val()+'</option><option value="2">'+$("#on_hold_txt").val()+'</option><option value="3">'+$("#pending_txt").val()+'</option><option value="1">'+$("#processing_txt").val()+'</option><option value="7">'+$("#refunded_txt").val()+'</option><option value="4">'+$("#out_of_delivery_txt").val()+'</option></select></div><div class=" form-group has-success"><label for="product_name" class="control-label mb-1">'+$("#product_txt").val()+'</label><input type="text" name="product_name" id="product_name" class="form-control"></div><div class=" form-group has-success"><label for="status" class="control-label mb-1">'+$("#SKU_txt").val()+'</label><input type="text" name="sku" id="sku" class="form-control"></div>';
                $('#product_purchase_report').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: $("#url_path").val()+'/admin/product_purchase_report'+"/"+"abc"+"/"+"abc"+"/"+0+"/"+0+"/"+0,
                        columns: [
                          {data: 'date'  , name: 'date'},
                          {data: 'product'  , name: 'product'},
                          {data: 'qty' , name: 'qty'}
                       ],     
                       dom: 'Bfrtip',
                        buttons: [
                           'excelFlash', 'excel', 'pdf'
                        ]     
                });
        }
        else if(report_id==4){
              txt=txt+'<div class=" form-group has-success"><label for="product_name" class="control-label mb-1">'+$("#product_name_txt").val()+'</label><input type="text" name="product_name" id="product_name" class="form-control"></div><div class=" form-group has-success"><label for="status" class="control-label mb-1">'+$("#SKU_txt").val()+'</label><input type="text" name="sku" id="sku" class="form-control"></div><div class=" form-group has-success"><label for="status" class="control-label mb-1">'+$("#stock_avilable_txt").val()+'</label><select name="stock_avilable" id="stock_avilable" class="form-control"><option value="">'+$("#select_txt").val()+'</option><option value="1">'+$("#in_stock_txt").val()+'</option><option value="0">'+$("#outstock_txt").val()+'</option></select></div>';
                $('#product_stock_report').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: $("#url_path").val()+'/admin/product_stock_report'+"/"+0+"/"+0+"/"+3,
                        columns: [
                          {data: 'product'  , name: 'product'},
                          {data:'sku',name:'sku'},
                          {data: 'stock'  , name: 'stock'}
                       ],    
                       dom: 'Bfrtip',
                        buttons: [
                           'excelFlash', 'excel', 'pdf'
                        ]      
                });
        }
        else if(report_id==5){
              txt=txt+'<div class=" form-group has-success"><label for="start_date" class="control-label mb-1">'+$("#start_date_txt").val()+'</label><input type="text" class="form-control" name="start_date" id="start_date" ></div><div class=" form-group has-success" ><label for="start_date" class="control-label mb-1">'+$("#end_date_txt").val()+'</label><input type="text" class="form-control" name="end_date" id="end_date"></div> <div class=" form-group has-success"><label for="status" class="control-label mb-1">'+$("#order_status_txt").val()+'</label><select name="order_status" id="order_status" class="form-control"><option value="">'+$("#select_txt").val()+'</option><option value="6">'+$("#canceled_txt").val()+'</option><option value="5">'+$("#completed_txt").val()+'</option><option value="2">'+$("#on_hold_txt").val()+'</option><option value="3">'+$("#pending_txt").val()+'</option><option value="1">'+$("#processing_txt").val()+'</option><option value="7">'+$("#refunded_txt").val()+'</option><option value="4">'+$("#out_of_delivery_txt").val()+'</option></select></div>';
                $('#sales_report').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: $("#url_path").val()+'/admin/sales_report'+"/"+"abc"+"/"+"abc"+"/"+0,
                        columns: [
                          {data: 'date'  , name: 'date'},
                          {data: 'order'  , name: 'order'},
                          {data: 'product' , name: 'product'},
                          {data: 'subtotal' , name: 'subtotal'},
                          {data: 'shipping' , name: 'shipping'},
                          {data: 'tax' , name: 'tax'},
                          {data: 'total' , name: 'total'}
                       ], 
                       dom: 'Bfrtip',
                        buttons: [
                           'excelFlash', 'excel', 'pdf'
                        ]         
                });
        }
        else if(report_id==6){
              txt=txt+'<div class=" form-group has-success"><label for="start_date" class="control-label mb-1">'+$("#start_date_txt").val()+'</label><input type="text" class="form-control" name="start_date" id="start_date" ></div><div class=" form-group has-success" ><label for="start_date" class="control-label mb-1">'+$("#end_date_txt").val()+'</label><input type="text" class="form-control" name="end_date" id="end_date"></div><div class=" form-group has-success"><label for="status" class="control-label mb-1">'+$("#shipping_method_txt").val()+'</label><select name="shipping_type" id="shipping_type" class="form-control"><option value="">'+$("#select_txt").val()+'</option><option value="1">'+$("#home_delivery_txt").val()+'</option><option value="2">'+$("#local_pickup_txt").val()+'</option></select></div>';
               $('#shipping_report').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: $("#url_path").val()+'/admin/shipping_report'+"/"+"abc"+"/"+"abc"+"/"+0,
                        columns: [
                          {data: 'date'  , name: 'date'},
                          {data: 'name'  , name: 'name'},
                          {data: 'order' , name: 'order'},
                          {data: 'total' , name: 'total'}
                       ],  
                       dom: 'Bfrtip',
                        buttons: [
                           'excelFlash', 'excel', 'pdf'
                        ]        
                });
        }
        else if(report_id==7){
              txt=txt+'<div class=" form-group has-success"><label for="start_date" class="control-label mb-1">'+$("#start_date_txt").val()+'</label><input type="text" class="form-control" name="start_date" id="start_date" ></div><div class=" form-group has-success" ><label for="start_date" class="control-label mb-1">'+$("#end_date_txt").val()+'</label><input type="text" class="form-control" name="end_date" id="end_date"></div><div class=" form-group has-success"><label for="status" class="control-label mb-1">'+$("#tax_name").val()+'</label><input type="text" name="tax_name" id="tax_name" class="form-control"></div>';
                $('#tax_report').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: $("#url_path").val()+'/admin/tax_report'+"/"+"abc"+"/"+"abc"+"/"+0,
                        columns: [
                          {data: 'date'  , name: 'date'},
                          {data: 'name'  , name: 'name'},
                          {data: 'order' , name: 'order'},
                          {data: 'total' , name: 'total'}
                       ],    
                       dom: 'Bfrtip',
                        buttons: [
                           'excelFlash', 'excel', 'pdf'
                        ]      
                });
        }
        else if(report_id==8){
              txt=txt+'<div class=" form-group has-success"><label for="start_date" class="control-label mb-1">'+$("#start_date_txt").val()+'</label><input type="text" class="form-control" name="start_date" id="start_date" ></div><div class=" form-group has-success" ><label for="start_date" class="control-label mb-1">'+$("#end_date_txt").val()+'</label><input type="text" class="form-control" name="end_date" id="end_date"></div>';
                $('#add_product_report').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: $("#url_path").val()+'/admin/add_product_report'+"/"+"abc"+"/"+"abc",
                        columns: [
                          {data: 'date'  , name: 'date'},
                          {data: 'name'  , name: 'name'},
                          {data: 'email' , name: 'email'}
                       ],  
                       dom: 'Bfrtip',
                        buttons: [
                           'excelFlash', 'excel', 'pdf'
                        ]        
                });
        }
        else if(report_id==9){
             txt=txt+'<div class=" form-group has-success"><label for="start_date" class="control-label mb-1">'+$("#start_date_txt").val()+'</label><input type="text" class="form-control" name="start_date" id="start_date" ></div><div class=" form-group has-success" ><label for="start_date" class="control-label mb-1">'+$("#end_date_txt").val()+'</label><input type="text" class="form-control" name="end_date" id="end_date"></div>';
              $('#top_seller_report').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: $("#url_path").val()+'/admin/top_seller_report'+"/"+"abc"+"/"+"abc",
                        columns: [
                          {data: 'date'  , name: 'date'},
                          {data: 'name'  , name: 'name'},
                          {data: 'sku'   , name: 'sku'},
                          {data: 'order'  ,name: 'order'}
                       ], 
                       dom: 'Bfrtip',
                        buttons: [
                           'excelFlash', 'excel', 'pdf'
                        ]         
                });
        }
        else if(report_id==10){
               txt=txt+'<div class=" form-group has-success"><label for="start_date" class="control-label mb-1">'+$("#start_date_txt").val()+'</label><input type="text" class="form-control" name="start_date" id="start_date" ></div><div class=" form-group has-success" ><label for="start_date" class="control-label mb-1">'+$("#end_date_txt").val()+'</label><input type="text" class="form-control" name="end_date" id="end_date"></div>';
                $('#add_customer_report').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: $("#url_path").val()+'/admin/add_customer_report'+"/"+"abc"+"/"+"abc",
                        columns: [
                          {data: 'date'  , name: 'date'},
                          {data: 'name'  , name: 'name'},
                          {data: 'email' , name: 'email'}
                       ],   
                       dom: 'Bfrtip',
                        buttons: [
                           'excelFlash', 'excel', 'pdf'
                        ]       
                });
        }
        else if(report_id==11){
              txt=txt+'<div class=" form-group has-success"><label for="start_date" class="control-label mb-1">'+$("#start_date_txt").val()+'</label><input type="text" class="form-control" name="start_date" id="start_date" ></div><div class=" form-group has-success" ><label for="start_date" class="control-label mb-1">'+$("#end_date_txt").val()+'</label><input type="text" class="form-control" name="end_date" id="end_date"></div>';
                $('#add_coupon_report').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: $("#url_path").val()+'/admin/add_coupon_report'+"/"+"abc"+"/"+"abc",
                        columns: [
                          {data: 'date'  , name: 'date'},
                          {data: 'name'  , name: 'name'},
                          {data: 'code' , name: 'code'},
                          {data: 'rate' , name: 'rate'}
                       ],  
                       dom: 'Bfrtip',
                        buttons: [
                           'excelFlash', 'excel', 'pdf'
                        ]        
                });
        }
        else{

        }

                        document.getElementById("filter_section").innerHTML=txt;
                        $('#start_date, #end_date').datepicker({
                            showOn: "both",
                            beforeShow: customRange,
                            dateFormat: "MM dd,yy",
                        });
                        function customRange(input) {
                    
                        if (input.id == 'end_date') {
                            var minDate = new Date($('#start_date').val());
                            minDate.setDate(minDate.getDate() + 1)
                    
                            return {
                                minDate: minDate
                    
                            };
                        }
                    
                        return {}
                    
                    }
                  if(report_id==1){
                      document.getElementById("coupon_report").style.display="inline-table";
                  }
                  else{
                     document.getElementById("coupon_report").style.display="none";
                  }
                  if(report_id==2){
                      document.getElementById("customer_order_report").style.display="inline-table";
                  }
                  else{
                     document.getElementById("customer_order_report").style.display="none";
                  }
                  if(report_id==3){
                      document.getElementById("product_purchase_report").style.display="inline-table";
                  }
                  else{
                     document.getElementById("product_purchase_report").style.display="none";
                  }
                  if(report_id==4){
                      document.getElementById("product_stock_report").style.display="inline-table";
                  }
                  else{
                     document.getElementById("product_stock_report").style.display="none";
                  }
                  if(report_id==5){
                      document.getElementById("sales_report").style.display="inline-table";
                  }
                  else{
                     document.getElementById("sales_report").style.display="none";
                  }
                  if(report_id==6){
                      document.getElementById("shipping_report").style.display="inline-table";
                  }
                  else{
                     document.getElementById("shipping_report").style.display="none";
                  }
                  if(report_id==7){
                      document.getElementById("tax_report").style.display="inline-table";
                  }
                  else{
                     document.getElementById("tax_report").style.display="none";
                  }
                  if(report_id==8){
                      document.getElementById("add_product_report").style.display="inline-table";
                  }
                  else{
                     document.getElementById("add_product_report").style.display="none";
                  }
                  if(report_id==9){
                      document.getElementById("top_seller_report").style.display="inline-table";
                  }
                  else{
                     document.getElementById("top_seller_report").style.display="none";
                  }
                  if(report_id==10){
                      document.getElementById("add_customer_report").style.display="inline-table";
                  }
                  else{
                     document.getElementById("add_customer_report").style.display="none";
                  }
                  if(report_id==11){
                      document.getElementById("add_coupon_report").style.display="inline-table";
                  }
                  else{
                     document.getElementById("add_coupon_report").style.display="none";
                  }
    }

       


    function filterdata(){
        var report_id=$("#report").val();
        if(report_id==1){
            var start_date=$("#start_date").val();
            var end_date=$("#end_date").val();
            var orderstatus=$("#order_status").val();
            var coupon_code=$("#coupon_code").val();
            if(start_date==""){
                start_date="abc";
                end_date="abc";
            }
            
            if(orderstatus==""){
                orderstatus=0;
            }
            if(coupon_code==""){
                coupon_code=0;
            }
             $("#coupon_report").dataTable().fnDestroy()
            $('#coupon_report').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: $("#url_path").val()+'/admin/couponreport'+"/"+start_date+"/"+end_date+"/"+orderstatus+"/"+coupon_code,
                        columns: [
                          {data: 'date'  , name: 'date'},
                          {data: 'code'  , name: 'code'},
                          {data: 'name'  , name: 'name'},
                          {data: 'order', name:'order'},
                          {data: 'total', name:'total'},
                       ],   
                       dom: 'Bfrtip',
                        buttons: [
                           'excelFlash', 'excel', 'pdf'
                        ]       
                });
        }
        else if(report_id==2){
            var start_date=$("#start_date").val();
            var end_date=$("#end_date").val();
            var orderstatus=$("#order_status").val();
            var customer_name=$("#customer_name").val();
            var customer_email=$("#customer_email").val();
            if(start_date==""){
                start_date="abc";
                end_date="abc";
            }
            if(orderstatus==""){
                orderstatus=0;
            }
            if(customer_name==""){
                customer_name=0;
            }
            if(customer_email==""){
                customer_email=0;
            }
            $("#customer_order_report").dataTable().fnDestroy()
            $('#customer_order_report').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: $("#url_path").val()+'/admin/customer_order_report'+"/"+start_date+"/"+end_date+"/"+orderstatus+"/"+customer_name+"/"+customer_email,
                        columns: [
                          {data: 'date'  , name: 'date'},
                          {data: 'name'  , name: 'name'},
                          {data: 'email' , name: 'email'},
                          {data: 'order', name:'order'},
                          {data: 'total', name:'total'},
                       ], 
                       dom: 'Bfrtip',
                        buttons: [
                           'excelFlash', 'excel', 'pdf'
                        ]         
                });
            
        }
        else if(report_id==3){
            var start_date=$("#start_date").val();
            var end_date=$("#end_date").val();
            var orderstatus=$("#order_status").val();
            var product_name=$("#product_name").val();
            var sku=$("#sku").val();
             if(start_date==""){
                start_date="abc";
                end_date="abc";
            }
            if(orderstatus==""){
                orderstatus=0;
            }
            if(product_name==""){
                product_name=0;
            }
            if(sku==""){
                sku=0;
            }
             $("#product_purchase_report").dataTable().fnDestroy()
            $('#product_purchase_report').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax:$("#url_path").val()+'/admin/product_purchase_report'+"/"+start_date+"/"+end_date+"/"+orderstatus+"/"+product_name+"/"+sku,
                        columns: [
                          {data: 'date'  , name: 'date'},
                          {data: 'product'  , name: 'product'},
                          {data: 'qty' , name: 'qty'}
                       ],     
                       dom: 'Bfrtip',
                        buttons: [
                           'excelFlash', 'excel', 'pdf'
                        ]     
                });

        }
        else if(report_id==4){
            var product_name=$("#product_name").val();
            var sku=$("#sku").val();
            var stock_avilable=$("#stock_avilable").val();
            if(product_name==""){
              product_name=0;
            }
            if(sku==""){
              sku=0;
            }
            if(stock_avilable==""){
               stock_avilable=3;
            }
             $("#product_stock_report").dataTable().fnDestroy()
            $('#product_stock_report').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: $("#url_path").val()+'/admin/product_stock_report'+"/"+product_name+"/"+sku+"/"+stock_avilable,
                        columns: [
                          {data: 'product'  , name: 'product'},
                          {data:'sku',name:'sku'},
                          {data: 'stock'  , name: 'stock'}
                       ], 
                       dom: 'Bfrtip',
                        buttons: [
                           'excelFlash', 'excel', 'pdf'
                        ]         
                });
        }
        else if(report_id==5){
             var start_date=$("#start_date").val();
             var end_date=$("#end_date").val();
             var orderstatus=$("#order_status").val();
               if(start_date==""&&end_date==""){
                start_date="abc";
                end_date="abc";
             }
             if(orderstatus==""){
                orderstatus=0;
             }
              $("#sales_report").dataTable().fnDestroy()
              $('#sales_report').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: $("#url_path").val()+'/admin/sales_report'+"/"+start_date+"/"+end_date+"/"+orderstatus,
                        columns: [
                          {data: 'date'  , name: 'date'},
                          {data: 'order'  , name: 'order'},
                          {data: 'product' , name: 'product'},
                          {data: 'subtotal' , name: 'subtotal'},
                          {data: 'shipping' , name: 'shipping'},
                          {data: 'tax' , name: 'tax'},
                          {data: 'total' , name: 'total'}
                       ],   
                       dom: 'Bfrtip',
                        buttons: [
                           'excelFlash', 'excel', 'pdf'
                        ]       
                });
        }
        else if(report_id==6){
            var start_date=$("#start_date").val();
            var end_date=$("#end_date").val();            
            var shipping_type=$("#shipping_type").val();
            if(start_date==""&&end_date==""){
                start_date="abc";
                end_date="abc";
             }
             if(shipping_type==""){
                shipping_type=0;
             }
             $("#shipping_report").dataTable().fnDestroy()
            $('#shipping_report').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: $("#url_path").val()+'/admin/shipping_report'+"/"+start_date+"/"+end_date+"/"+shipping_type,
                        columns: [
                          {data: 'date'  , name: 'date'},
                          {data: 'name'  , name: 'name'},
                          {data: 'order' , name: 'order'},
                          {data: 'total' , name: 'total'}
                       ], 
                       dom: 'Bfrtip',
                        buttons: [
                           'excelFlash', 'excel', 'pdf'
                        ]         
                });
        }
        else if(report_id==7){
            var start_date=$("#start_date").val();
            var end_date=$("#end_date").val();
            var tax_name=$("#tax_name").val();
             if(start_date==""&&end_date==""){
                start_date="abc";
                end_date="abc";
             }
             if(tax_name==""){
                tax_name=0;
             }
             $("#tax_report").dataTable().fnDestroy()
             $('#tax_report').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: $("#url_path").val()+'/admin/tax_report'+"/"+start_date+"/"+end_date+"/"+tax_name,
                        columns: [
                          {data: 'date'  , name: 'date'},
                          {data: 'tax_name'  , name: 'tax_name'},
                          {data: 'order' , name: 'order'},
                          {data: 'total' , name: 'total'}
                       ],  
                       dom: 'Bfrtip',
                        buttons: [
                           'excelFlash', 'excel', 'pdf'
                        ]        
                });

        }
        else if(report_id==8){
            var start_date=$("#start_date").val();
            var end_date=$("#end_date").val();
             if(start_date==""&&end_date==""){
                start_date="abc";
                end_date="abc";
             }
             $("#add_product_report").dataTable().fnDestroy()
             $('#add_product_report').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: $("#url_path").val()+'/admin/add_product_report'+"/"+start_date+"/"+end_date,
                        columns: [
                          {data: 'date'  , name: 'date'},
                          {data: 'name'  , name: 'name'},
                          {data: 'email' , name: 'email'}
                       ],   
                       dom: 'Bfrtip',
                        buttons: [
                           'excelFlash', 'excel', 'pdf'
                        ]       
                });
        }
        else if(report_id==9){
            var start_date=$("#start_date").val();
            var end_date=$("#end_date").val();
             if(start_date==""&&end_date==""){
                start_date="abc";
                end_date="abc";
             }
             $("#top_seller_report").dataTable().fnDestroy()
             $('#top_seller_report').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: $("#url_path").val()+'/admin/top_seller_report'+"/"+start_date+"/"+end_date,
                        columns: [
                          {data: 'date'  , name: 'date'},
                          {data: 'name'  , name: 'name'},
                          {data: 'sku'   , name: 'sku'},
                          {data: 'order'  ,name: 'order'}
                       ], 
                       dom: 'Bfrtip',
                        buttons: [
                           'excelFlash', 'excel', 'pdf'
                        ]         
                });
        }
        else if(report_id==10){
            var start_date=$("#start_date").val();
            var end_date=$("#end_date").val();
             if(start_date==""&&end_date==""){
                start_date="abc";
                end_date="abc";
             }
             $("#add_customer_report").dataTable().fnDestroy()
             $('#add_customer_report').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: $("#url_path").val()+'/admin/add_customer_report'+"/"+start_date+"/"+end_date,
                        columns: [
                          {data: 'date'  , name: 'date'},
                          {data: 'name'  , name: 'name'},
                          {data: 'email' , name: 'email'}
                       ], 
                       dom: 'Bfrtip',
                        buttons: [
                           'excelFlash', 'excel', 'pdf'
                        ]         
                });
        }
        else if(report_id==11){
            var start_date=$("#start_date").val();
            var end_date=$("#end_date").val();
            if(start_date==""&&end_date==""){
                start_date="abc";
                end_date="abc";
            }
             $("#add_coupon_report").dataTable().fnDestroy()
             $('#add_coupon_report').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: $("#url_path").val()+'/admin/add_coupon_report'+"/"+start_date+"/"+end_date,
                        columns: [
                          {data: 'date'  , name: 'date'},
                          {data: 'name'  , name: 'name'},
                          {data: 'code' , name: 'code'},
                          {data: 'rate' , name: 'rate'}
                       ],  
                       dom: 'Bfrtip',
                        buttons: [
                           'excelFlash', 'excel', 'pdf'
                        ]        
                });
        }
        else{
           alert($("#report_not_select").val());
        }
                  if(report_id==1){
                      document.getElementById("coupon_report").style.display="inline-table";
                  }
                  else{
                     document.getElementById("coupon_report").style.display="none";
                  }
                  if(report_id==2){
                      document.getElementById("customer_order_report").style.display="inline-table";
                  }
                  else{
                     document.getElementById("customer_order_report").style.display="none";
                  }
                  if(report_id==3){
                      document.getElementById("product_purchase_report").style.display="inline-table";
                  }
                  else{
                     document.getElementById("product_purchase_report").style.display="none";
                  }
                  if(report_id==4){
                      document.getElementById("product_stock_report").style.display="inline-table";
                  }
                  else{
                     document.getElementById("product_stock_report").style.display="none";
                  }
                  if(report_id==5){
                      document.getElementById("sales_report").style.display="inline-table";
                  }
                  else{
                     document.getElementById("sales_report").style.display="none";
                  }
                  if(report_id==6){
                      document.getElementById("shipping_report").style.display="inline-table";
                  }
                  else{
                     document.getElementById("shipping_report").style.display="none";
                  }
                  if(report_id==7){
                      document.getElementById("tax_report").style.display="inline-table";
                  }
                  else{
                     document.getElementById("tax_report").style.display="none";
                  }
                  if(report_id==8){
                      document.getElementById("add_product_report").style.display="inline-table";
                  }
                  else{
                     document.getElementById("add_product_report").style.display="none";
                  }
                  if(report_id==9){
                      document.getElementById("top_seller_report").style.display="inline-table";
                  }
                  else{
                     document.getElementById("top_seller_report").style.display="none";
                  }
                  if(report_id==10){
                      document.getElementById("add_customer_report").style.display="inline-table";
                  }
                  else{
                     document.getElementById("add_customer_report").style.display="none";
                  }
                  if(report_id==11){
                      document.getElementById("add_coupon_report").style.display="inline-table";
                  }
                  else{
                     document.getElementById("add_coupon_report").style.display="none";
                  }
    }
function play_sound() {
            var source = $("#soundnotify").val();
            var audioElement = document.createElement('audio');
            audioElement.autoplay = true;
            audioElement.load();
            audioElement.addEventListener("load", function() { 
                audioElement.play(); 
            }, true);
            audioElement.src = source;
        }
    $(document).ready(function(){
            function have_notification(){
                $.ajax({
                    url:$("#url_path").val()+"/admin/notification/0",
                    method:"GET",
                    dataType:"json",
                    success:function(resp) {
                        var data = resp.response;
                      
                        if(resp.status == 200){
                            if(data.total > 0){
                                document.getElementById("ordercount").innerHTML=data.total;
                                document.getElementById("notificationmsg").innerHTML=$("#you_have").val()+'  <b>'+data.total+'  </b>'+$("#new_order").val();
                                $('#bell-animation').addClass('icon-anim-pulse');
                                $('.notification-badge').addClass('badge-danger');
                                play_sound();
                               
                            } else{
                                document.getElementById("ordercount").innerHTML=0;
                                document.getElementById("notificationmsg").innerHTML=$("#orders_pending").val();
                                   document.getElementById("notificationshow").style.display="none";
                               
                            }
                        } else {
                             document.getElementById("ordercount").innerHTML=0;
                            document.getElementById("notificationmsg").innerHTML=$("#orders_pending").val();
                            $('#bell-animation').removeClass('icon-anim-pulse');
                            $('.notification-badge').removeClass('badge-danger');
                        }
                    }
                });
            }
            have_notification();

            setInterval(function(){
                have_notification();
            },5000);
        });
      
         function checknotify(){
                $.ajax({
                    url:$("#url_path").val()+"/admin/notification/1",
                    method:"GET",
                    dataType:"json",
                    success:function(resp){
                        var data = resp.response;
                        if(resp.status == 200){
                            $('#notification-data').html(data.data);
                            $('#bell-animation').removeClass('icon-anim-pulse');
                            $('.notification-badge').removeClass('badge-danger');
                        }
                    }
                });       
         }
         
          function demofun(){
            alert('This function is currently disable as it is only a demo website, in your admin it will work perfect');
         }
         function setupemail(){
             alert($("#setemail").val());
         }
         
         function changeboxoption(val,fields){
             if($("#isemailset").val()==0){
                 $("#"+fields).attr("checked",false);
                 setupemail();
             }
         }
        // load new notifications
       
