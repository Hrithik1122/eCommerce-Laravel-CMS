<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/schedule', function() {
    Artisan::call('schedule:run');
    return "schedule done";
});
Route::get('config-cache', function() {
    $image_path = __DIR__."/bootstrap/cache/config.php";
        if(file_exists($image_path)) {
            try {
                    unlink($image_path);
            }catch(Exception $e) {
                              
            }                        
       }
    Artisan::call('config:cache');
     echo "php clear cache";
});
/*Route::get('/', function() {
    Artisan::call('config:cache');
  return redirect('home');
});*/


Route::get('admin', function() {
     return redirect('login');
});
    

Route::get("privacy_policy","Admincontroller@privacy");

Route::get("adddefaultreview","FrontController@adddefaultreview");
route::get("demo","FrontController@demodesign");
Route::get("productmod","FrontController@productmod");
Route::get("getallcategory","FrontController@getallcategory");
Route::get("colorchange","FrontController@colorchange");
Route::get("confirmregister/{id}","UserController@confirmregister")->name("confirmregister");
Route::get("document","Admincontroller@showdoc");
Route::get("addproductdoc","Admincontroller@adddoc");
Route::get("addproductstep","Admincontroller@addproductstep");
Route::get("mainofferdoc","Admincontroller@mainofferdoc");
Route::get("normalofferdoc","Admincontroller@normalofferdoc");
Route::get("dealofferdoc","Admincontroller@dealofferdoc");
Route::group(['prefix' => '/'], function (){
     Route::get("compare","FrontController@getcompare");
     Route::get('auth/{driver}', 'Auth\FacebookController@redirectToProvider')->name('social.oauth');
     Route::get("getallhelp","QuestionSupportController@getallhelp");
     Route::get('auth/{driver}/callback', 'Auth\FacebookController@handleProviderCallback')->name('social.callback');
     Route::get("Offers","FrontController@showoffers");
     Route::post("newsletter","FrontController@newsletter");
     Route::get("gethelpresult","FrontController@gethelpresult");
     Route::get("forgotpassword","FrontController@forgotpassword");
     Route::get("resetpassword/{code}","FrontController@resetpassword");
     Route::any("resetnewpwd","FrontController@resetnewpwd");
     Route::get("confirmregister/{id}","UserController@confirmregister")->name("confirmregister");
     Route::get("/","FrontController@home");
     Route::get("getallproduct","ProductController@getallproduct")->name("getallproduct");
     Route::get('helpsupport',"FrontController@help");
     Route::get("termscondition","FrontController@termscondition");
     Route::get("aboutus","FrontController@aboutus");
     Route::get("contactus","FrontController@contactus");
     Route::post("storecontact","FrontController@storecontact")->name("storecontact");
     Route::get("viewproduct/{id}","FrontController@viewproduct")->name("viewproduct");
     Route::get("getorderjson","OrderController@getorderjson")->name("getorderjson");
     Route::post("searchproduct","FrontController@searchproduct")->name("searchproduct");
     Route::get("productlist/{category_id}/{subcategory_id}/{brand_id}","FrontController@productlist")->name("productlist");
     Route::get("userlogin","UserController@userlogin")->name("userlogin");
     Route::get("userregister","UserController@userregister")->name("userregister");
     Route::any("productaddtocart","CartController@addtocart")->name("addtocart");
     Route::post("productaddtowish","CartController@addtowish")->name("addtowish");
     Route::get("getcartview","CartController@getcartview")->name("getcartview");
     Route::get("cartdetail","CartController@cartdetail")->name("cartdetail");
     Route::get("deletecartitem/{id}","CartController@deletecartitem")->name("deletecartitem");
     Route::get("updatecartqty","CartController@updatecartqty")->name("updatecartqty");
     Route::get("checkcoupon","CouponController@checkcoupon");
     Route::any("checkout","FrontController@checkout")->name("checkout");
     Route::any("changeproductdata","productfilterController@changeproductdata");
     Route::get("productslist/{category}/{subcategory}/{brand}/{discount}","productfilterController@productls");
     Route::get("getallsearchproduct","ProductController@getallsearchproduct");
     Route::get("addcomapreitem/{id}","FrontController@addcomapreitem");
     Route::get("deletecomapreitem/{id}","FrontController@deletecomapreitem");
     Route::group(['middleware' => ['usercheckexiste']], function () {
          Route::get("myaccount","FrontController@myaccount")->name("myaccount");
          Route::get("logout","Admincontroller@showuserlogout")->name("logout");
          Route::get("samepwd/{id}","Admincontroller@check_user_password_same");
          Route::get("changeuserpwd","Admincontroller@changeuserpwd")->name("changeuserpwd");
          Route::post("updateuserprofile","Admincontroller@updateuserprofile")->name("updateuserprofile");
          Route::post("saveuserreview","UserController@saveuserreview");
          Route::get("storewishlist","UserController@storewishlist");
          Route::get("mywishlist","FrontController@mywishlist");
          Route::get("deletewishlist","UserController@deletewishlist");
          Route::get("saveaddress","UserController@saveaddress");
          Route::get("vieworder/{id}","FrontController@vieworder");
          Route::post("cashorder","OrderController@cashorder");
          Route::post("paywithpaypal","PaypalController@postPaymentWithpaypal");
          Route::post('paypal', array('as' => 'paypal','uses' => 'PaypalController@postPaymentWithpaypal',));
          Route::get('paypal', array('as' => 'status','uses' => 'PaypalController@getPaymentStatus',));

        
     });
});

 Route::get("login","Admincontroller@showlogin")->name("showlogin");
