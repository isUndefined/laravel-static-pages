@extends('backoffice.app')

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
			  <h3 class="box-title">pages</h3>

			  <div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
				  <i class="fa fa-minus"></i>
				</button>				
			  </div>
			</div>
			<div class="box-body table-responsive no-padding">
                <div class="col-sm-12">
					<br>
					<div class="form-group">
						<a href="{!! url('backend/page/new') !!}" class="btn btn-flat btn-primary"><i class="fa fa-plus"></i> New page</a> &nbsp;&nbsp;
						<a class="btn btn-default btn-flat disabled delete-selected"><i class="fa fa-trash-o"></i> Delete selected</a>
					</div>
				</div>
				<table class="table table-hover">
                    <tr>
                      <th class="text-center">
						<label>
						  <input type="checkbox" class="minimal select-all">
						</label>
					  </th>
                      <th>TITLE</th>
                      <th>PARENT</th>
                      <th>AUTHOR</th>
                      <th>PUBLISHED</th>
                    </tr>
					@forelse($pages as $page)
					<tr>
                      <td class="text-center">
						<label>
						  <input type="checkbox" class="minimal">
						</label>
					  </td>
                      <td><a href="{!! url('backend/page/') !!}/{{ $page->id }}">{{ $page->header }}</a></td>
                      <td>@if($page->parentCategory) <a href="{!! url('backend/page/') !!}/{{ $page->parentCategory->id }}">{{ $page->parentCategory->header }}</a> @else - @endif</td>
                      <td>{{ $page->author()->name }}</td>
                      <td>{{ date('M d, Y',strtotime($page->created_at)) }}</td>
                    </tr>
					@empty
						<tr>
							<td colspan="4" class="text-center">No data.</td>
						</tr>
					@endforelse
                    
                  </table>
				  {!! $pages->render() !!}
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