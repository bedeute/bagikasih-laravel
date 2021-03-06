@extends('admin.layouts.default')

@section('content')
<div class="row">
    <div class="col-xs-12 col-md-12">
		<div class="box">
			<div class="box-header">
				<h3 class="box-title">Edit Data with ID {{ $data->id }}</h3>				
			</div><!-- /.box-header -->
			<div class="box-body">
				@if(count($errors))
					<div class="alert alert-warning">
					@foreach($errors->all() as $err)
					<p>{{ $err }}</p>	
					@endforeach
					</div>
				@endif				
				{{ Form::open(['route'=> 'admin.donation.update.post']) }}
				{{ Form::hidden('id', $data->id) }}
				<div class="form-group">
					{{ Form::label('user', 'user')}}
					{{ Form::select('user', $options_user, '', ['class'=> 'form-control']) }}
				</div>
				<div class="form-group">
					{{ Form::label('payment', 'Payment') }}
					{{ Form::select('payment', $options_payment, $data->payment_id, ['class'=> 'form-control']) }}
				</div>				
				<div class="form-group">
					{{ Form::label('type_name', 'Type Name') }}
					{{ Form::select('type_name', $options_type_name, $data->type_name, ['class'=> 'form-control']) }}
				</div>				
				<div class="form-group">
					{{ Form::label('type_id', 'Type Id') }}
					{{ Form::select('type_id', $options_type_id, $data->type_id, ['class'=> 'form-control']) }}
				</div>				
				<div class="form-group">
					{{ Form::label('currency', 'Currency') }}
					{{ Form::select('currency', $options_currency, $data->currency, ['class'=> 'form-control']) }}
				</div>				
				<div class="form-group">
					{{ Form::label('total', 'Total') }}
					{{ Form::text('total', $data->id, ['class'=> 'form-control']) }}
				</div>				
				<div class="form-group">
					{{ Form::label('message', 'Message') }}
					{{ Form::textarea('message', $data->message, ['class'=> 'form-control']) }}
				</div>				
				<div class="form-group">
					{{ Form::label('as_noname', 'As Noname ?')}}
					<div class="radio">
						<label>{{ Form::radio('as_noname','0',$data->as_noname == 0? true : false,['class' => 'radio']) }} No</label>
						<label>{{ Form::radio('as_noname','1',$data->as_noname == 1? true : false,['class' => 'radio']) }} Yes</label>
					</div>
				</div>
				<div class="form-group">
					{{ Form::label('status', 'Status')}}
					<div class="radio">
						<label>{{ Form::radio('status','0',$data->status == 0? true : false,['class' => 'radio']) }} Belum Dibayar</label>
						<label>{{ Form::radio('status','1',$data->status == 1? true : false,['class' => 'radio']) }} Dibayar</label>
					</div>
				</div>
				<div class="form-group">
					{{ Form::submit('Save', ['class' => 'btn btn-info']) }} <a href="{{ route('admin.donation')}}" class="btn btn-default">Cancel</a>
				</div>
				{{ Form::close() }}
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
</div>
@stop