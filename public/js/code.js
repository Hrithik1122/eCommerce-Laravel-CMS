"use strict"
$(window).load(function() {
    $(".loader").fadeOut("slow");
});


//document.addEventListener('contextmenu', event => event.preventDefault());

$(".cart").on({
    mouseenter: function () {
        console.log($("#modal1").css('display'));
        if($("#modal1").css('display')=="flex"){
            console.log("none");
            $("#modal1").css({"display":"none"});
        }
        else{
            console.log("flex");
            $("#modal1").css({"display":"flex"});
        }
    }
});

function addcomapre(id,field){
  $.ajax({
    url: $("#path").val() + '/addcomapreitem/'+id,
    success: function (data) {
        document.getElementById("totalcompare").innerHTML=data;
        var txt='<div class="col-sm-12"><div class="alert  alert-success alert-dismissible fade show" role="alert">'+$("#compare_add_lang").val()+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div></div>';
        document.getElementById(field).innerHTML=txt;
    }
  });
}

function maxnumber(val){
   var min=$("#minmum_send").val();
   if(parseInt(val)<parseInt(min)){
      alert($("#coupon_vaild_max").val());
      $("#maximum_spend").val();
   }
}

function quickview(val){
  window.location.href=$("#path").val()+"/viewproduct/"+val;
}


function removewishselect(val){
    document.getElementById(val).checked=false;
}
function changehelp(val,id){
    var tab_id = $("#"+val).attr('data-tab');     

    $('ul.tabs li').removeClass('current');
    $('.tab-content').removeClass('current');      
    $("#"+val).addClass('current');
    $("#"+id).addClass('current');
    for(var i=0;i<$("#total-tab").val();i++){     
      $("#"+i).css('background-color',"");      
    }
    $("#"+id).css('background-color', $("#site_color_store").val()+' !important');
}
function changeradio(typename,value,idata,jdata){
   if($("#previousradio"+idata).val()==""){
      $("#previousradio"+idata).val(value);
   }
   else{
       if($("#previousradio"+idata).val()==value){
          $("#customRadio"+idata+jdata).attr('checked', false);
          $("#previousradio"+idata).val("");
       }
       else{
            $("#previousradio"+idata).val(value);
       }
   }
   changetotalamount(typename);
}

function changefilter(type,idata,values){
    if(type==3){
        for(var i=0;i<$("#noprice").val();i++){
            if(idata==i&&document.getElementById("pricesel"+i).value==values){
                if($("#selpriceval").val()==""){
                    document.getElementById("pricesel"+i).checked=true;
                    $("#selpriceval").val(values);
                }else if($("#selpriceval").val()==values){
                    
                    document.getElementById("pricesel"+i).checked=false;
                    $("#selpriceval").val("");
                }else{
                    document.getElementById("pricesel"+i).checked=true;
                    $("#selpriceval").val(values);
                }
               
            }
        }
    }
    if(type==1){
        for(var i=0;i<$("#nosub").val();i++){
            if(idata==i&&document.getElementById("subcategory"+i).value==values){
                if($("#selsubcat").val()==""){
                    document.getElementById("subcategory"+i).checked=true;
                    $("#selsubcat").val(values);
                }else if($("#selsubcat").val()==values){
                   
                    document.getElementById("subcategory"+i).checked=false;
                    $("#selsubcat").val("");
                }else{
                    document.getElementById("subcategory"+i).checked=true;
                    $("#selsubcat").val(values);
                }
               
            }
        }
    }
    if(type==2){
        for(var i=0;i<5;i++){
            if(idata==i&&document.getElementById("ratting"+i).value==values){
                if($("#selratting").val()==""){
                    document.getElementById("ratting"+i).checked=true;
                    $("#selratting").val(values);
                }else if($("#selratting").val()==values){
                   
                    document.getElementById("ratting"+i).checked=false;
                    $("#selratting").val("");
                }else{
                    document.getElementById("ratting"+i).checked=true;
                    $("#selratting").val(values);
                }
               
            }
        }
    }
    if(type==4){
        for(var i=0;i<$("#nobrand").val();i++){
            if(idata==i&&document.getElementById("brand"+i).value==values){
                if($("#selbrand").val()==""){
                    document.getElementById("brand"+i).checked=true;
                    $("#selbrand").val(values);
                }else if($("#selbrand").val()==values){
                   
                    document.getElementById("brand"+i).checked=false;
                    $("#selbrand").val("");
                }else{
                    document.getElementById("brand"+i).checked=true;
                    $("#selbrand").val(values);
                }
               
            }
        }
    }
    if(type==5){
        for(var i=0;i<$("#totalcolor").val();i++){
            if(idata==i&&document.getElementById("customcolor"+i).value==values){
                if($("#selcolor").val()==""){
                    document.getElementById("customcolor"+i).checked=true;
                    $("#selcolor").val(values);
                }else if($("#selcolor").val()==values){
                    document.getElementById("customcolor"+i).checked=false;
                    $("#selcolor").val("");
                }else{
                    document.getElementById("customcolor"+i).checked=true;
                    $("#selcolor").val(values);
                }
               
            }
        }
    }
    if(type==6){
        for(var i=0;i<$("#totalsize").val();i++){
            if(idata==i&&document.getElementById("sizechk"+i).value==values){
                if($("#selsize").val()==""){
                    document.getElementById("sizechk"+i).checked=true;
                    $("#selsize").val(values);
                }else if($("#selsize").val()==values){
                    document.getElementById("sizechk"+i).checked=false;
                    $("#selsize").val("");
                }else{
                    document.getElementById("sizechk"+i).checked=true;
                    $("#selsize").val(values);
                }
               
            }
        }
    }
    
    changeproductlist();
}

$(document).ready(function () {
    $.ajax({
    url: $("#path").val() + '/getallcategory',
    success: function (data) {
          data=JSON.parse(data);
          var selindex="";
          var catindex=$("#select_cate_id").val();
          var ddBasic=[];
          for(var i=0;i<data.length;i++){
              ddBasic.push({ text: data[i].name, value: data[i].id });
              if(catindex==data[i].id){
                  selindex=i;
              }
          }
          if(selindex==""){
              $('#divNoImage').ddslick({
                  data: ddBasic,
                  selectText: $("#All_lang").val()
              });
              $('#divNoImage1').ddslick({
                  data: ddBasic,
                  selectText: $("#All_lang").val()
              });
          }
          else{
               $('#divNoImage').ddslick({
                data: ddBasic,
                defaultSelectedIndex:selindex
            });
            $('#divNoImage1').ddslick({
                data: ddBasic,
                defaultSelectedIndex:selindex
            });
          }
       }
    });
});

$(document).ready(function () {
    $('.td.View a:hover').css({"background-color":"yellow"});
});
$(document).ready(function () {
  var cat=0; 
  if($("input[name='search_cat']").val()){
     cat=$("input[name='search_cat']").val();
  }
  
 
  $.ajax({
    url: $("#path").val() + '/getallsearchproduct',
    data: {id:cat},
    success: function (data) {
      var product = new Array();
      var stringify = JSON.parse(data);
      for (var i = 0; i < stringify.length; i++) {
        product.push(stringify[i]["name"]);
      }
      $("#search_product").autocomplete({
        source: product,
        minLength: 1
      });
      $("#search_product_mobile").autocomplete({
        source: product,
        minLength: 1
      });

    }
  });
});







