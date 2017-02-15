@extends('backoffice.app')
@section('admin_head_css')
	<link href="{{ asset('blog_assets/css/post.css') }}" rel="stylesheet">
	<link href="{{ asset('css/fileupload.css') }}" rel="stylesheet">
    <link href="{{ asset('js/plugins/markitup/skins/simple/style.css') }}" rel="stylesheet">
    <link href="{{ asset('js/plugins/markitup/sets/default/style.css') }}" rel="stylesheet">
@stop

@section('content')

	<section class="content-header">
		<h1>
		{{ trans('main.blog') }}
			<small>it all starts here</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li><a href="#">Examples</a></li>
			<li class="active">Blank page</li>
		</ol>
    </section>
	<section class="content">
		<!-- Default box -->
		<div class="box">
			<div class="box-header with-border">
			  <h3 class="box-title">New pages</h3> 

			  <div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
				  <i class="fa fa-minus"></i></button>				
			  </div>
			</div>
			<div class="box-body">
				<form id="form-page" method="POST">
				<input type="hidden" name="_token" value="{!! csrf_token() !!}" />
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label>Title*</label>
							@if(checkI18n())
								{!! I18n::GenerateField(['field'=>['type'=>'text','name'=>'header']]) !!}
							@else
								<input type="text" class="form-control" name="header"/>
							@endif
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label>Slug*</label>
							<input type="text" class="form-control" name="slug" value="" maxlength="100"/>							
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-buttons loading-indicator-container">

							<!-- Save -->
							<a href="javascript:;" class="save" data-request="onSave" data-load-indicator="Saving..." data-request-after="redirect" data-request-form="#form-page" data-request-url="{!! url('backend/page/new') !!}">
								<i class="fa fa-check"></i> Save    
							</a>
						</div>
					</div>
					
					<div class="col-sm-12 post-tabs-section">
						<ul class="disabled nav-post-edit">
							<li class="active"><a href="#tab-edit">Edit</a></li>
							<li ><a href="#tab-manage">Manage</a></li>
						</ul>
						<div id="tab-edit" class="tab-section">
							@if (checkI18n())
								{!! I18n::GenerateField(['field'=>['type'=>'textarea','name'=>'article','class'=>'markItUpText description']]) !!}
							@else
								<textarea class="textarea field te-editor" placeholder="Place some text here" name="article" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px; font-family:Arial;">{{ $post->article }}</textarea>											
							@endif
						</div>
						
						<div id="tab-manage" class="tab-section hidden">
							<div class="form-group custom-checkbox">
								<input type="checkbox" class="minimal" name="published" value="1"/>
								<label>
									&nbsp;&nbsp;Published
								</label>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label>Published on</label>
										<div class="input-group">
											<input type="text" class="form-control" id="datepicker" name="published_date"/>	
											<span class="input-group-addon">
												<i class="fa fa-calendar"></i>
											</span>
										</div>
										
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label>Parent</label>
										<select class="form-control" name="parent_id">
											<option value="0">-</option>
											@if($pages->count())
												@foreach($pages as $page)
													<option value="{{$page->id}}">{{$page->header}}</option>
												@endforeach
											@endif
										</select>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label>Excerpt</label>
								@if (checkI18n())
									{!! I18n::GenerateField(['field'=>['type'=>'textarea','name'=>'excerpt', 'class'=>'excerpt']]) !!}
								@else
									<textarea class="form-control" rows="5" name="excerpt"></textarea>
								@endif
							</div>
					
							<div class="form-group">
								<label>Featured Image</label>			
								<div id="FileUpload-formFeaturedImages-featured_images" class="field-fileupload style-image-multi is-sortable is-multi is-populated " data-control="fileupload" data-template="#FileUpload-formFeaturedImages-template-featured_images" data-error-template="#FileUpload-formFeaturedImages-errorTemplate-featured_images" data-sort-handler="formFeaturedImages::onSortAttachments" data-unique-id="FileUpload-formFeaturedImages-featured_images" data-config-handler="formFeaturedImages::onLoadAttachmentConfig" data-file-types=".jpg,.jpeg,.bmp,.png,.gif,.svg" data-disposable="">

									<!-- Upload Button -->
									<div id="thumbnail-form" class="upload-button"  onclick="openImageMedia('#form-page',['ImageAsThumbnail']); return false;">
										<i class="upload-button-icon fa fa-plus"></i>
									</div>
									<!-- Existing files -->
									<div class="upload-files-container">
										
									</div>
								</div>
							</div>
						
						<input type="hidden" name="_action" value="submit" />
					<input type="hidden" name="thumbnail" value="" />
				</form>
						</div>
					</div>
				</div>
				
			</div><!-- /.box-body -->
			<!-- /.box-body -->
			<div class="box-footer">
				Footer
			</div>
			<!-- /.box-footer-->
		</div>
		<!-- /.box -->
	</section>
	
@stop


@section('admin_footer_js')
    <script src="/js/plugins/markitup/jquery.markitup.js"></script>
    <script src="/js/plugins/markitup/sets/default/set.js"></script>

	<script>
		$(function(){

            $('.markItUpText').markItUp(advancedSettings);

			$('.post-tabs-section .nav-post-edit a').click(function(e){
				e.preventDefault();
				var openId = $(this).attr('href');
				$('.post-tabs-section .tab-section:not(.hidden)').addClass('hidden');
				$(openId).removeClass('hidden');
				$('.post-tabs-section .nav-post-edit li').removeClass('active');
				$(this).parent().addClass('active');
			});
			
			//Date range picker
			$('#datepicker').datepicker({
				autoclose: true,
				format: 'dd/mm/yyyy'
			});
			
			$('input[name="header"]').keyup(function(){
				translit('input[name="header"]', 'input[name="slug"]');
			});
			$('input[name="header"]').blur(function(){
				translit('input[name="header"]', 'input[name="slug"]');
			});
			$('input[name="slug"]').keyup(function(){
				$('input[name="header"]').unbind('keyup');
			});

		});
		
	</script>
@stop