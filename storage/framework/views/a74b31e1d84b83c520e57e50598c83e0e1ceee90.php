<?php ini_set("allow_url_fopen","1");
?>
<!doctype html>
<html class="no-js" lang="en">
   <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title><?php echo e(__('messages.site_name')); ?></title>
      <meta name="description" content="<?php echo e(__('messages.site_name')); ?>">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="apple-touch-icon" href="apple-icon.png">
      <link rel="shortcut icon" href="<?php echo e(asset('public/favicon.png')); ?>">
      <link rel="stylesheet" href="<?php echo e(asset('public/admin/vendors/bootstrap/dist/css/bootstrap.min.css')); ?>">
      <link rel="stylesheet" href="<?php echo e(asset('public/admin/vendors/font-awesome/css/font-awesome.min.css')); ?>">
      <link rel="stylesheet" href="<?php echo e(asset('public/admin/vendors/themify-icons/css/themify-icons.css')); ?>">
      <link rel="stylesheet" href="<?php echo e(asset('public/admin/vendors/flag-icon-css/css/flag-icon.min.css')); ?>">
      <link rel="stylesheet" href="<?php echo e(asset('public/admin/vendors/selectFX/css/cs-skin-elastic.css')); ?>">
      <link rel="stylesheet" href="<?php echo e(asset('public/admin/vendors/jqvmap/dist/jqvmap.min.css')); ?>">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
      <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.2/jquery.min.js'></script>
      <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js'></script>
      <script src='https://cdn.jsdelivr.net/bootstrap.tagsinput/0.8.0/bootstrap-tagsinput.min.js'></script>
      <link rel='stylesheet' href='https://cdn.jsdelivr.net/bootstrap.tagsinput/0.8.0/bootstrap-tagsinput.css'>
      <input type="hidden" id="url_path" value="<?php echo e(url('/')); ?>">
      <script src="<?php echo e(asset('public/uikit/tests/js/test.js')); ?>"></script>
       <?php if(Session::get("is_rtl")==0): ?>
      <link rel="stylesheet" href="<?php echo e(asset('public/admin/assets/css/style.css').'?v=24'); ?>">
      <?php else: ?>
       <link rel="stylesheet" href="<?php echo e(asset('public/admin/assets/css/Rtl.css').'?v=2'); ?>">
      <?php endif; ?>
     
      <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>
      <link rel="stylesheet" href="<?php echo e(asset('public/admin/vendors/chosen/chosen.min.css')); ?>">
      <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.3/css/selectize.default.min.css'>
      <script>UPLOADCARE_PUBLIC_KEY = '7b7f57c2b9e95d9770ed';</script>
      
      <script type="text/javascript" src="<?php echo e(asset('public/ckeditor/ckeditor.js')); ?>"></script>
      <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
      <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
     
      <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/css/bootstrap-colorpicker.min.css" rel="stylesheet">
      
   </head>
   <style type="text/css">
   .product-heading h1:before{
      background-color:<?= Session::get('site_color') ?> !important;
   }
   .product-heading h1:after{
      background-color:<?= Session::get('site_color') ?> !important;
   }
   .one-product-slider h1:after{
      background-color:<?= Session::get('site_color') ?> !important;
   }
   .col-md-3.services:after{
      background-color:<?= Session::get('site_color') ?> !important;
   }
   