function changesearcat(){
 $(document).ready(function () {
  var cat=0; 
  if($("input[name='search_cat']").val()!=""){
     cat=$("input[name='search_cat']").val();
  }
 
  $.ajax({
    url: $("#path").val() + '/getallsearchproduct',
    data: {id:cat},
    success: function (data) {
      var product = new Array();
      var stringify = JSON.parse(data);
      for (var i = 0; i < stringify.length; i++) {
        product.push(stringify[i]["name"]);
      }
      $("#search_product").autocomplete({
        source: product,
        minLength: 1
      });
      $("#search_product_mobile").autocomplete({
        source: product,
        minLength: 1
      });

    }
  });
});
  
}
function changeactive(val){
      var totalcolor=$("#totalcolor").val();
      for(var i=0;i<totalcolor;i++){
          $("#changeli"+i).removeClass("active-1");
      }
      $("#changeli"+val).addClass("active-1");
   }

  
      $(".tab_content").hide();
      $(".tab_content:first").show();

   
      
       $('ul.tabs li').last().addClass("tab_last");
    
function changeproducttab(val){
    for(var i=1;i<4;i++){
        $("#reltab"+i).removeClass("active");
        $("#hredtab"+i).removeClass("d_active");
        document.getElementById("tab"+i).style.display="none";
    }
    $("#reltab"+val).addClass("active");
    $("#hredtab"+val).addClass("d_active");
    document.getElementById("tab"+val).style.display="block";
}
 
function registeruser() {
     $(document).ajaxSend(function() {
    $("#overlay").fadeIn(300);　
  });
    $(document).ajaxSend(function() {
    $("#overlaychk").fadeIn(300);　
  });
  var first_name = $("input[name='first_name']").val();
  var email = $("input[name='reg_email']").val();
  var password = $("input[name='reg_password']").val();
  var phone = $("input[name='reg_phone']").val();
  
  var confirm_password = $("input[name='confirm_password']").val();
  if (first_name != ""  && email != "" && password != "" && confirm_password != "") {
    if (password != confirm_password&&phone!="") {
      document.getElementById("reg_error_msg").innerHTML = '<div class="col-sm-12"><div class="alert  alert-danger alert-dismissible fade show" role="alert">Password And Confirm Password Must Be Same<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div></div>';
      document.getElementById("reg_success_msg").style.display = "none";
      document.getElementById("reg_error_msg").style.display = "block";
    } else {
      $.ajax({
        url: $("#path").val() + "/userregister",
        method: "GET",
        data: {
          first_name: first_name,
          email: email,
          password: password,
          phone:phone
        },
        success: function (data) {
          if (data == "done") {
            $("input[name='first_name']").val("");
            $("input[name='reg_phone']").val("");
            $("input[name='reg_email']").val("");
            $("input[name='reg_password']").val("");
            $("input[name='confirm_password']").val("");
            alert($("#user_register").val());
            $("#buzz").removeClass('in show active');
            $('a[href="#buzz"]').removeClass('active');
            $('a[href="#profile"]').addClass('active');
            $("#profile").addClass('in show active');
            document.getElementById("reg_success_msg").style.display = "none";
            document.getElementById("reg_error_msg").style.display = "none";
          } else {
            document.getElementById("reg_error_msg").innerHTML = '<div class="col-sm-12"><div class="alert  alert-danger alert-dismissible fade show" role="alert">'+data+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div></div>';
            document.getElementById("reg_success_msg").style.display = "none";
            document.getElementById("reg_error_msg").style.display = "block";
          }
        }
      });
     
    }
  } else {
     $("#myModal1").scrollTop(0);
    document.getElementById("reg_error_msg").innerHTML = '<div class="col-sm-12"><div class="alert  alert-danger alert-dismissible fade show" role="alert">'+$("#required_field_lang").val()+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div></div>';
    document.getElementById("reg_success_msg").style.display = "none";
    document.getElementById("reg_error_msg").style.display = "block";
  }
   $("#overlay").fadeOut(1000);
   $("#overlaychk").fadeOut(1000);
}

function loginuser(field) {
     $(document).ajaxSend(function() {
    $("#overlay").fadeIn(300);　
  });
    $(document).ajaxSend(function() {
    $("#overlaychk").fadeIn(300);　
  });
  
  var email = $("input[name='" + field + "email']").val();
  var password = $("input[name='" + field + "password']").val();
  if ($("input[name='" + field + "remember']").prop("checked") == true) {
    var rem_me = 1;
  } else {
    var rem_me = 0;
  }
  if (email != "" && password != "") {
    $.ajax({
      url: $("#path").val() + "/userlogin",
      method: "GET",
      data: {
        email: email,
        password: password,
        rem_me: rem_me
      },
      success: function (data) {
        if (data == "done") {
          var url1 = window.location.href;
          var url2 = $("#path").val()+"/home";          
          var n = url1.localeCompare(url2);
          
          if (n == 0) {
           window.location.href = $("#path").val() + "/myaccount";
          } else {
           window.location.reload();
          }
          document.getElementById("" + field + "error_msg").style.display = "none";
        } else {

          document.getElementById("" + field + "error_msg").innerHTML = '<div class="col-sm-12"><div class="alert  alert-danger alert-dismissible fade show" role="alert">'+data+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div></div>';;
          document.getElementById("" + field + "error_msg").style.display = "block";
        }
      }
    });
  } else {
    document.getElementById("" + field + "error_msg").innerHTML =  '<div class="col-sm-12"><div class="alert  alert-danger alert-dismissible fade show" role="alert">Please Enter Required Fields<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div></div>';;
    document.getElementById("" + field + "error_msg").style.display = "block";
  }
  $("#overlay").fadeOut(1000);
  $("#overlaychk").fadeOut(1000);
}

function userlogout() {
  window.location.href = $("#path").val() + "/logout";
}

function changewishlist(id, idata) {
    document.getElementById("wishfavor"+id).style.visibility="hidden";
    document.getElementById("loading"+id).style.visibility="visible";
    
  var user_id = $("#login_user_id").val();
  if (user_id == "") {
    $("#myModal").modal();
    $("#" + idata).prop("checked", false);
  } else {
    if ($("#" + idata).prop("checked") == true) { //insert
      $.ajax({
        url: $("#path").val() + "/storewishlist",
        method: 'GET',
        data: {
          product_id: id,
          user_id: user_id
        },
        success: function (data) {
          document.getElementById("totalwish").innerHTML=data;
          
        }
      });
    } else {
      $.ajax({
        url: $("#path").val() + "/deletewishlist",
        method: 'GET',
        data: {
          product_id: id,
          user_id: user_id
        },
        success: function (data) {
            data=JSON.parse(data);
           document.getElementById("totalwish").innerHTML=data.total;
        }
      });
    }
  }
  setTimeout(function(){
      document.getElementById("wishfavor"+id).style.visibility="visible";
      document.getElementById("loading"+id).style.visibility="hidden";
   }, 500);
 
}

function openregsiter() {
  $("a[href='#profile']").removeClass("active");
  $("a[href='#buzz']").addClass("active");
  $("#profile").removeClass("active");
  $("#buzz").addClass("active show");
}

function resetmodel(){
      $("a[href='#profile']").addClass("active");
      $("a[href='#buzz']").removeClass("active");
      $("#profile").addClass("active");
      $("#buzz").removeClass("active show");
}

function changeship() {
  if ($("#to_ship").prop("checked") == true) {
    document.getElementById("shipping_address").style.display = "block";
  } else {
    document.getElementById("shipping_address").style.display = "none";
  }
}

