@extends('admin.layouts.default')

@section('content')
<div class="row">
    <div class="col-xs-12">
		<div class="box">
			<div class="box-header">
				<h3 class="box-title">Data <b>{{ $title }}</b></h3>
				<a href="{{ route('admin.social-target.create') }}" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Tambah</a>
			</div><!-- /.box-header -->
			<div class="box-body">
				<table id="datatable" class="table table-bordered table-striped">
					<tbody>
						<tr>
							<th>ID</th>
							<td>{{ $social_target->id }}</td>
						</tr>
						<tr>
							<th>Nama</th>
							<td>{{ $social_target->name }}</td>
						</tr>
						<tr>
							<th>Kategori</th>
							<td>{{ $social_target->category->name }}</td>
						</tr>
						<tr>
							<th>Kota</th>
							<td>{{ $social_target->city->name }}</td>
						</tr>
						<tr>
							<th>Pembuat</th>
							<td>
								@if (isset($social_target->user))
								<a href="{{ route('admin.user.show', $social_target->user->id) }}">
									{{ $social_target->user->firstname }} {{ $social_target->user->lastname }}
								</a>
								@endif
							</td>
						</tr>
						<tr>
							<th>Donasi Terkumpul</th>
							<td>{{ $social_target->currency }} {{ number_format($social_target->total_donation,0,',','.') }}</td>
						</tr>
						<tr>
							<th>Total Aksi Sosial Yang Berjalan</th>
							<td>{{ $social_target->total_running_social_actions }} Aksi Sosial</td>
						</tr>
						<tr>
							<th>Tentang Target Sosial</th>
							<td>{{ nl2br($social_target->description) }}</td>
						</tr>
						<tr>
							<th>Kepengurusan</th>
							<td>{{ nl2br($social_target->stewardship) }}</td>
						</tr>
						<tr>
							<th>Alamat</th>
							<td>{{ $social_target->address }}</td>
						</tr>
						<tr>
							<th>No.Telp</th>
							<td>{{ $social_target->phone_number }}</td>
						</tr>
						<tr>
							<th>Email</th>
							<td>{{ $social_target->email }}</td>
						</tr>
						<tr>
							<th>Sosial Media</th>
							<td>{{ $social_target->social_media_urls }}</td>
						</tr>
						<tr>
							<th>No.Rekening Bank</th>
							<td>{{ nl2br($social_target->bank_account_description) }}</td>
						</tr>
						<tr>
							<th>Foto Utama</th>
							<td id="setphoto"><img src="{{ url('photos') }}/{{ $social_target->default_photo_id ? $social_target->default_photo_id : 'default' }}.jpg" class="img-polaroid img-rounded" style="max-width:150px;height:auto;"></td>
						</tr>
						<tr>
							<th>Foto Banner</th>
							<td><img src="{{ url('photos') }}/{{ $social_target->cover_photo_id ? $social_target->cover_photo_id : 'default-cover' }}.jpg" class="img-polaroid img-rounded" style="max-width:600px;height:auto;"></td>
						</tr>
						<tr>
							<th>Status</th>
							<td>
								@if ($social_target->status == 1)
									Aktif
								@elseif ($social_target->status == 0)
									Butuh Konfirmasi
								@elseif ($social_target->status == 2)
									Tidak Aktif
								@endif
							</td>
						</tr>
						<tr>
							<th>Dibuat Pada</th>
							<td>{{ date('d M Y H:i:s', $social_target->created_at->timestamp) }}</td>
						</tr>
						<tr>
							<th>Diubah Pada</th>
							<td>{{ date('d M Y H:i:s', $social_target->updated_at->timestamp) }}</td>
						</tr>
						<tr>
							<th>Aksi</th>
							<td>
								<a href="{{ route('admin.social-target') }}" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-list" aria-hidden="true"></span> Kembali</a>
								<a href="{{ route('admin.social-target.update', $social_target->id) }}" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Ubah</a>
								<a href="{{ route('admin.social-target.delete', $social_target->id) }}" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Hapus</a>
							</td>
						</tr>
					</tbody>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box -->	

		<!-- Related Social Actions -->
		@include('admin.pages.social-action.related-list')

		<!-- Related Donations -->
		@include('admin.pages.donation.related-list')

		<!-- Related Photos -->
		@include('admin.pages.photo.related-list')

		<div class="modal" id="modal_no_head" tabindex="-1" role="dialog" aria-labelledby="defModalHead" aria-hidden="true">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                      <div class="modal-body" align="center">
                                        <span style="font-size:19px;"><b>Jadikan Gambar berikut sebagai default photo</b></span>
                                      </div>
                                        <div class="thumb" id="isiPhoto">
                                        	
                                        </div>
                                      <div class="modal-footer">
                                         <button type="button" id="delImage" data-del="" class="btn btn-success" data-dismiss="modal">Setuju</button>
                                      </div>
                                    </div>
                                  </div>
                      </div>
        <script type="text/javascript">
						var base_url = "{{ URL::to('') }}/social-target/setphoto?id={{ $social_target->id }}&image=";
					</script>
		{{ HTML::script('multiupload/js/gambarDefault.js'); }}

	</div>
</div>
@stop