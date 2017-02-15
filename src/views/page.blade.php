@extends('layouts.default')
@section('title')
	{{$page->header}}
@stop
@section('head_css')
	<link href="{{ asset('blog_assets/css/post.css') }}" rel="stylesheet">
@stop

@section('breadcrumbs')
	<ol class="breadcrumb hidden-sm hidden-xs">
		<li><a href="{{ asset('/') }}"> <i class="fa fa-home"></i>{{ trans('main.home') }}</a></li>
		@if(!empty($crumbs))
			@foreach($crumbs as $crumb)
				<li><a href="{{ url('/page/'.$crumb['slug']) }}">{{$crumb['header']}}</a></li>
			@endforeach
		@endif
		<li>{{ $page->header }}</li>
	</ol>
@stop

@section('page_title')
	<div class="row blog-title " style="background:url( 
							@if ($page->thumbnail)
							{!! url($page->thumbBySize('862x217')) !!}
								@else
							{!! url('/thumbnails/fake/862x217') !!}
							@endif 
						  ) center center;background-size:100% auto">
		<div class="overlay"></div>
		<div class="col-xs-12">
			<h3 class="page-title">{{ $page->header }}</h3>
			<div class="">
				<p class="gray col-md-10 page-subtitle col-md-offset-1">{{ $page->excerpt }}</p>
			</div>
		</div>
		
	</div>	
@stop

@section('content')
<section class="blog-list">
	<div class="row">
		<div class="col-sm-12">
			<div class="article">				
			@permission('post.add')	   
				<a href="/backend/page/{{ $page->id }}"><i class="fa fa-pencil"></i> Edit</a>	<br/>
			@endpermission
				<div class="post-content">{!! func_spoilerParse($page->article) !!}</div>				
			</div>
		</div>	
	</div>
</section>

@stop