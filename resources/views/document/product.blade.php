<!doctype html>
<html lang="en">
   <meta http-equiv="content-type" content="text/html;charset=utf-8" />
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <link rel="stylesheet" href="{{url('public/guidebook/assets/css/vendor.css')}}" />
      <link rel="stylesheet" href="{{url('public/guidebook/assets/css/style.css')}}" />
      <title>Documentation - Introduction</title>
   </head>
   <body data-spy="scroll" data-target="#toc">
      <section class="py-0">
         <div class="container-fluid">
            <div class="row justify-content-between">
               <aside class="col-lg-3 p-3 doc-sidebar">
                  <div class="sticky">
                     <nav class="navbar navbar-vertical navbar-expand-lg navbar-light">
                        <div>
                           <a href="#" class="navbar-brand"><img src="https://webuildthemes.com/guidebook/assets/images/logo.svg" alt="Logo"></a>
                           <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                           <span class="navbar-toggler-icon"></span>
                           </button>
                        </div>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                           <ul id="page-nav" class="nav nav-vertical-2 flex-column">
                              <li class="nav-item ">
                                 <a class="nav-link"  href="{{url('document')}}" role="button" aria-expanded="true" aria-controls="menu-1">Introduction Started</a>
                              </li>
                              <li class="nav-item active">
                                 <a class="nav-link"  href="{{url('addproductdoc')}}" role="button" aria-expanded="false" aria-controls="menu-2">Prerequisite Before Add Product</a>
                              </li>
                               <li class="nav-item">
                                 <a class="nav-link"  href="{{url('addproductstep')}}" role="button" aria-expanded="false" aria-controls="menu-2">How to add product</a>
                                 
                              </li>
                              <li class="nav-item">
                                 <a class="nav-link" href="{{url('mainofferdoc')}}" role="button" aria-expanded="false" aria-controls="menu-3">How to add main offers</a>
                               
                              </li>
                              <li class="nav-item">
                                  <a class="nav-link"  href="{{url('normalofferdoc')}}" role="button" aria-expanded="false" aria-controls="menu-2">How to add normal offers</a>
                              </li>
                              <li class="nav-item">
                                  <a class="nav-link"  href="{{url('dealofferdoc')}}" role="button" aria-expanded="false" aria-controls="menu-2">How to add deal offers</a>
                              </li>
                           </ul>
                        </div>
                     </nav>
                  </div>
               </aside>
               <article class="col-lg-9 doc-content">
                  <div class="row">
                     <div class="col">
                        <div class="doc-content-header">
                           <div class="row align-items-center">
                              <div class="col-6 col-lg-10">
                                 <nav aria-label="breadcrumb d-none d-inline-flex-lg">
                                    <a href="{{url('document')}}" class="breadcrumb-back" title="Back"></a>
                                    <ol class="breadcrumb d-none d-lg-inline-flex">
                                       <li class="breadcrumb-item"><a href="{{url('document')}}">Home</a></li>
                                       <li class="breadcrumb-item"><a href="#">Documentation</a></li>
                                       <li class="breadcrumb-item active" aria-current="page">Prerequisite Before Add Product</li>
                                    </ol>
                                 </nav>
                              </div>
                              <div class="col text-right">
                                 <a href="{{url('login')}}" class="btn btn-sm btn-rounded btn-primary">Login</a>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row doc-content-body" id="intro">
                     <article class="col-md-9">
                        <h1>Prerequisite Before Add Product</h1>
                        <p>This is a quick quide that will help to before adding product in system </p>
                        <section id="setup">
                           <h2 class="section-title-2">Category<a href="#setup" class="clipboard"></a></h2>
                           <p>You need to add category,subcategory,brand.</p>
                           <p>when you add  cateogry,subcategory,brand then graph show like this.</p>
                           <div class="owl-carousel owl-loaded owl-drag" data-items="[1,1,1]" data-margin="20" data-nav="true">
                              <div class="owl-stage-outer">
                                 <div class="owl-stage" style="transform: translate3d(-1357px, 0px, 0px); transition: all 0.25s ease 0s; width: 2037px;">
                                    <div class="owl-item" style="width: 658.813px; margin-right: 20px;">
                                       <figure class="photo">
                                          <a class="lightbox" href="{{asset('public/doc/1.png')}}" title="Cateogry">
                                          <img src="{{asset('public/doc/1.png')}}" alt="cateogry">
                                          </a>
                                       </figure>
                                    </div>
                                    <div class="owl-item" style="width: 658.813px; margin-right: 20px;">
                                       <figure class="photo">
                                          <a class="lightbox" href="{{asset('public/doc/2.png')}}" title="Subcategory">
                                          <img src="{{asset('public/doc/2.png')}}" alt="subcategory">
                                          </a>
                                       </figure>
                                    </div>
                                    <div class="owl-item active" style="width: 658.813px; margin-right: 20px;">
                                       <figure class="photo">
                                          <a class="lightbox" href="{{asset('public/doc/3.png')}}" title="Brand">
                                          <img src="{{asset('public/doc/3.png')}}" alt="brand">
                                          </a>
                                       </figure>
                                    </div>
                                 </div>
                              </div>
                              <div class="owl-nav"><button type="button" role="presentation" class="owl-prev"><span aria-label="Previous">‹</span></button><button type="button" role="presentation" class="owl-next disabled"><span aria-label="Next">›</span></button></div>
                              <div class="owl-dots disabled"></div>
                           </div>
                        </section>
                        <section id="template">
                           <h2 class="section-title-2">Taxes <a href="#template" class="clipboard"></a></h2>
                           <p>You need add tax for your product.</p>
                           <a class="lightbox" href="{{asset('public/doc/4.png')}}" title="Tax Dashboard">
                           <img src="{{asset('public/doc/4.png')}}" alt="brand">
                           </a>
                           <pre></pre>
                        </section>
                        <section id="template1">
                           <h2 class="section-title-2">Options<a href="#template1" class="clipboard"></a></h2>
                           <p>This part is optional.if you are add option here then you can directly use this option in every product.</p>
                              <div class="owl-carousel owl-loaded owl-drag" data-items="[1,1,1]" data-margin="20" data-nav="true">
                              <div class="owl-stage-outer">
                                 <div class="owl-stage" style="transform: translate3d(-1357px, 0px, 0px); transition: all 0.25s ease 0s; width: 2037px;">
                                    <div class="owl-item" style="width: 658.813px; margin-right: 20px;">
                                       <figure class="photo">
                                          <a class="lightbox" href="{{asset('public/doc/5.png')}}" title="Option">
                                          <img src="{{asset('public/doc/5.png')}}" alt="Option Dashboard">
                                          </a>
                                       </figure>
                                    </div>
                                    <div class="owl-item" style="width: 658.813px; margin-right: 20px;">
                                       <figure class="photo">
                                          <a class="lightbox" href="{{asset('public/doc/6.png')}}" title="Add/edit section">
                                          <img src="{{asset('public/doc/6.png')}}" alt="subcategory">
                                          </a>
                                       </figure>
                                    </div>
                                    
                                 </div>
                              </div>
                              <div class="owl-nav"><button type="button" role="presentation" class="owl-prev"><span aria-label="Previous">‹</span></button><button type="button" role="presentation" class="owl-next disabled"><span aria-label="Next">›</span></button></div>
                              <div class="owl-dots disabled"></div>
                           </div>
                           <pre></pre>

                        </section>
                         <section id="template2">
                           <h2 class="section-title-2">Product <a href="#template2" class="clipboard"></a></h2>
                           <p>Now you can add product in system</p>
                          
                           <pre></pre>
                           
                        </section>
                     </article>
                     <aside class="col-md-3 d-none d-md-block">
                        <div class="sticky">
                           <ul id="toc" class="nav flex-column toc">
                              <li class="nav-item">
                                 <a data-scroll class="nav-link" href="#setup">Category</a>
                              </li>
                              <li class="nav-item">
                                 <a data-scroll class="nav-link" href="#template">Taxes</a>
                              </li>
                              <li class="nav-item">
                                 <a data-scroll class="nav-link" href="#template1">Options</a>
                              </li>
                              <li class="nav-item">
                                 <a data-scroll class="nav-link" href="#template2">Product</a>
                              </li>
                           </ul>
                        </div>
                     </aside>
                  </div>
               </article>
            </div>
         </div>
      </section>
      <footer class="bg-dark">
         <div class="container">
         </div>
      </footer>
      <script src="{{url('public/guidebook/assets/js/vendor.js')}}"></script>
      <script src="{{url('public/guidebook/assets/js/app.js')}}"></script>
   </body>
</html>