</style>


   <body class="rtl">
       <div id="overlaychk">
                        <div class="cv-spinner">
                           <span class="spinner"></span>
                        </div>
                     </div>
      <div class="go">
      <aside id="left-panel" class="left-panel">
         <nav class="navbar navbar-expand-sm navbar-default">
            <div class="navbar-header">
               <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
               <i class="fa fa-bars"></i>
               </button>
               <a class="navbar-brand" href="<?php echo e(url('admin/dashboard')); ?>">
               <?php echo e(__('messages.site_name')); ?>

               </a>
               <a class="navbar-brand hidden" href="<?php echo e(url('admin/dashboard')); ?>">    
               <?php echo e(__('messages.short_code')); ?>

               </a>
            </div>
            <div id="main-menu" class="main-menu collapse">
               <ul class="nav navbar-nav">
                  <li class="active">
                     <a href="<?php echo e(url('admin/dashboard')); ?>">
                     <i class="menu-icon fa fa-dashboard"></i>
                     <?php echo e(__('messages.dashboard')); ?>

                     </a>
                  </li>
                  <h3 class="menu-title"></h3>
                  <li class="active">
                     <a href="<?php echo e(url('admin/order')); ?>"  aria-haspopup="true" aria-expanded="false"> 
                         <i class="menu-icon  fa fa-dollar"></i>
                         <?php echo e(__('messages.sales')); ?>

                     </a>                     
                  </li>                 
                  <li class="menu-item-has-children dropdown">
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
                     <i class="menu-icon  fa fa-cube"></i>
                     <?php echo e(__('messages.product')); ?>

                     </a>
                     <ul class="sub-menu children dropdown-menu">
                        <li>
                           <a href="<?php echo e(url('admin/category')); ?>">
                           <?php echo e(__('messages.category')); ?>

                           </a>
                        </li>                       
                        <li>
                           <a href="<?php echo e(url('admin/options')); ?>">
                           <?php echo e(__('messages.option')); ?>

                           </a>
                        </li>
                        <!--<li>
                           <a href="<?php echo e(url('admin/attributeset')); ?>">
                           <?php echo e(__('messages.attributeset')); ?>

                           </a>
                        </li>
                        <li>
                           <a href="<?php echo e(url('admin/attribute')); ?>">
                           <?php echo e(__('messages.attribute')); ?>

                           </a>
                        </li>-->
                        <li>
                           <a href="<?php echo e(url('admin/review')); ?>">
                           <?php echo e(__('messages.review')); ?>

                           </a>
                        </li>
                        <li>
                           <a href="<?php echo e(url('admin/featureproduct')); ?>">
                           <?php echo e(__('messages.feature_product')); ?>

                           </a>
                        </li>
                     </ul>
                  </li>
                  <li class="active">
                     <a href="<?php echo e(url('admin/product')); ?>"  aria-haspopup="true" aria-expanded="false"> 
                         <i class="menu-icon  fa fa-linode"></i>
                         <?php echo e(__('messages.add')); ?> <?php echo e(__('messages.catalog')); ?>

                     </a>                     
                  </li>
                   <li class="active">
                     <a href="<?php echo e(url('admin/news')); ?>"  aria-haspopup="true" aria-expanded="false"> 
                         <i class="menu-icon  fa fa-newspaper-o"></i>
                        <?php echo e(__('messages.news')); ?>

                     </a>                     
                  </li>
                  <li class="menu-item-has-children dropdown">
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
                     <i class="menu-icon  fa fa-gift"></i>
                     <?php echo e(__('messages.offers')); ?> 
                     </a>
                     <ul class="sub-menu children dropdown-menu">
                        <li>
                           <a href="<?php echo e(url('admin/offer')); ?>">
                           <?php echo e(__('messages.big_offer')); ?>

                           </a>
                        </li>
                        <li>
                           <a href="<?php echo e(url('admin/normaloffer')); ?>">
                           <?php echo e(__('messages.normal_offer')); ?>

                           </a>
                        </li>
                        <li>
                           <a href="<?php echo e(url('admin/sensonal_offer')); ?>">
                           <?php echo e(__('messages.sensonal_offer')); ?>

                           </a>
                        </li>
                        <li>
                           <a href="<?php echo e(url('admin/deals')); ?>">
                           <?php echo e(__('messages.current_offer')); ?>

                           </a>
                        </li>
                     </ul>
                  </li>
                 <?php if(Session::get("is_web")==1): ?>
                  <li class="active">
                     <a href="<?php echo e(url('admin/sepical_category')); ?>">
                     <i class="menu-icon fa fa-image"></i>
                     <?php echo e(__('messages.special_category')); ?>

                     </a>
                  </li>
                  <?php endif; ?>
                   <li class="active">
                     <a href="<?php echo e(url('admin/notification')); ?>">
                     <i class="menu-icon fa fa-image"></i>
                     <?php echo e(__('messages.notification')); ?>

                     </a>
                  </li>
                 <?php if(Session::get("is_web")==1): ?>
                  <li class="active">
                     <a href="<?php echo e(url('admin/contact')); ?>">
                     <i class="menu-icon fa fa-address-book"></i>
                     <?php echo e(__('messages.contact_details')); ?>

                     </a>
                  </li>
                 <?php endif; ?>
                 
                  <li class="active">
                     <a href="<?php echo e(url('admin/coupon')); ?>">
                     <i class="menu-icon  fa fa-tags"></i>
                     <?php echo e(__('messages.coupon')); ?>

                     </a>
                  </li>
                   
                   <li class="active">
                     <a href="<?php echo e(url('admin/complain')); ?>">
                     <i class="menu-icon  fa fa-tags"></i>
                     <?php echo e(__('messages.complain')); ?>

                     </a>
                  </li>
                  
                  
                  <li class="menu-item-has-children dropdown">
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
                     <i class="menu-icon  fa fa-users"></i>
                     <?php echo e(__('messages.users')); ?>

                     </a>
                     <ul class="sub-menu children dropdown-menu">
                        <li>
                           <a href="<?php echo e(url('admin/user')); ?>">
                           <?php echo e(__('messages.users')); ?>

                           </a>
                        </li>
                        <li>
                           <a href="<?php echo e(url('admin/admin')); ?>">
                           <?php echo e(__('messages.admin')); ?>

                           </a>
                        </li>
                        <li>
                           <a href="<?php echo e(url('admin/userrole')); ?>">
                           <?php echo e(__('messages.role')); ?>

                           </a>
                        </li>
                     </ul>
                  </li>
                  <li class="menu-item-has-children dropdown">
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
                     <i class="menu-icon  fa fa-cog"></i>
                     <?php echo e(__('messages.site_setting')); ?>

                     </a>
                     <ul class="sub-menu children dropdown-menu">
                        <li>
                           <a href="<?php echo e(url('admin/setting')); ?>">
                           <?php echo e(__('messages.setting')); ?>

                           </a>
                        </li>
                        <li>
                           <a href="<?php echo e(url('admin/pages')); ?>">
                           <?php echo e(__('messages.page')); ?>

                           </a>
                        </li>
                        <li>
                           <a href="<?php echo e(url('admin/serverkey/1')); ?>">
                           <?php echo e(__('messages.Android Server Key')); ?>

                           </a>
                        </li>
                        <li>
                           <a href="<?php echo e(url('admin/serverkey/2')); ?>">
                           <?php echo e(__('messages.Iphone Server Key')); ?>

                           </a>
                        </li>
                        <?php if(Session::get("is_web")==1): ?>
                        <li>
                           <a href="<?php echo e(url('admin/banner')); ?>">
                           <?php echo e(__('messages.banner')); ?>

                           </a>
                        </li>
                        <li>
                           <a href="<?php echo e(url('admin/support/1')); ?>">
                           <?php echo e(__('messages.helpsupport')); ?>

                           </a>
                        </li>
                        <li>
                           <a href="<?php echo e(url('admin/support/2')); ?>">
                           <?php echo e(__('messages.termscon')); ?>

                           </a>
                        </li>
                        <?php endif; ?>
                     </ul>
                  </li>
                  <li class="menu-item-has-children dropdown">
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
                     <i class="menu-icon fa fa-globe"></i>
                     <?php echo e(__('messages.localization')); ?>

                     </a>
                     <ul class="sub-menu children dropdown-menu">
                        <li>
                           <a href="<?php echo e(url('admin/translations')); ?>">
                           <?php echo e(__('messages.translation')); ?>

                           </a>
                        </li>
                        <li>
                           <a href="<?php echo e(url('admin/taxes')); ?>">
                           <?php echo e(__('messages.taxes')); ?>

                           </a>
                        </li>
                     </ul>
                  </li>
                  <li class="active">
                     <a href="<?php echo e(url('admin/report')); ?>">
                     <i class="menu-icon fa fa-bar-chart"></i>
                     <?php echo e(__('messages.report')); ?>

                     </a>
                  </li>
               </ul>
            </div>
         </nav>
      </aside>
      <div id="right-panel" class="right-panel">
         <header id="header" class="header">
            <div class="header-menu">
               <div class="col-sm-10 pr0 eco-tog">
                  <a id="menuToggle" class="menutoggle pull-left">
                  <i class="fa fa fa-tasks"></i>
                  </a>
                  <div class="header-left florig">                     
                  </div>
               </div>
                   <div class="col-sm-1 dropdown for-notification">
                  <div class="dropdown">
  <button class="btn  dropdown-toggle" type="button" id="bell-button" onclick="checknotify()" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="fa fa-bell"></i>
                                <span class="count bg-danger" id="ordercount" style="">0</span>
  </button>
  <div class="dropdown-menu" aria-labelledby="bell-button" id="notificationshow">
   <p class="red" id="notificationmsg"><?php echo e(__('messages.orders_pending')); ?></p>
  </div>
