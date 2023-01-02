<?php $__env->startSection('content'); ?>
<div class="breadcrumbs">
      <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
         <div class="page-title">
            <h1><?php echo e(__('messages.dashboard')); ?></h1>
         </div>
      </div>
   </div>
    <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
         <div class="page-title">
            <ol class="breadcrumb text-right">
               <li class="active"><?php echo e(__('messages.dashboard')); ?></li>
            </ol>
         </div>
      </div>
   </div>
</div>
<div class="content mt-3 sale">
   <div class="col-lg-3 col-sm-6">
      <div class="card">
         <div class="card-body">
            <div class="stat-widget-one">
               <div class="stat-icon dib"><i class="ti-money text-success border-success"></i></div>
               <div class="stat-content dib">
                  <div class="stat-text"><?php echo e(__('messages.total_sale')); ?></div>
                  <div class="stat-digit">
                     <?php echo e($total_sell); ?>

                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-lg-3 col-sm-6">
      <div class="card">
         <div class="card-body">
            <div class="stat-widget-one">
               <div class="stat-icon dib"><i class="ti-shopping-cart text-success border-success"></i></div>
               <div class="stat-content dib">
                  <div class="stat-text"><?php echo e(__('messages.total_order')); ?></div>
                  <div class="stat-digit"><?php echo e($total_order); ?></div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-lg-3 col-sm-6">
      <div class="card">
         <div class="card-body">
            <div class="stat-widget-one">
               <div class="stat-icon dib"><i class="ti-bar-chart text-success border-success"></i></div>
               <div class="stat-content dib">
                  <div class="stat-text"><?php echo e(__('messages.total_product')); ?></div>
                  <div class="stat-digit"><?php echo e($total_product); ?></div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-lg-3 col-sm-6">
      <div class="card">
         <div class="card-body">
            <div class="stat-widget-one">
               <div class="stat-icon dib"><i class="ti-user text-success border-success"></i></div>
               <div class="stat-content dib">
                  <div class="stat-text"><?php echo e(__('messages.total_customers')); ?></div>
                  <div class="stat-digit"><?php echo e($total_users); ?></div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="col-md-12 flt">
   <div class="row">
   <div class="col-md-12 col-xl-6 col-12 flat-r">
      <div class="card">
         <div class="card-body">
            <h4 class="orderh4">
               <i class="fa fa-shopping-cart" aria-hidden="true"></i>     <?php echo e(__('messages.latest_order')); ?>

               <a class="btn btn-primary btn-flat m-b-30 m-t-30 elec textorder" href="<?php echo e(url('admin/order')); ?>"><?php echo e(__('messages.Show All')); ?> </a>
            </h4>

            <div class="table-responsive dtdiv">
               <table id="latestorderTable" class="table table-striped dttablewidth">
                  <thead>
                     <tr>
                        <th><?php echo e(__('messages.order_id')); ?></th>
                        <th><?php echo e(__('messages.customer')); ?></th>
                        <th><?php echo e(__('messages.status')); ?></th>
                        <th><?php echo e(__('messages.total')); ?></th>
                     </tr>
                  </thead>
               </table>
            </div>
         </div>
      </div>
   </div>
   <div class="col-md-12 col-xl-6 col-12 flat-r">
      <div class="card">
         <div class="card-body">
            <h4 class="orderh4">
               <i class="fa fa-comments-o" aria-hidden="true"></i>    <?php echo e(__('messages.latest_review')); ?>

                <a class="btn btn-primary btn-flat m-b-30 m-t-30 elec textorder" href="<?php echo e(url('admin/review')); ?>"><?php echo e(__('messages.Show All')); ?> </a>
            </h4>
            <div class="table-responsive dtdiv">
               <table id="myTablereview" class="table table-striped dttablewidth">
                  <thead>
                     <tr>
                        <th><?php echo e(__('messages.product')); ?></th>
                        <th><?php echo e(__('messages.customer')); ?></th>
                        <th><?php echo e(__('messages.ratting')); ?></th>
                     </tr>
                  </thead>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\xampp\htdocs\project\company\blank_script\blank_ecomerce_admin_panel\ecommerce\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>