function orderpayment(pay_type) {
  if ($("#login_user_id").val() != "") {
    var order_firstname = $("#order_firstname").val();
    var order_billing_address = $("#order_billing_address").val();
    var order_billing_city = $("#order_billing_city").val();
    var order_billing_pincode = $("#order_billing_pincode").val();
    var order_phone = $("#order_phone").val();
    var order_email = $("#order_email").val();
    var to_ship;
    if ($("#to_ship").prop("checked") == true) {
      to_ship = 1;
    } else {
      to_ship = 0;
    }
    var order_ship_firstname = $("#order_ship_firstname").val();
    var order_shipping_address = $("#order_shipping_address").val();
    var order_shipping_city = $("#order_shipping_city").val();
    var order_shipping_pincode = $("#order_shipping_pincode").val();
    var order_notes = $("#order_notes").val();
    var couponcode = $("#couponcode").val();
    var couponval = $("#couponval").val();
    var freeshipping = $("#freeshipping").val();
    if (freeshipping == "") {
      freeshipping = 0;
    }
    var shipping_type = $("#shipping_type").val();
    var shipping_charges = $("#shipping_charges").val();
    var total_order_price = $("#total_order_price").val();
    var payment_method = $("#pay_type").val();
    var totaltax = $("#total_tax").val();
    if ((order_firstname != "")  && (order_billing_address != "") && (order_billing_city != "") && (order_billing_pincode != "") && (order_phone != "") && (order_email != "")) {
      if (to_ship == 1 && order_ship_firstname == "" && order_shipping_address == "" && order_shipping_city == "" && order_shipping_pincode == "") {
        alert($("#required_field_lang").val());
      } else {
        var prefix;
        if (pay_type == 1) {
          prefix = "pay_";
          document.getElementById("paypal").style.display = "block";
          document.getElementById("stripe").style.display = "none";
          document.getElementById("cod").style.display = "none";
        } else if (pay_type == 2) {
          prefix = "stri_";
          document.getElementById("paypal").style.display = "none";
          document.getElementById("stripe").style.display = "block";
          document.getElementById("cod").style.display = "none";
        } else {
          prefix = "cod_";
          document.getElementById("paypal").style.display = "none";
          document.getElementById("stripe").style.display = "none";
          document.getElementById("cod").style.display = "block";
        }
        $("#" + prefix + "order_firstname").val(order_firstname);
        $("#" + prefix + "order_billing_address").val(order_billing_address);
        $("#" + prefix + "order_billing_city").val(order_billing_city);
        $("#" + prefix + "order_billing_pincode").val(order_billing_pincode);
        $("#" + prefix + "order_phone").val(order_phone);
        $("#" + prefix + "order_email").val(order_email);
        $("#" + prefix + "order_to_ship").val(to_ship);
        $("#" + prefix + "order_ship_firstname").val(order_ship_firstname);
        $("#" + prefix + "order_shipping_address").val(order_shipping_address);
        $("#" + prefix + "order_shipping_city").val(order_shipping_city);
        $("#" + prefix + "order_shipping_pincode").val(order_shipping_pincode);
        $("#" + prefix + "couponcode").val(couponcode);
        $("#" + prefix + "couponval").val(couponval);
        $("#" + prefix + "freeshipping").val(freeshipping);
        $("#" + prefix + "order_notes").val(order_notes);
        $("#" + prefix + "shipping_type").val(shipping_type);
        $("#" + prefix + "shipping_charges").val(shipping_charges);
        $("#" + prefix + "total_order_price").val(total_order_price);
        $("#" + prefix + "total_taxes").val(totaltax);
        $("#" + prefix + "payment_method").val(pay_type);
      }
    } else {
      alert($("#required_field_lang").val());
      document.getElementById("paypal").style.display = "none";
      document.getElementById("stripe").style.display = "none";
      document.getElementById("cod").style.display = "none";
      $("#payment_method_3").prop("checked", false);
      $("#payment_method_2").prop("checked", false);
      $("#payment_method_1").prop("checked", false);
    }
  } else {
    alert($("#login_account_lang").val());
    $("#payment_method_3").prop("checked", false);
    $("#payment_method_2").prop("checked", false);
    $("#payment_method_1").prop("checked", false);
  }
}

function checkboth(val) {
  var npwd = $("#npwd").val();
  if (npwd != val) {
    alert($("#match_error").val());
    $("#npwd").val("");
    $("#rpwd").val("");
  }
}

function checkcurrentpwd(val) {
  $.ajax({
    url: $("#path").val() + '/samepwd' + "/" + val,
    data: {},
    success: function (data) {
      console.log(data);
      if (data == 0) {
        alert($("#error_current_pwd").val());
        $("#cpwd").val("");
      }
    }
  });
}

function cancelpwd() {
  $("#cpwd").val("");
  $("#npwd").val("");
  $("#rpwd").val("");
}

function changepassword() {
  var npwd = $("input[name='npwd']").val();
  var cpwd = $("input[name='cpwd']").val();
  var pwd=$("#cur_pwd").val();
  if(pwd==1&&cpwd==""){
      alert($("#ent_current_pwd_lang").val());
  }
  else{
         $.ajax({
              url: $("#path").val() + "/changeuserpwd",
              method: "GET",
              data: {
                npwd: npwd,
                cpwd: cpwd,
              },
              success: function (data) {
                $("input[name='npwd']").val("");
                $("input[name='rpwd']").val("");
                $("input[name='cpwd']").val("");
                $('#contact').addClass('active');
                var txt = '<div class="col-sm-12"><div class="alert  alert-success alert-dismissible fade show" role="alert">' + data + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div></div>';
                document.getElementById("msgres").innerHTML = txt;
              }
      });
  }
 
}

function editbilling() {
  var getbill = document.getElementById("billing_address").innerHTML;
  document.getElementById("billing_address").style.display = "none";
  document.getElementById("textbilling").style.display = "block";
}

function editshipping() {
  var getbill = document.getElementById("shipping_address").innerHTML;
  document.getElementById("shipping_address").style.display = "none";
  document.getElementById("textshipping").style.display = "block";
}

function closebill() {
  document.getElementById("billing_address").style.display = "block";
  document.getElementById("textbilling").style.display = "none";
}

function closeship() {
  document.getElementById("shipping_address").style.display = "block";
  document.getElementById("textshipping").style.display = "none";
}

function SaveAddress(val) {
  if (val == "bill") {
    var fields = "billing_address";
    var address = $("#bill").val();
  } else {
    var fields = "shipping_address";
    var address = $("#ship").val();
  }
  $.ajax({
    url: $("#path").val() + "/saveaddress",
    method: "GET",
    data: {
      fields: fields,
      address: address,
    },
    success: function (data) {
      if (val == "bill") {
        document.getElementById("billing_address").innerHTML = address;
        document.getElementById("billing_address").style.display = "block";
        document.getElementById("textbilling").style.display = "none";
      } else {
        document.getElementById("shipping_address").innerHTML = address;
        document.getElementById("shipping_address").style.display = "block";
        document.getElementById("textshipping").style.display = "none";
      }
    }
  });
}

function deletewish(id) {
  var user_id = $("#login_user_id").val();
  $.ajax({
    url: $("#path").val() + '/deletewishlist',
    method: 'GET',
    data: {
      product_id: id,
      user_id: user_id
    },
    success: function (data) {
        data=JSON.parse(data);
     document.getElementById("mywish").innerHTML=data.content;
      document.getElementById("totalwish").innerHTML=data.total;
    }
  });
}

