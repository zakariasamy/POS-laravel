@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.home')</h1>

            <ol class="breadcrumb">
                <li class="active"><i class="fa fa-dashboard"></i> @lang('site.home')</li>
            </ol>
        </section>

        <section class="content">

            <div class="row">

              <div class="content-wrapper">
                  Welcome to dashboard
              </div>
            </div><!-- end of row -->


        </section><!-- end of content -->

    </div><!-- end of content wrapper -->


@endsection