</div>
                      
                        </div>
               <div class="col-sm-1 col-12 eco-pro">
                  <div class="user-area dropdown float-right">
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <img class="user-avatar rounded-circle" src="<?php echo e(Session::get('profile_pic')); ?>" alt="User Avatar">
                     </a>
                     <div class="user-menu dropdown-menu">
                        <a class="nav-link" href="<?php echo e(url('admin/editprofile')); ?>">
                        <i class="fa fa-user"></i>
                        <?php echo e(__('messages.my_profile')); ?>

                        </a>
                        <a class="nav-link" href="<?php echo e(url('admin/changepassword')); ?>">
                        <i class="fa fa-user"></i>
                        <?php echo e(__('messages.change_pwd')); ?>

                        </a>
                        <a class="nav-link" href="<?php echo e(url('admin/logout')); ?>">
                        <i class="fa fa-power-off"></i>
                        <?php echo e(__('messages.logout')); ?>

                        </a>
                     </div>
                  </div>
               </div>
            </div>
         </header>
         <?php echo $__env->yieldContent('content'); ?>
   </div>
</div>
      <input type="hidden" id="url_path" value="<?php echo e(url('/')); ?>">
      <input type="hidden" id="error_cur_pwd" value="<?php echo e(__('passwords.error_cur_pwd')); ?>">
      <input type="hidden" id="delete_data" value='<?php echo e(__("messages_error_success.delete_alert")); ?>'>
      <input type="hidden" id="data_save_success" value="<?php echo e(__('messages_error_success.data_save_success')); ?>">
      <input type="hidden" id="something" value="<?php echo e(__('messages_error_success.error_code')); ?>">
      <input type="hidden" id="generalmsg" value="<?php echo e(__('messages_error_success.general_form_msg')); ?>">
      <input type="hidden" id="fixerror" value="<?php echo e(__('messages_error_success.per_bet')); ?>">
      <input type="hidden" id="check_price" value="<?php echo e(__('messages_error_success.check_price')); ?>">
      <input type="hidden" id="offer_price_error" value="<?php echo e(__('messages_error_success.offer_price_error')); ?>">
      <input type="hidden" id="pass_mus" value="<?php echo e(__('passwords.pass_mus')); ?>">
      <input type="hidden" id="requiredfields" value="<?php echo e(__('messages_error_success.required_field')); ?>">
      <input type="hidden" id="soundnotify" value="<?php echo e(asset('public/sound/notification/notification.mp3')); ?>">
      <input type="hidden" id="orders_pending" value="<?php echo e(__('messages.orders_pending')); ?>">
      <input type="hidden" id="you_have" value="<?php echo e(__('messages.you_have')); ?>">
      <input type="hidden" id="new_order" value="<?php echo e(__('messages.new_order')); ?>">
      <input type="hidden" id="demo_lang" value="<?php echo e(Session::get('is_demo')); ?>">
       <input type="hidden" id="vieworder_lang" value="<?php echo e(__('messages.view_order')); ?>">
       <input type="hidden" id="setemail" value="<?php echo e(__('messages.setupemail')); ?>"/>
        <input type="hidden" id="no_realted_msg" value="<?php echo e(__('messages.no_realted')); ?>"/>
      <input type="hidden" id="coupon_vaild_max" value="<?php echo e(__('messages.coupon_vaild_max')); ?>"/>
      <input type="hidden" id="image_invaild" value="<?php echo e(__('messages.img_invaild')); ?>"/>
      <script src="<?php echo e(asset('public/admin/vendors/jquery/dist/jquery.min.js')); ?>"></script>
      <script src="<?php echo e(asset('public/admin/vendors/popper.js/dist/umd/popper.min.js')); ?>"></script>
      <script src="<?php echo e(asset('public/admin/vendors/bootstrap/dist/js/bootstrap.min.js')); ?>"></script>
      <script src="<?php echo e(asset('public/admin/assets/js/main.js')); ?>"></script>
      <script src="<?php echo e(asset('public/admin/vendors/chosen/chosen.jquery.min.js')); ?>"></script>
      <script src="<?php echo e(asset('public/admin/vendors/jqvmap/examples/js/jquery.vmap.sampledata.js')); ?>"></script>
       <script src='https://code.jquery.com/jquery-1.12.3.js'></script>
      
     
      <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
      <script src='https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js'></script>
      <script src='https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js'></script>
      <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js'></script>
      <script src='https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js'></script>
      <script src='https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js'></script>
      <script src='https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js'></script>
      <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js'></script>
      <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js'></script>
      <script type="text/javascript" src="<?php echo e(asset('public/js/admin.js').'?v=fgdfgdf'); ?>"></script>
      <script src='https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.1/js/standalone/selectize.js'></script>
      <?php echo $__env->yieldContent('footer'); ?>
   </body>
</html><?php /**PATH E:\xampp\htdocs\project\company\blank_script\blank_ecomerce_admin_panel\ecommerce\resources\views/admin/index.blade.php ENDPATH**/ ?>