function usqty(idata, op, item_id) {
  var qty = $("#quantity" + idata).val();
  var price = document.getElementById("pricecart" + idata).innerHTML;
  
  if (op == 1) { //plus
    var newqty = parseInt(qty) + 1;
    
    var total = parseFloat(price) * parseFloat(newqty);
   
    $("#quantity" + idata).val(newqty);
    document.getElementById("totalprice" + idata).innerHTML = total.toFixed(2);
    qty = 1;
  }
  if (op == 0) { //min
    if (qty == 1) {
      var newqty = parseInt(qty);
    } else {
      var newqty = parseInt(qty) - 1;
    }
  //  console.log(price);
  //  console.log(newqty);
    var total = parseFloat(price) * parseFloat(newqty);
    $("#quantity" + idata).val(newqty);
    document.getElementById("totalprice" + idata).innerHTML = total.toFixed(2);
  }
  $.ajax({
    url: $("#path").val() + "/updatecartqty",
    method: 'GET',
    data: {
      item_id: item_id,
      qty: op
    },
    success: function (data) {
      document.getElementById("subtotal").innerHTML = data.subtotal;
      var coupon = document.getElementById("couponname").innerHTML;
      if (coupon != "") {
            $.ajax({
              url: $("#path").val() + "/checkcoupon",
              method: 'GET',
              data: {
                coupon: coupon
              },
              success: function (data) {
                if (data != 0) {
                  document.getElementById("coupon_total").style.display = "block";
                  $("#coupon_discount_type").val(data.discount_type);
                  $("#coupon_discount_value").val(data.value);
                  $("#coupon_min_value").val(data.minmum_spend);
                  $("#coupon_max_value").val(data.maximum_spend);
                  $("#freedelivery").val(data.free_shipping);
                  var price = 0.00;
                  if (data.discount_type == 1) {
                    var subtotal = document.getElementById("subtotal").innerHTML;
                    var price = parseFloat(subtotal) * parseFloat(data.value) / parseFloat(100);
        
                  } else {
                    price = parseFloat(value);
                  }
                  document.getElementById("free-delivery").style.display = "none";
                  document.getElementById("couponval").innerHTML = price;
                  document.getElementById("couponname").innerHTML = coupon;
                  var subtotal = document.getElementById("subtotal").innerHTML;
                  var str=0;
                   $.each($("input:radio[name=delivery]:checked"), function () {
                        delivery_ch = $(this).val();
                         var str1 = delivery_ch.split("#");
                         str=str1[1];
                  });
                  
                 
                  if (data.free_shipping == '1') {
                    var charg = 0;
                    document.getElementById("free-delivery").style.display = "block";
                  } else {
                    var charg = str;
        
                  }
                  var addco = parseFloat(subtotal) + parseFloat(charg);
                  var txt=parseFloat(addco) - parseFloat(price);
                //  console.log(addco);
                //  console.log(price);
                  document.getElementById("totalamount").innerHTML = txt.toFixed(2);
                } else {
                  alert($("#invaild_coupon_lang").val());
                  document.getElementById("free-delivery").style.display = "none";
                  document.getElementById("coupon_total").style.display = "none";
                  $("#couponcode").val("");
                }
              }
            });
        }
        else{
               var str=0;
               var charg=0;
                   $.each($("input:radio[name=delivery]:checked"), function () {
                        delivery_ch = $(this).val();
                         var str1 = delivery_ch.split("#");
                         str=str1[1];
                  });
              var coupon = document.getElementById("couponval").innerHTML;
              if (coupon == "") {
                coupon = 0;
              }
              if ($("#freedelivery").val() == 1) {
                var charg = 0;
              } else {
                  if(str[1]===null){
                       var charg = str[1];
                  }
               
              }
              
            //  console.log(data.subtotal);
             //     console.log(charg);
              var addco = parseFloat(data.subtotal) + parseFloat(charg);
             //   console.log(addco);
             //     console.log(price);
              var add=parseFloat(addco) - parseFloat(coupon);
              document.getElementById("totalamount").innerHTML = add.toFixed(2);
        }
    }
  });
}

function Changeradio(val) {
  var subtotal = document.getElementById("subtotal").innerHTML;
  var str = val.split("#");
  var coupon = document.getElementById("couponval").innerHTML;
  if (coupon == "") {
    coupon = 0;
  }
  if ($("#freedelivery").val() == 1) {
    var charg = 0;
  } else {
    var charg = str[1];
  }
  var addco = parseFloat(subtotal) + parseFloat(charg);
  
   var add=parseFloat(addco) - parseFloat(coupon);
              document.getElementById("totalamount").innerHTML = add.toFixed(2);
}

function Checkout() {
  var delivery = "";
  $("input[name='delivery[]']:checked").each(function () {
    delivery = $(this).val();
  });
  var discount_type = $("#coupon_discount_type").val();
  var value = $("#coupon_discount_value").val();
  var free_shipping = $("#freedelivery").val();
  var coupon = document.getElementById("couponval").innerHTML;
  var couponcode = document.getElementById("couponname").innerHTML;

  $("#checkout_delivery").val(delivery);
  $("#checkout_discount_type").val(discount_type);
  $("#checkout_discount_value").val(value);
  $("#checkout_free_shipping").val(free_shipping);
  $("#checkout_coupon_value").val(coupon);
  $("#checkout_couponcode").val(couponcode);
}

function addcoupon() {
  var coupon = $("#couponcode").val();
  if (coupon != "") {
    $.ajax({
      url: $("#path").val() + "/checkcoupon",
      method: 'GET',
      data: {
        coupon: coupon
      },
      success: function (data) {
        if (data != 0) {
          document.getElementById("coupon_total").style.display = "block";
          $("#coupon_discount_type").val(data.discount_type);
          $("#coupon_discount_value").val(data.value);
          $("#coupon_min_value").val(data.minmum_spend);
          $("#coupon_max_value").val(data.maximum_spend);
          $("#freedelivery").val(data.free_shipping);
          var price = 0;
          if (data.discount_type == 1) {
            var subtotal = document.getElementById("subtotal").innerHTML;
            var price = parseFloat(subtotal) * parseFloat(data.value) / parseFloat(100);

          } else {
            price = parseFloat(data.value);
          }
          document.getElementById("free-delivery").style.display = "none";
          document.getElementById("couponval").innerHTML = price;
          document.getElementById("couponname").innerHTML = coupon;
          var subtotal = document.getElementById("subtotal").innerHTML;
          var str=0;
           $.each($("input:radio[name=delivery]:checked"), function () {
                delivery_ch = $(this).val();
                 var str1 = delivery_ch.split("#");
                 str=str1[1];
          });
          
         
          if (data.free_shipping == '1') {
            var charg = 0;
            document.getElementById("free-delivery").style.display = "block";
          } else {
            var charg = str;

          }
          var addco = parseFloat(subtotal) + parseFloat(charg);
          var add=parseFloat(addco) - parseFloat(price);
          document.getElementById("totalamount").innerHTML = add.toFixed();
        } else {
          alert($("#invaild_coupon_lang").val());
          document.getElementById("free-delivery").style.display = "none";
          document.getElementById("coupon_total").style.display = "none";
          $("#couponcode").val("");
        }
      }
    });
  } else {
    alert($("#coupon_req_lang").val());
  }
}

