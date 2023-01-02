<!doctype html>
<html class="no-js" lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title><?php echo e(__('messages.site_name')); ?></title>
      <meta name="description" content="Sufee Admin - HTML5 Admin Template">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="apple-touch-icon" href="apple-icon.png">
      <link rel="shortcut icon" href="<?php echo e(asset('public/favicon.png')); ?>">
      <link rel="stylesheet" href="<?php echo e(url('public/admin/vendors/bootstrap/dist/css/bootstrap.min.css')); ?>">
      <link rel="stylesheet" href="<?php echo e(url('public/admin/vendors/font-awesome/css/font-awesome.min.css')); ?>">
      <link rel="stylesheet" href="<?php echo e(url('public/admin/vendors/themify-icons/css/themify-icons.css')); ?>">
      <link rel="stylesheet" href="<?php echo e(url('public/admin/vendors/flag-icon-css/css/flag-icon.min.css')); ?>">
      <link rel="stylesheet" href="<?php echo e(url('public/admin/vendors/selectFX/css/cs-skin-elastic.css')); ?>">
      <link rel="stylesheet" href="<?php echo e(url('public/admin/assets/css/style.css')); ?>">
      <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>
   </head>
   <body class="bg-dark">
      <div class="sufee-login d-flex align-content-center flex-wrap">
         <div class="container">
            <div class="login-content">
               <div class="login-logo">
                  <h4 class="sitecolor">
                     <?php echo e(__('messages.site_name')); ?>

                     <font class="admincolor">
                     <?php echo e(__('messages.admin')); ?>

                     </font>
                  </h4>
               </div>
               <div class="login-form">
                  <div id="respond" class="comment-respond">
                     <?php if(Session::has('message')): ?>
                     <div class="col-sm-12">
                        <div class="alert  <?php echo e(Session::get('alert-class', 'alert-info')); ?> alert-dismissible fade show" role="alert"><?php echo e(Session::get('message')); ?>

                           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                           </button>
                        </div>
                     </div>
                     <?php endif; ?>
                  </div>
                  <form action="<?php echo e(url('admin/postlogin')); ?>" method="post">
                     <?php echo e(csrf_field()); ?>

                     <div class="form-group">
                        <label><?php echo e(__('messages.email')); ?></label>
                        <input type="email" class="form-control" placeholder="<?php echo e(__('messages.email')); ?>" required name="email" id="email">
                     </div>
                     <div class="form-group">
                        <label><?php echo e(__('messages.password')); ?></label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="<?php echo e(__('messages.password')); ?>">
                     </div>
                     <button type="submit" class="btn btn-success btn-flat m-b-30 m-t-30">
                     <?php echo e(__('messages.sign_in')); ?>

                     </button>
                     <div class="form-group" style="">
                        <a href="<?php echo e(url('document')); ?>" style="color: blue !important;float: right;"><?php echo e(__('messages.document')); ?></a>
                     </div>
                  </form>
                   
               </div>
            </div>
         </div>
      </div>
      <script src="<?php echo e(asset('public/admin/vendors/jquery/dist/jquery.min.js')); ?>"></script>
      <script src="<?php echo e(asset('public/admin/vendors/popper.js/dist/umd/popper.min.js')); ?>"></script>
      <script src="<?php echo e(asset('public/admin/vendors/bootstrap/dist/js/bootstrap.min.js')); ?>"></script>
      <script src="<?php echo e(asset('public/admin/assets/js/main.js')); ?>"></script>
   </body>
</html><?php /**PATH E:\xampp\htdocs\project\company\blank_script\blank_ecomerce_admin_panel\ecommerce\resources\views/admin/login.blade.php ENDPATH**/ ?>