Route::group(['prefix' => 'admin'], function () {

   
    Route::post("postlogin","Admincontroller@postlogin")->name("postlogin");

    Route::group(['middleware' => ['admincheckexiste']], function () {

      Route::get("dashboard","Admincontroller@showdashboard")->name("dashboard");
      //logout
      Route::get("logout","Admincontroller@showlogout")->name("logout");

      //start profile 
      Route::get("editprofile","Admincontroller@editprofile")->name("editprofile")->middleware('admincheckexiste');
      Route::post("updateprofile","Admincontroller@updateprofile")->name("updateprofile")->middleware('admincheckexiste');
      //end profile
       
      Route::get("checktotalproduct","ProductController@checktotalproduct");
      //password change 
      Route::get("changepassword","Admincontroller@changepassword")->name("changepassword")->middleware('admincheckexiste');
      Route::get("samepwd/{id}","Admincontroller@check_password_same");
      Route::post("updatepassword","Admincontroller@updatepassword");

      Route::get("contact","QuestionSupportController@contactindex");

      Route::get("notification","NotificationController@index");
      Route::get("notificationTable","NotificationController@notificationTable");
      Route::post("sendnotification","NotificationController@addsendnotification");
      //end password change

      //categories
      Route::get("category","Categorycontroller@index")->name("category");
      Route::get("categorydatatable","Categorycontroller@categorydatatable")->name('categorydatatable');
      Route::post("addcategory","Categorycontroller@addcategory")->name("addcategory");
      Route::get("getcategorybyid/{id}","Categorycontroller@getcategorybyid")->name("getcategorybyid");
      Route::post("updatecategory","Categorycontroller@updatecategory")->name("updatecategory");
      Route::get("sepical_category","Categorycontroller@sepical_category")->name("sepical_category");
      Route::get("sepicalcategorytable","Categorycontroller@sepicalcategorytable")->name("sepicalcategorytable");
      Route::get("addsepicalcategory","Categorycontroller@addsepicalcategory")->name("addsepicalcategory");
      Route::post("storesepicalcategory","Categorycontroller@storesepicalcategory")->name("storesepicalcategory");
      Route::get("editsepicalcategory/{id}","Categorycontroller@editsepicalcategory")->name("editsepicalcategory");
      Route::post("updatesepicalcategory","Categorycontroller@updatesepicalcategory")->name("updatesepicalcategory");
      Route::get("sepicalchange/{id}","Categorycontroller@sepicalchange")->name("sepicalchange");
      Route::get("deletecategory/{id}","Categorycontroller@deletecategory")->name("deletecategory");
      Route::get("deletebrand/{id}","Categorycontroller@deletebrand")->name("deletebrand");

        //sub category
        Route::get("subcategory/{id}","Categorycontroller@subindex")->name("subcategory");
        Route::get("subcategorydatatable/{id}","Categorycontroller@subdatatable")->name("subcategorydatatable");
        Route::post("subaddcategory","Categorycontroller@subaddcategory")->name("subaddcategory");

        Route::get("brand/{id}","Categorycontroller@brandindex")->name("brand");
        Route::get("branddatatable/{id}","Categorycontroller@branddatatable")->name('branddatatable');
        Route::post("addbrand","Categorycontroller@addbrand")->name("addbrand");
        Route::get("getbrandbyname/{id}","Categorycontroller@getbrandbyname")->name("getbrandbyid");
        Route::post("updatebrand","Categorycontroller@updatebrand")->name("updatebrand");
        Route::get("viewscategory","Categorycontroller@viewcategory")->name("viewcategory");
        Route::get("getallsubcategory","Categorycontroller@getallsubcategory");
        Route::get("product","ProductController@showproduct")->name('product');
        Route::get("productdatatable","ProductController@productdatatable")->name("productdatatable");
        Route::get("addcatalog","ProductController@showaddcatalog")->name("addcatalog");
        Route::get("getsubcategory/{id}","ProductController@getsubcategory")->name("getsubcategory");
        Route::get("getbrandbyid/{id}","ProductController@getbrandbyid")->name("getbrandbyid");
        Route::post("saveproduct","ProductController@saveproduct")->name("saveproduct");
        Route::get("changeproductstatus/{id}","ProductController@changeproductstatus");
        Route::post("saveprice","ProductController@saveprice")->name("saveprice");
        Route::post("saveinventory","ProductController@saveinventory")->name("saveinventory");
        Route::post("saveproductimage","ProductController@saveproductimage")->name("#saveproductimage");
         Route::post("saveseoinfo","ProductController@saveseoinfo")->name("saveseoinfo");
         Route::get("getoptionvalues/{id}","ProductController@getoptionvalues")->name("getoptionvalues");
         Route::post("productuploadimg","ProductController@productuploadimg")->name("productuploadimg");
         Route::get("getallproduct","ProductController@getallproduct")->name("getallproduct");
         Route::post("updateproduct","ProductController@updateproduct")->name("updateproduct");
         Route::post("saverealtedprice","ProductController@saverealtedprice")->name("saverealtedprice");


     Route::get("savecatalog/{id}/{tab}","ProductController@showaddcatalog")->name("addcatalog");
     
       Route::get("editproduct/{id}","ProductController@editproduct")->name("editproduct");
       Route::post("saveadditionalinfo","ProductController@saveadditionalinfo")->name("saveadditionalinfo");
       Route::get("getattibutevalue/{id}","ProductController@getattibutevalue")->name("getattibutevalue");
       Route::get("getattributedata","ProductController@getattributedata")->name("getattributedata");

       route::post("saveproductattibute","ProductController@saveproductattibute")->name("saveproductattibute");
       Route::post("saveproductoption","ProductController@saveproductoption")->name("saveproductoption");
       Route::post("saverelatedproduct","ProductController@saverelatedproduct")->name("saverelatedproduct");
       Route::post("saveupsellproduct","ProductController@saveupsellproduct")->name("saveupsellproduct");
       Route::post("savecrosssellproduct","ProductController@savecrosssellproduct")->name("savecrosssellproduct");
       Route::get("getproductprice/{id}","ProductController@getproductprice")->name("getproductprice");
       Route::get("userdelete/{id}","UserController@userdelete");

        Route::get("attributeset","ProductController@showattset")->name('attributeset');
        Route::get("AttributeSetdatatable","ProductController@AttributeSetdatatable")->name("AttributeSetdatatable");
        Route::post("addattrset","ProductController@addattrset")->name("addattrset");
        route::get("getattrsetbyid/{id}","ProductController@getattrsetbyid")->name("getattrsetbyid");
        Route::post("updateattrset","ProductController@updateattrset")->name("updateattrset");
        Route::get("options","ProductController@indexoption");
        Route::get("Optiondatatable","ProductController@Optiondatatable")->name("Optiondatatable");
        Route::get("addoption","ProductController@showaddoption")->name("addoption");
        Route::post("saveoption","ProductController@saveoption")->name("saveoption");
        Route::get("editoption/{id}","ProductController@editoption")->name("editoption");
        Route::post("updateoption","ProductController@updateoption")->name("updateoption");

        Route::get("attribute","ProductController@showattribute")->name("attribute");
        Route::get("attributedatatable","ProductController@attributedatatable")->name("attributedatatable");
        Route::get("addattribute","ProductController@showaddattribute")->name("showaddattribute");   
        route::post("saveattribute","ProductController@saveattribute")->name("saveattribute");
        Route::get("editattribute/{id}","ProductController@editattribute")->name("editattribute");  
        Route::post("updateattribute","ProductController@updateattribute")->name("updateattribute"); 

       Route::get("review","ProductController@showreview");
       Route::get("reviewdatatable/{id}","ProductController@reviewdatatable")->name("reviewdatatable");
       Route::get("changereview/{id}","ProductController@changereview")->name("changereview");

       Route::post("productimagesection","ProductController@productimage")->name("productimagesection");
       Route::get("productlist/{id}/{pro_id}","ProductController@productlist")->name("productlist");
       Route::get("deletecatlog/{id}","ProductController@deletecatlog")->name("deletecatlog");
       Route::get("deleteoption/{id}","ProductController@deleteoption")->name("deleteoption");
       Route::get("deleteattset/{id}","ProductController@deleteattset")->name("deleteattset");
       Route::get("deleteattribute/{id}","ProductController@deleteattribute")->name("deleteattribute");
       Route::get("deletereview/{id}","ProductController@deletereview")->name("deletereview");


       Route::get("banner","BannerController@showbanner")->name("showbanner");
       Route::post("bannerupload","BannerController@bannerupload")->name("bannerupload");
       Route::post("updatebanner","BannerController@updatebanner")->name("updatebanner");

       Route::get("offer","OfferController@showoffer")->name("showoffer");
       Route::get("addoffer","OfferController@showaddoffer")->name("addoffer");
       Route::get("getofferon/{id}","OfferController@getofferon")->name("getofferon");
       Route::get("offerdatatable/{id}","OfferController@offerdatatable")->name("offerdatatable");
       Route::post("storeoffer","OfferController@storeoffer")->name("storeoffer");
       Route::get("sensonal_offer","OfferController@showsensonaloffer")->name("sensonal_offer");
       Route::get("sensonaldatatable","OfferController@sensonaldatatable")->name("sensonaldatatable");
       Route::get("add_sensonal_offer","OfferController@addsensonal")->name("add_sensonal_offer");
       Route::post("storesensonal","OfferController@storesensonal")->name("storesensonal");
       Route::get("changespeofferstatus/{id}","OfferController@changeoffer")->name("changespeofferstatus");
        Route::get("addoffersection/{id}","OfferController@addoffersection")->name("addoffersection");
        Route::post("storeofferdata","OfferController@storeofferdata")->name("storeofferdata");
        Route::get("deleteoffer/{id}","OfferController@deleteoffer")->name("deleteoffer");
        Route::get("deletesensonaloffer/{id}","OfferController@deletesensonaloffer")->name("deletesensonaloffer");
        Route::get("deals","OfferController@deals")->name("deals");
        Route::get("editdeal/{id}","OfferController@editdeal")->name("editdeal");
        Route::get("dealdatatable","OfferController@dealdatatable")->name("dealdatatable");
        Route::get("getofferfordeal/{deal_id}","OfferController@getofferfordeal")->name("getofferfordeal");
        Route::get("updatedeal/{deal_id}/{offer_id}","OfferController@updatedeal")->name("updatedeal");
        Route::get("editoffer/{id}","OfferController@editoffer");
        Route::post("updateofferdata","OfferController@updateofferdata")->name("updateofferdata");
        Route::get("normaloffer","OfferController@shownormaloffer");

        Route::get("coupon","CouponController@index");
        Route::get("coupondatatable","CouponController@coupondatatable")->name("coupondatatable");
        Route::get("addcoupon","CouponController@addcoupon")->name("addcoupon");
        Route::post("savecoupon","CouponController@savecoupon")->name("savecoupon");
        Route::get("deletecoupon/{id}","CouponController@deletecoupon");
        Route::post("savecouponsecondstep","CouponController@savecouponsecondstep")->name("savecouponsecondstep");
        Route::post("savecouponstepthree","CouponController@savecouponstepthree")->name("savecouponstepthree");
        Route::get("editcoupon/{id}","CouponController@editcoupon")->name("editcoupon");

        Route::get("user","UserController@index")->name("user");
        Route::get("userdatatable/{id}","UserController@userdatatable")->name("userdatatable");
        Route::post("adduser","UserController@adduser")->name("adduser");
        Route::get("changeuserstatus/{id}","UserController@changestatus")->name("changeuserstatus");
        Route::get("edituser/{id}","UserController@edituser")->name("edituser");
        Route::post("updateuser","UserController@updateuser")->name("updateuser");
        Route::get("userrole","UserController@userrole")->name("userrole");
        Route::get("roletable","UserController@roletable")->name("roletable");
        Route::get("admin","UserController@indexadmin")->name("admin");

        Route::get("pages","SettingController@indexpage")->name("page");
        Route::get("pagedatatable","SettingController@pagedatatable")->name("pagedatatable");

        Route::get("editpage/{id}","SettingController@editpage")->name("editpage");
        Route::post("updatepage","SettingController@updatepage")->name("updatepage");
        Route::post("savesoicalsetting","SettingController@savesoicalsetting")->name("savesoicalsetting");
        Route::get("shipping","SettingController@showshipping")->name("shipping");
        Route::get("shippingdatatable","SettingController@shippingdatatable")->name("shippingdatatable");
        Route::get("changeshipping_status/{id}","SettingController@changeshipping")->name("changeshipping_status");
        Route::get("editshipping/{id}","SettingController@editshipping")->name("editshipping");
        Route::post("updateshipping","SettingController@updateshipping")->name("updateshipping");
        Route::post("savepaymentdata","SettingController@savepaymentdata")->name("savepaymentdata");

        Route::get("taxes","TaxesController@showtaxes")->name("taxes");
        Route::get("taxesdatatable","TaxesController@taxesdatatable")->name("taxesdatatable");
        Route::get("addtaxes","TaxesController@addtaxes")->name("addtaxes");
        Route::post("storetaxes","TaxesController@storetaxes")->name("storetaxes");
        Route::get("edittaxes/{id}","TaxesController@edittaxes")->name("edittaxes");
        Route::post("updatetaxdata","TaxesController@updatetaxdata")->name("updatetaxdata");

        Route::get("translations","TaxesController@showtranslations")->name("translations");  
        Route::get("translationdatatable","TaxesController@translationdatatable")->name("translationdatatable");
        Route::get("getdatatranslation/{id}","TaxesController@getdatatranslation")->name("getdatatranslation");
        Route::post("updatetranslation","TaxesController@updatetranslation")->name("updatetranslation");
 
        Route::get("setting","SettingController@editsetting")->name("setting");
        Route::get("getcountrylist","SettingController@getcountrylist")->name("getcountrylist");
        Route::get("getlanglist","SettingController@getlanglist")->name("getlanglist");
        Route::post("updatesetting","SettingController@updatesetting")->name("updatesetting");
        route::post("savegeneralsetting","SettingController@updatesetting")->name("savegeneralsetting");
          
        Route::get("order","OrderController@showorder")->name("order");
        Route::get("orderdatatable","OrderController@orderdatatable")->name("orderdatatable");

        Route::get("vieworder/{id}","OrderController@vieworder")->name("vieworder");
        Route::get("generateorderpdf/{id}","OrderController@generateorderpdf")->name("generateorderpdf");
        Route::get("transactionorder","OrderController@showtransactionorder");
        Route::get("transactiondatatable","OrderController@transactiondatatable");
        Route::get("changeorderstatus/{order_id}/{status_id}","OrderController@changeorderstatus")->name("changeorderstatus");

        Route::get("report","ReportController@index");
        Route::get("couponreport/{start_date}/{end_date}/{order_status}/{code}","ReportController@couponreport");
        Route::get("customer_order_report/{start_date}/{order_date}/{order_status}/{name}/{email}","ReportController@customerOrder");
        Route::get("product_purchase_report/{start_date}/{order_date}/{order_status}/{product}/{sku}","ReportController@product_purchase_report");
        route::get("add_coupon_report/{start_date}/{end_date}","ReportController@add_coupon_report");
        Route::get("add_customer_report/{start_date}/{end_date}","ReportController@add_customer_report");
        Route::get("add_product_report/{start_date}/{end_date}","ReportController@add_product_report");
        Route::get("tax_report/{start_date}/{end_date}/{tax_name}","ReportController@tax_report");
        Route::get("shipping_report/{start_date}/{end_date}/{shipping_method}","ReportController@shipping_report");
       Route::get("sales_report/{start_date}/{end_date}/{order_status}","ReportController@sales_report");
       Route::get("product_stock_report/{product}/{sku}/{stock}","ReportController@product_stock_report");
       Route::get("top_seller_report/{start_date}/{end_date}","ReportController@top_seller_report");

        Route::get("latestorder","OrderController@latestorder")->name("latestorder");
        Route::get("latestreview","OrderController@latestreview")->name("latestreview");
        Route::post("sendordermail","OrderController@sendordermail")->name("sendordermail");

        Route::get("featureproduct","FeatureProductController@index")->name("featureproduct");
        Route::get("featureproductdatatable","FeatureProductController@featureproductdatatable")->name("featureproductdatatable");
        Route::get("deletefeature/{id}","FeatureProductController@deletefeature")->name("deletefeature");
        Route::post("addfeatureproduct","FeatureProductController@addfeatureproduct")->name("addfeatureproduct");

        Route::get("support/{id}","QuestionSupportController@helpindex");
        Route::get("topicdatatable/{id}","QuestionSupportController@topicdatatable");
        Route::post("addsupporttopic","QuestionSupportController@addsupporttopic");
        Route::get("questionans/{support_id}/{topic_id}","QuestionSupportController@questionansindex");
        Route::post("addquesans","QuestionSupportController@addquesans");
        Route::get("quesdatatable/{topic_id}","QuestionSupportController@quesdatatable");
        Route::get("editsupport/{id}","QuestionSupportController@editsupport");
        Route::post("updatetopic","QuestionSupportController@updatetopic");
        Route::get("deletesupport/{id}","QuestionSupportController@deletesupport");
        Route::get("editques/{id}","QuestionSupportController@editques");
        Route::post("updatequestion","QuestionSupportController@updatequestion");
        Route::get("deletequestion/{id}","QuestionSupportController@deletequestion");
        Route::get("contactdatatable","QuestionSupportController@contactdatatable");
        Route::get("deletecontact/{id}","QuestionSupportController@deletecontact");


        Route::resource("complain","ComplainController");
        Route::get("complaindatatable","ComplainController@complaindatatable");

        Route::get("notification/{id}","OrderController@notification");
        Route::get("getcoupondata/{id}","OfferController@getcoupondata");

        Route::get("serverkey/{id}","SettingController@serverkey");
        Route::post("updatekey","SettingController@updatekey");

        Route::get("datatable","ComplainController@datatabletest");
        Route::get("changesettingstatus/{field}","SettingController@changesettingstatus");
        
        Route::get("news","BannerController@shownews");
        Route::post("sennews","BannerController@sendnews");
     
    });

});