function changeproductlist(sorttype='1') { 
  $("#overlaychk").fadeIn(300);
  console.log("hello");
  var subcategory = 0;
  var brand = 0;
  var price = 0;
  var ratting = 0;
  var color=0;
  var size=0;
  var code=0;
  var search=0;
  if($("#code_search").val()!=""){
      var code=$("#code_search").val();
  }
  if($("#search").val()!=""){
      var search=$("#search").val();
  }
  var discount=$("#discount").val();
  $.each($("input:radio[name=subcategory]:checked"), function () {
    subcategory = $(this).val();
  });
  $.each($("input:radio[name=brand]:checked"), function () {
    brand = $(this).val();
  });
  $.each($("input:radio[name=pricesel]:checked"), function () {
    price = $(this).val();
  });
  $.each($("input:radio[name=ratting]:checked"), function () {
    ratting = $(this).val();
  });
  $.each($("input:radio[name=colorchk]:checked"), function () {
    color = $(this).val();
  });
  $.each($("input:radio[name=sizechk]:checked"), function () {
    size = $(this).val();
  });
  $.ajax({
    url: $("#path").val() + "/changeproductdata",
    method: "POST",
    data: {
      category: $("#categoryid").val(),
      subcategory: subcategory,
      brand: brand,
      price: price,
      ratting: ratting,
      sorttype:sorttype,
      discount:discount,
      color:color,
      size:size,
      code:code,
      search:search
    },
    success: function (data) {
        if(code!="0"){
          document.getElementById("searchme").innerHTML="<b>"+$("#coupon_ds").val()+":</b>"+code;
      }
       if (data.brand.length != 0) { //demo
        var txt = "";
        var temp=0;
        txt = txt + '<div class="brand-check">';
        for (var i = 0; i < data.brand.length; i++) {
          if (brand == data.brand[i]["brand_name"]) {
            txt = txt + '<div class="check-shop"><input type="radio" class="my-checkbox" value="' + data.brand[i]["brand_name"] + '" name="brand" id="brand'+i+'"  onclick="changefilter(4,'+i+',' + "'" + data.brand[i]["brand_name"] + "'" +')" checked><lable>' + data.brand[i]["brand_name"] + '</lable></div>';
            temp=1;
          } else {
            txt = txt + '<div class="check-shop"><input type="radio" class="my-checkbox" value="' + data.brand[i]["brand_name"] + '" name="brand" id="brand'+i+'"  onclick="changefilter(4,'+i+',' + "'" + data.brand[i]["brand_name"] + "'" +')"><lable>' + data.brand[i]["brand_name"] + '</lable></div>';

          }
          if(temp==0){
            $("#brand").prop("checked",false);
        }
       
        }
         txt = txt + '<input type="hidden" id="nobrand" value="'+i+'"/></div>';
        document.getElementById('demo').innerHTML = txt;
      }
      if (data.subcategory.length != 0) { //demo
        var txt = "";
        var temp=0;
        txt = txt + '<div class="brand-check">';
        for (var i = 0; i < data.subcategory.length; i++) {
          if (subcategory == data.subcategory[i]["id"]) {
            txt = txt + '<div class="check-shop"><input type="radio" class="my-checkbox" value="' + data.subcategory[i]["id"] + '" name="subcategory" id="subcategory'+i+'"  onclick="changefilter(1,'+i+',' + "'" + data.subcategory[i]["id"] + "'" +')" checked><lable>' + data.subcategory[i]["name"] + '</lable></div>';
            temp=1;
          } else {
            txt = txt + '<div class="check-shop"><input type="radio" class="my-checkbox" value="' + data.subcategory[i]["id"] + '" name="subcategory" id="subcategory'+i+'" onclick="changefilter(1,'+i+',' + "'" + data.subcategory[i]["id"] + "'" +')"><lable>' + data.subcategory[i]["name"] + '</lable></div>';

          }
        }
        txt = txt + '<input type="hidden" id="nosub" value="'+i+'"/></div>';
        document.getElementById('demo12').innerHTML = txt;
      }

        if (data.price.length != 0&&price==0) { //demo
        var txt = "";
        var temp=0;
        txt = txt + '<div class="brand-check">';
        for (var i = 0; i < data.price.length; i++) {
          if (price ==data.price[i]) {
            txt = txt + '<div class="check-shop"><input type="radio" class="my-checkbox" value="' + data.price[i] + '" name="pricesel" id="pricesel'+i+'"  onclick="changefilter(3,'+i+',' + "'" + data.price[i] + "'" +')" checked><lable>' + data.price[i] + '</lable></div>';
            temp=1;
          } else {
            txt = txt + '<div class="check-shop"><input type="radio" class="my-checkbox" value="' + data.price[i] + '" name="pricesel" id="pricesel'+i+'"  onclick="changefilter(3,'+i+',' + "'" + data.price[i] + "'" +')"><lable>' + data.price[i] + '</lable></div>';

          }

        }
        if(temp==0){
            $("#pricesel").prop("checked",false);
        }
        txt = txt + '<input type="hidden" id="noprice" value="'+i+'"/></div>';
        document.getElementById('demo-2').innerHTML = txt;
      }
      if (data.size.length != 0&&size==0) { //demo
        var txt = "";
        var temp=0;
        txt = txt + '<div class="brand-check">';
        for (var i = 0; i < data.size.length; i++) {
          if (size ==data.size[i]) {
            txt = txt + '<div class="check-shop"><input type="radio" class="my-checkbox" value="' + data.size[i] + '" name="sizechk" id="sizechk'+i+'"   onclick="changefilter(5,'+i+',' + "'" + data.size[i] + "'" +')" checked><lable>' + data.size[i] + '</lable></div>';
            temp=1;
          } else {
            txt = txt + '<div class="check-shop"><input type="radio" class="my-checkbox" value="' + data.size[i] + '" name="sizechk" id="sizechk'+i+'"  onclick="changefilter(5,'+i+',' + "'" + data.size[i] + "'" +')"><lable>' + data.size[i] + '</lable></div>';

          }

        }
        if(temp==0){
            $("#size").prop("checked",false);
        }
        txt = txt + '<input type="hidden" name="totalsize" id="totalsize" value="'+i+'"></div>';
        document.getElementById('demo-size').innerHTML = txt;
      }
     
       if (data.color.length != 0) { //demo
        var txt = "";
        var temp=0;
        txt = txt + '<div class="brand-radio"><ul class="colors colorslist-1 checkboxcolor">';
        for (var i = 0; i < data.color.length; i++) {
          if (color ==data.color[i]) {
            txt = txt + '<li id="changeli'+i+'" onclick="changeactive('+i+')" class="active-1"><input type="radio" id="customcolor'+i+'" name="colorchk" value="'+data.color[i]+'"  class="color-1" style="background:'+data.color[i]+'" onclick="changefilter(5,'+i+',' + "'" + data.color[i] + "'" +')"></li>';
            temp=1;
          } else {
            txt = txt + '<li id="changeli'+i+'" onclick="changeactive('+i+')"><input type="radio" id="customcolor'+i+'" name="colorchk" value="'+data.color[i]+'"  class="color-1" style="background:'+data.color[i]+'" onclick="changefilter(5,'+i+',' + "'" + data.color[i] + "'" +')"></li>';

          }

        }
        txt=txt+'</ul><input type="hidden" name="totalcolor" id="totalcolor" value="'+i+'"></div>'; 
        console.log(txt);      
        document.getElementById('demo-color').innerHTML = txt;
      }
      if(data.size.length==0&&document.getElementById('demo-size')){
          document.getElementById('demo-size').innerHTML ="";
      }
       if(data.color.length==0){
          document.getElementById('demo-color').innerHTML ="";
      }
    
      if (data.product.length != 0) {
        txt = "";
        for (var i = 0; i < data.product.length; i++) {

          var imgpath = $("#path").val() + "/public/upload/product" + "/" + data.product[i]["basic_image"];
           var ppath = $("#path").val() + "/viewproduct/" + data.product[i]["id"];
          if($("#login_user_id").val()!=""){              
            txt = txt + '<div class="pro-1"><div class="product-box"><div class="pro-img"><figure class="preview-image"><a href="'+ppath+'"> <img src="'+imgpath+'" class="img-responsive"></a><div class="preview-image-overlay"><button type="button" onclick="quickview('+data.product[i]["id"]+')">'+$("#quick_view_lang").val()+'</button></div></figure><div class="img-text"><label class="fancy-checkbox"><input type="checkbox" id="checkda' + i + '" name="checkdata" onclick="changewishlist(' + i + ',"checkda' + i + '")"/><big id="wishfavor' + data.product[i]["id"] + '"></big></label><i class="fa fa-spinner loadlconwish" aria-hidden="true" id="loading' + data.product[i]["id"] + '" ></i></label><span>' + data.product[i]["disper"] + '%</span></div></div><div class="text-s-box"><h1 class="h1proname">' + data.product[i]["name"] + '</h1><span class="rating">';
          }else{
            var ids="checkda"+i;
            txt = txt + '<div class="pro-1"><div class="product-box"><div class="pro-img"><figure class="preview-image"><a href="'+ppath+'"> <img src="'+imgpath+'" class="img-responsive"></a><div class="preview-image-overlay"><button type="button" onclick="quickview('+data.product[i]["id"]+')">'+$("#quick_view_lang").val()+'</button></div></figure><div class="img-text"><label class="fancy-checkbox"><input type="checkbox" id="checkda' + i + '" name="checkdata" onclick="removewishselect(' + i + ',"checkda'+i+'")" data-toggle="modal" data-target="#myModal"/><big id="wishfavor' + data.product[i]["id"] + '"></big></label><i class="fa fa-spinner loadlconwish" aria-hidden="true" id="loading' + data.product[i]["id"] + '" ></i><span>' + data.product[i]["disper"] + '%</span></div></div><div class="text-s-box"><h1 class="h1proname">' + data.product[i]["name"] + '</h1><span class="rating">';
          }
          
          for (var k = 0; k < data.product[i]["avgStar"]; k++) {
            txt = txt + '<i class="fa fa-star" aria-hidden="true"  style="color:'+$("#site_color_store").val()+' !important"></i>';
          }
          for (var k = 0; k < (5 - data.product[i]["avgStar"]); k++) {
            txt = txt + '<i class="fa fa-star-o" aria-hidden="true" style="color:'+$("#site_color_store").val()+' !important"></i>';
          }
           var comparepath=$("#path").val()+"/public/Ecommerce/images/compare.png";  
          txt = txt + '</span><span class="review">(' + data.product[i]["total_review"] + ' '+ $("#review_lang").val()+')</span> <span class="compare_icon"><a href="javascript:addcomapre('+data.product[i]["id"]+','+"productfiltercompare"+')"><img src="'+comparepath+'"></a></span><div class="price"><h2>' + $("#currency").val() + data.product[i]["price"] + '</h2><span >' + $("#currency").val() + data.product[i]["MRP"] + '</span><a href="' + ppath + '" style="background-color:'+$("#site_color_store").val()+' !important">'+$("#shop_now_lang").val()+'</a></div></div></div></div>';
        }

        document.getElementById("productlistdata").innerHTML = txt;
        document.getElementById("totalresult").innerHTML="Showing  "+1+"-"+data.product.length+" products of "+data.product.length+" products";
      } else {
        document.getElementById("productlistdata").innerHTML = $("#np_product_lang").val();
        document.getElementById("totalresult").innerHTML="";
      }
     
    }


  });
   $("#overlaychk").fadeOut(1000);
}
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

