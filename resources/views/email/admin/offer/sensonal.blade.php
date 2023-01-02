 @extends('admin.index') @section('content')
<div class="breadcrumbs">
    <div class="col-sm-4 float-right-1">
      <div class="page-header float-left float-right-1">
            <div class="page-title">
                <h1>{{__('messages.seasonal_offer')}}</h1>
            </div>
        </div>
    </div>
    <div class="col-sm-8 float-left-1">
      <div class="page-header float-right float-left-1">
            <div class="page-title">
                <ol class="breadcrumb text-right">
                    <li class="active">{{__('messages.seasonal_offer')}}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content mt-3">
        <div class="col-12">
            <div class="card">
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
                    <p class="successnote">{{__('messages_error_success.note_sensonal')}}</p>
                    <button onclick="addsensonal()" class="btn btn-primary btn-flat m-b-30 m-t-30">{{__('messages.add_sen')}}</button>

                    <div class="table-responsive dtdiv">
                        <table id="sensonalTable" class="table table-striped table-bordered dttablewidth">
                            <thead>
                                <tr>
                                    <th>{{__('messages.id')}}</th>
                                    <th>{{__('messages.banner')}}</th>
                                    <th>{{__('messages.title')}}</th>
                                    <th>{{__('messages.cate_gory')}}</th>
                                    <th>{{__('messages.action')}}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    <input type="hidden" id="confirm_alert" value='{{__("messages_error_success.confirm_alert")}}'>
    @stop