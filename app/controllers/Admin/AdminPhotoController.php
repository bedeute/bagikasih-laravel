<?php

class AdminPhotoController extends AdminBaseController {

	/*
	|--------------------------------------------------------------------------
	| Photo Controller
	|--------------------------------------------------------------------------
	|
	| 
	|
	*/
	private $_menu = 'photos';

	public function index()
	{
		// init
		$photo 	= Photo::all();

		$data 	= array(
			'menu'			=> 'Photo',
			'title' 		=> 'Photo',
			'description' 	=> '',
			'breadcrumb' 	=> array(
				'photo' 	=> route('admin.photo'),
			),
		);

		$data['data'] 		= $photo;

		return View::make('admin.pages.photo.index')
					->with($data);
	}

	public function show($id){

	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function create()
	{		
		$data = array(
			'menu' 	=> $this->_menu,
			'title' => 'Photo+',
			'description' 	=> '',
			'breadcrumb' 	=> array(
				'Social Target Category' 	=> route('admin.photo'),
			),
			'options'		=> ['social_targets' => 'Social Targets', 'social_actions' => 'Social Actions', 'events' => 'Events'],
		);

		return View::make('admin.pages.photo.create', $data);
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function store()
	{		
		$validator = $this->storeValid();
		if($validator->passes())
		{		
			$input 		= $this->storeInput();
			$photo 		= Photo::add($input);			
			if($photo)
			{			
				return Redirect::route('admin.photo')->withStatuses(['add'=>'Tambah Berhasil!']);
			}	
			return Redirect::route('admin.photo.create')->withErrors(['add'=>'Tambah Gagal!'])->withInput();
		}
		return Redirect::route('admin.photo.create')->withErrors($validator)->withInput();
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	protected function storeInput()
	{
		return ['name'		=> Input::get('name'), 				
				'status' 	=> Input::get('status')];				
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	protected function storeValid()
	{
		return Validator::make(Input::all(), ['name'=> 'required', 'status'=> 'required']);
	}

	public function update(Photo $photo)
	{		
		$data 		= array(
			'menu' 				=> $this->_menu,
			'title' 			=> 'Photo+',
			'description' 		=> '',
			'breadcrumb' 		=> array(
				'Negara' 		=> route('admin.photo')			
			),			
			'data' 				=> $photo,
		);

		return View::make('admin.pages.photo.edit', $data);
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function updateDo()
	{
		$validator = $this->updateValid();
		if($validator->passes())
		{
			$input = $this->updateInput();
			
			$save  = Photo::edit($input);			
			if($save)
			{
				return Redirect::route('admin.photo')->withStatuses(['edit'=> 'Data Berhasil di edit!']);
			}
			return Redirect::route('admin.photo')->withErrors(['edit'=> 'Data Gagal di edit!']);
		}
		return Redirect::back()->withErrors($validator)->withInput();
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	protected function updateInput()
	{
		return ['id'		=> Input::get('id'), 
				'name'		=> Input::get('name'),				
				'status'	=> Input::get('status')];
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	protected function updateValid()
	{
		return Validator::make(Input::all(), [
								'id'		=> 'required', 
								'name'		=> 'required', 								
								'status'	=> 'required']);
	}

	public function delete(Photo $photo)
	{
		$data = array(
			'menu' 	=> $this->_menu,
			'title' => 'Photo+',
			'description' => '',
			'breadcrumb' => array(
				'Negara' => route('admin.photo')			
			),
			'data' 	=> $photo
		);
		return View::make('admin.pages.photo.delete', $data);
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function deleteDo()
	{	
		$id 			= Input::get('id');
		$validator 		= $this->deleteValid($id);
		if($validator)
		{		
			$photo = Photo::remove($id);
			if($photo)
			{
				return Redirect::route('admin.photo')->withStatuses(['delete' => 'Hapus Sukses!']);
			}
			return Redirect::route('admin.photo')->withErrors(['delete' => 'Hapus Gagal!']);
		}
		return Redirect::route('admin.photo')->withErrors(['used'=> 'Maaf, data ini masih digunakan! Hapus Gagal.']);
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	protected function deleteValid($id)
	{
		$exist =  Photo::isExist($id);
		if($exist)
		{
			return false;
		}
		return true;
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function ajax()
	{
		if(Request::ajax())
		{
			$type = Input::get('type_name');	
			$data =	['data'=> Photo::listId($type)];	
			return View::make('admin.pages.photo.ajax', $data);	
		}
		return 'Whoops! Not ajax Request';
	}	
}