function deletecartitem(item_id){
     $.ajax({
            url: $("#path").val() + "/deletecartitem"+"/"+item_id,
            method: "GET",
            success: function (data) {
                data=JSON.parse(data);
                document.getElementById("totalcart").innerHTML=data.totalcart;
                document.getElementById("cartview").innerHTML=data.content;
                var url1 = window.location.href;
                var url2 = $("#path").val()+"/cartdetail";  
                var n = url1.localeCompare(url2);
                if(n==0){
                    document.getElementById("mycart").innerHTML=data.mycart;
                    document.getElementById("subtotal").innerHTML=data.cartsubtotal;
                    if(data.displaycart==1){
                       document.getElementById("coupon_section").style.display="block";
                    }
                    else{
                         document.getElementById("coupon_section").style.display="none";
                    } 
                     var coupon = document.getElementById("couponname").innerHTML;
                      if (coupon != "") {
                            $.ajax({
                              url: $("#path").val() + "/checkcoupon",
                              method: 'GET',
                              data: {
                                coupon: coupon
                              },
                              success: function (data) {
                                if (data != 0) {
                                  document.getElementById("coupon_total").style.display = "block";
                                  $("#coupon_discount_type").val(data.discount_type);
                                  $("#coupon_discount_value").val(data.value);
                                  $("#coupon_min_value").val(data.minmum_spend);
                                  $("#coupon_max_value").val(data.maximum_spend);
                                  $("#freedelivery").val(data.free_shipping);
                                  var price = 0;
                                  if (data.discount_type == 1) {
                                    var subtotal = document.getElementById("subtotal").innerHTML;
                                    var price = parseFloat(subtotal) * parseFloat(data.value) / parseFloat(100);
                        
                                  } else {
                                    price = parseFloat(value);
                                  }
                                  document.getElementById("free-delivery").style.display = "none";
                                  document.getElementById("couponval").innerHTML = price;
                                  document.getElementById("couponname").innerHTML = coupon;
                                  var subtotal = document.getElementById("subtotal").innerHTML;
                                  var str=0;
                                   $.each($("input:radio[name=delivery]:checked"), function () {
                                        delivery_ch = $(this).val();
                                         var str1 = delivery_ch.split("#");
                                         str=str1[1];
                                  });
                                  
                                 
                                  if (data.free_shipping == '1') {
                                    var charg = 0;
                                    document.getElementById("free-delivery").style.display = "block";
                                  } else {
                                    var charg = str;
                        
                                  }
                                  var addco = parseFloat(subtotal) + parseFloat(charg);
                                  var add=parseFloat(addco) - parseFloat(price);
          document.getElementById("totalamount").innerHTML = add.toFixed();
                                } else {
                                  alert($("#invaild_coupon_lang").val());
                                  document.getElementById("free-delivery").style.display = "none";
                                  document.getElementById("coupon_total").style.display = "none";
                                  $("#couponcode").val("");
                                }
                              }
                            });
                        }
                        else{
                               var str=0;
                                   $.each($("input:radio[name=delivery]:checked"), function () {
                                        delivery_ch = $(this).val();
                                         var str1 = delivery_ch.split("#");
                                         str=str1[1];
                                  });
                              var coupon = document.getElementById("couponval").innerHTML;
                              if (coupon == "") {
                                coupon = 0;
                              }
                              if ($("#freedelivery").val() == 1) {
                                var charg = 0;
                              } else {
                                var charg = str[1];
                              }
                              var addco = parseFloat(subtotal) + parseFloat(charg);
                              var add=parseFloat(addco) - parseFloat(coupon);
                              document.getElementById("totalamount").innerHTML = add.toFixed(2);
                        }
                      }
                      
                }
          
       });
}
function addwishtocart(id,name,qty,price){
       $.ajax({
            url: $("#path").val() + "/productaddtowish",
            method: "POST",
            data: {
              product_id: id,
              product_name: name,
              qty: qty,
              product_price: price
            },

            success: function (data) {
                     data=JSON.parse(data);
                     document.getElementById("mywish").innerHTML=data.content;
                     document.getElementById("totalwish").innerHTML=data.total;
                      document.getElementById("totalcart").innerHTML=data.totalcart;
                      document.getElementById("cartview").innerHTML=data.cartcontent;
           }
       });
    }
       $(document).ready(function() {
        $(".block__pic").imagezoomsl({
            zoomrange: [1, 1]
        });

    });

function storereview() {
  var ratting = 0;
  var product_id = $("#product_id").val();
  $(".radiobtn:checked").each(function () {
    ratting = $(this).val();
  });
  var review = $("#reviewtext").val();
  $.ajax({
    url: $("#path").val() + "/saveuserreview",
    method: "POST",
    data: {
      product_id: product_id,
      ratting: ratting,
      review: review
    },
    success: function (data) {
      var txt = '<div class="col-sm-12"><div class="alert  alert-success alert-dismissible fade show" role="alert">' + data + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div></div>';
      document.getElementById("msgrev").innerHTML = txt;
      $("#reviewtext").val("");
      $('.radiobtn').prop('checked', false);
    }
  });
}

function addtocart() {
  var label = $("input[name='option_name[]']").map(function () {
    return $(this).val();
  }).get();
  var op_req = $("input[name='option_req[]']").map(function () {
    return $(this).val();
  }).get();
  var option_type = $("input[name='option_type[]']").map(function () {
    return $(this).val();
  }).get();
  var option_sel = [];
  var label_sel = [];
  var price_sel = [];
  for (var i = 0; i < label.length; i++) {
    if (option_type[i] == 1) { //dropdown
      var price = 0;
      $('select[name="option_ls[' + i + '][]"] option:selected').each(function () {
        return price = $(this).val();
      });
      if (op_req[i] == 'required' && price == "") {
        alert(label[i] +" "+$("#is_required_lang").val());
        $("#reqerror").val(1);
        break;
      }
      if (price != "") {
        option_sel.push(label[i]);
        var str = price.split("#");
        label_sel.push(str[0]);
        price_sel.push(str[1]);
      }
    }
    if (option_type[i] == 2) { //checkbox
      var price = [];
      $.each($("input:checkbox[name=option_ls" + i + "]:checked"), function () {
        price.push($(this).val());
      });
      if (op_req[i] == 'required' && price.length == 0) {
        alert(label[i] +" "+ $("#is_required_lang").val());
        $("#reqerror").val(1);
        break;
      }
      if (price.length != 0) {
        for (var j = 0; j < price.length; j++) {
          option_sel.push(label[i]);
          var str = price[j].split("#");
          label_sel.push(str[0]);
          price_sel.push(str[1]);
        }
      }
    }
    if (option_type[i] == 3) { //radio button
      var price = [];
      $.each($("input:radio[name=option_ls" + i + "]:checked"), function () {
        price.push($(this).val());
      });
      console.log(price.length);
      if (op_req[i] == 'required' && price.length == 0) {
        alert(label[i] +" "+ $("#is_required_lang").val());
        $("#reqerror").val(1);
        break;
      }
      if (price.length != 0) {
        for (var j = 0; j < price.length; j++) {
          option_sel.push(label[i]);
          var str = price[j].split("#");
          label_sel.push(str[0]);
          price_sel.push(str[1]);

        }
      }

    }
    if (option_type[i] == 4) { //multiple
      var price = [];
      price = $('#option_ls' + i).val();
      if (op_req[i] == 'required' && price == null) {
        alert(label[i]+" "+$("#is_required_lang").val());
        $("#reqerror").val(1);
        break;
      }
      if (price != null) {
        for (var j = 0; j < price.length; j++) {
          option_sel.push(label[i]);
          var str = price[j].split("#");
          label_sel.push(str[0]);
          price_sel.push(str[1]);
        }
      }
    }


  }
  if ($("#reqerror").val() != 1) {
    var product_id = $("#product_id").val();
    var product_name = $("#productname").val();
    var qty = $("input[name='pro_qty']").val();
    var product_price = document.getElementById("order_price").innerHTML;
    var optionarr = option_sel.toString();
    var labelarr = label_sel.toString();
    var price_sel = price_sel.toString();
    $.ajax({
      url: $("#path").val() + "/productaddtocart",
      method: "GET",
      data: {
        product_id: product_id,
        product_name: product_name,
        qty: qty,
        product_price: product_price,
        optionarr: optionarr,
        labelarr: labelarr,
        price_sel: price_sel
      },

      success: function (data) {
          data=JSON.parse(data);
        var txt = '<div class="col-sm-12"><div class="alert  alert-success alert-dismissible fade show" role="alert">' + $("#cartsuccesslang").val() + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div></div>';
        document.getElementById("msgrev").innerHTML = txt;
        document.getElementById("cartview").innerHTML=data.content;
        document.getElementById("totalcart").innerHTML=data.totalcart;
         setTimeout(function () {
                    document.getElementById("msgrev").innerHTML="";
                 }, 2000);
                 $('input:checkbox').removeAttr('checked');
                 $('input:radio').removeAttr('checked');
        document.getElementById("order_price").innerHTML=$("#product_price").val();
      }
    });

  } else {
    $("#reqerror").val(0);
  }
}

function changeqty(val){
    var qty=parseInt($("#qty-nu").val())+parseInt(val);
    if(qty>=1){
        var qty=parseInt($("#qty-nu").val())+parseInt(val);
        $("#qty-nu").val(qty);
         var price1=document.getElementById("new_price").value;
         var price=parseFloat(price1);
        
         var total=parseFloat(price)*qty;                
         document.getElementById("order_price").innerHTML=total.toFixed(2);
    }
  
}
function changeproprice(qty){
    var price=document.getElementById("new_price").value;
    var total=parseFloat(price)*qty;                
    document.getElementById("order_price").innerHTML=total.toFixed(2);
}
function changetotalamount(typename){
    var product_price=$("#product_price").val();
    var productqty=$("#qty-nu").val();
    var label=$("input[name='option_name[]']").map(function(){return $(this).val();}).get();
    var op_req=$("input[name='option_req[]']").map(function(){return $(this).val();}).get();
    var option_type=$("input[name='option_type[]']").map(function(){return $(this).val();}).get();
    var price_sel=[0];
      for(var i=0;i<label.length;i++){
            if(option_type[i]==1){
                var price="";
                $('select[name="option_ls['+i+'][]"] option:selected').each(function() { return price=$(this).val();});
                  if(price!=""){
                              var str=price.split("#");
                              if(str[1]!=""){
                                price_sel.push(parseFloat(str[1]));
                              }
                            }
                    }
                    if(option_type[i]==2){//checkbox
                      var price=[];
                       $.each($("input:checkbox[name=option_ls"+i+"]:checked"), function(){
                         price.push($(this).val());
                     });
                            if(price.length!=0){
                              for(var j=0;j<price.length;j++){
                                var str=price[j].split("#");
                                if(str[1]!=""){
                                  price_sel.push(parseFloat(str[1]));
                                }                             
                              }
                            }                     
                    }
                    if(option_type[i]==3){//radio button
                         var price=[];
                       $.each($("input:radio[name=option_ls"+i+"]:checked"), function(){
                         price.push($(this).val());
                     });
                            if(price.length!=0){
                              for(var j=0;j<price.length;j++){
                                var str=price[j].split("#");
                                if(str[1]!=""){
                                  price_sel.push(parseFloat(str[1]));
                                }                           
                              }
                            }
                    }
                    if(option_type[i]==4){//multiple
                      var price=[];
                      price=$('#option_ls'+i).val();                           
                            if(price!=null){
                              for(var j=0;j<price.length;j++){
                                var str=price[j].split("#");
                                if(str[1]!=""){
                                  price_sel.push(parseFloat(str[1]));
                                }                           
                              }
                            }
                    }
               }
               const reducer = (accumulator, currentValue) => accumulator + currentValue;
               var exter=price_sel.reduce(reducer);
               var product=parseFloat(exter)+parseFloat(product_price);
               var total=product*parseFloat(productqty);
               console.log("total:-"+total.toFixed(2));
               document.getElementById("order_price").innerHTML=total.toFixed(2);
               $("#new_price").val(parseInt(total));
          }

         
function changesort(val){
   if(val==1){
        $("#sort1").addClass("active-2");
        $("#sort2").removeClass("active-2");
        $("#sort3").removeClass("active-2");
        $("#sort4").removeClass("active-2");
   }
   if(val==2){
        $("#sort1").removeClass("active-2");
        $("#sort2").addClass("active-2");
        $("#sort3").removeClass("active-2");
        $("#sort4").removeClass("active-2");
   }
   if(val==3){
        $("#sort1").removeClass("active-2");
        $("#sort2").removeClass("active-2");
        $("#sort3").addClass("active-2");
        $("#sort4").removeClass("active-2");
   }
   if(val==4){
        $("#sort1").removeClass("active-2");
        $("#sort2").removeClass("active-2");
        $("#sort3").removeClass("active-2");
        $("#sort4").addClass("active-2");
   }
   changeproductlist(val);
}

function forgotmodel(){
  document.getElementById("forgotbody").style.display="block";
  document.getElementById("loginbody").style.display="none";
}

function loginmodel(){
  document.getElementById("loginbody").style.display="block";
  document.getElementById("forgotbody").style.display="none";
}

function forgotpassword(){
   var email = $("input[name='forgot_email']").val();
   if (email != "") {
                $.ajax({
                    url: $("#path").val()+"/forgotpassword",
                    method: "GET",
                    data: {
                        email: email
                    },
                    success: function(data) {
                        if (data == 1) {
                           document.getElementById("forgot_msg").innerHTML='<div class="col-sm-12"><div class="alert  alert-success alert-dismissible fade show" role="alert">'+$("#email_success_lang").val()+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div></div>';
                            document.getElementById("forgot_msg").style.display = "block";
                        } else {
                            document.getElementById("forgot_msg").innerHTML='<div class="col-sm-12"><div class="alert  alert-danger alert-dismissible fade show" role="alert">'+$("#email_not_lang").val()+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div></div>';
                            document.getElementById("forgot_msg").style.display = "block";
                        }
                    }
                });
            } else {
                document.getElementById("forgot_msg").innerHTML='<div class="col-sm-12"><div class="alert  alert-danger alert-dismissible fade show" role="alert"> '+$("#email_req_lang").val()+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div></div>';
                document.getElementById("forgot_msg").style.display = "block";
              
            }
}
 $(document).ready(function () {
  $.ajax({
    url: $("#path").val() + '/getallhelp',
    data: {},
    success: function (data) {
      var product = new Array();
      var stringify = JSON.parse(data);
      for (var i = 0; i < stringify.length; i++) {
        product.push(stringify[i]);
      }
      $("#help-search-bar").autocomplete({
        source: product,
        minLength:1
      });
     
    }
  });
});

 function runScript(e) {
    if (e.keyCode == 13) {
        var tb = document.getElementById("help-search-bar").value;
        $.ajax({
                    url: $("#path").val()+"/gethelpresult",
                    method: "GET",
                    data: {
                        search: tb
                    },
                    success: function(data) {
                      if(data=="0"){
                          alert($("#not_found_lang").val());
                      }
                      else{
                        var str=data.split("-");
                        var totaltab=$("#total-tab").val();
                        if(str[0]!=""){
                           for(var i=0;i<totaltab;i++){
                                var tb1=$("#"+i).attr("data-tab");
                                if(tb1=="tab-"+str[0]){
                                  $("#"+i).addClass("active current");
                                  $("#"+tb1).addClass("current");
                                  $("#"+i).css('background-color', $("#site_color_store").val()+' !important');
                                }
                                else{
                                  $("#"+i).removeClass("active current");
                                   $("#"+tb1).removeClass("current");
                                   $("#"+i).css('background-color',"");
                                }
                           }
                           if(str[1]!=""){
                              var txt=str[0]+str[1];
                               $("#heading"+txt).attr("aria-expanded",true);
                               $("#heading"+txt).removeClass("collapsed");
                               $("#collapse"+txt).addClass("show");
                               
                           }
                        }
                        else{
                           alert($("#not_found_lang").val());
                        }
                      }
                    }
                });
        return false;
    }
}
      AOS.init({
          duration: 1000,
      })
      $(document).ready(function() {
         var owl = $("#owl-demo");
         owl.owlCarousel({
            autoPlay: 4000,
           items : 5, 
           itemsDesktop : [1200,4], 
           itemsDesktopSmall : [991,3],
           itemsTablet: [767,2], 
           itemsMobile : [375,1],
           pagination:false
         });        
      });
      $(document).ready(function() {
         var owl = $("#owl-demo2");
         owl.owlCarousel({
            autoPlay: 4000,
           items : 5, 
           itemsDesktop : [1200,4], 
           itemsDesktopSmall : [991,3],
           itemsTablet: [767,2], 
           itemsMobile : [375,1],
           pagination:false
         });        
      });
            function prevdemo2(){
        var owl = $("#owl-demo2");
          owl.trigger('owl.prev');
      }
      function nextdemo2(){
        var owl = $("#owl-demo2");
         owl.trigger('owl.next');
      }
      function prevowl(){
        var owl = $("#owl-demo");
          owl.trigger('owl.prev');
      }
      function nextowl(){
        var owl = $("#owl-demo");
         owl.trigger('owl.next');
      }
      $(document).ready(function() {
         var owl = $("#owl-demo1");
         owl.owlCarousel({
            autoPlay: 4000,
           items : 5,
           itemsDesktop : [1200,4],
           itemsDesktopSmall : [991,3], 
           itemsTablet: [767,2], 
           itemsMobile :  [375,1],
           pagination:false
         });
      });
      
      function changebox(id,colorvalue){
        
          $.ajax({
              url: $("#path").val() + '/colorchange',
              method:"GET",
              data: {colorvalue:colorvalue,color_id:id},
              success: function (data) {                  
                  window.location.reload();
               }
          });
      }
      function prevdemo1(){
          var owl = $("#owl-demo1");
          owl.trigger('owl.prev');
      }

      function nextdemo1(){
         var owl = $("#owl-demo1");
         owl.trigger('owl.next');
      }
      function changecolorlog(val){
         $('html').attr('class', 'back-color-'+val);
      }
    
     
      
      const modalTriggerButtons = document.querySelectorAll("[data-modal-target]");
      const modals = document.querySelectorAll(".modal");
      const modalCloseButtons = document.querySelectorAll(".modal-close");
      
      modalTriggerButtons.forEach(elem => {
       elem.addEventListener("click", event => toggleModal(event.currentTarget.getAttribute("data-modal-target")));
      });
      modalCloseButtons.forEach(elem => {
       elem.addEventListener("click", event => toggleModal(event.currentTarget.closest(".modal").id));
      });
      modals.forEach(elem => {
       elem.addEventListener("click", event => {
         if(event.currentTarget === event.target) toggleModal(event.currentTarget.id);
       });
      });
      
      // Maybe also close with "Esc"...
      document.addEventListener("keydown", event => {
       if(event.keyCode === 27 && document.querySelector(".modal.modal-show")) {
         toggleModal(document.querySelector(".modal.modal-show").id);
       }
      });
      
      function toggleModal(modalId) {
       const modal = document.getElementById(modalId);
      
       if(getComputedStyle(modal).display==="flex") {
         modal.classList.add("modal-hide");
         setTimeout(() => {
           document.body.style.overflow = "initial"; 
           modal.classList.remove("modal-show", "modal-hide");
           modal.style.display = "none";      
         }, 200);
       }
       else {
         document.body.style.overflow = "hidden"; 
         modal.style.display = "flex";
         modal.classList.add("modal-show");
       }
      }
 
function changetab(val,id){
    var tab_id = $("#"+val).attr('data-tab');      
    $('ul.tabs li').removeClass('current');
    $('.tab-content').removeClass('current');      
    $("#"+val).addClass('current');
    $("#litab"+id).addClass('current');
    for(var i=1;i<6;i++){
      console.log();
      $("#litab"+i).css('background-color',"");      
    }
    $("#litab"+id).css('background-color', $("#site_color_store").val()+' !important');
}

function removecomareitem(id){
       $.ajax({
                url: $("#path").val() + '/deletecomapreitem/'+id,
                success: function (data) {
                    window.location.reload();
                }
      });
}

function addcomparetocart(id,name,qty,price){
      $.ajax({
            url: $("#path").val() + "/productaddtowish",
            method: "POST",
            data: {
              product_id: id,
              product_name: name,
              qty: qty,
              product_price: price
            },

            success: function (data) {
              removecomareitem(id);
           }
       });
    }


 
