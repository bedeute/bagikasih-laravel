	<?php

class SocialTarget extends BaseModel {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'social_targets';

	protected $guarded = array('id');  // Important


	public function category()
	{
		return $this->belongsTo('SocialTargetCategory', 'social_target_category_id');
	}

	public function user()
	{
		return $this->belongsTo('User');
	}
	public static function checkSlugName($str){
		return SocialTarget::where('slug',$str)->count();
	}
	public function city()
	{
		return $this->belongsTo('City');
	}

	public function defaultPhoto()
	{
		return $this->belongsTo('Photo', 'default_photo_id');
	}

	public function coverPhoto()
	{
		return $this->belongsTo('Photo', 'cover_photo_id');
	}

	public static function getAll(){
		
		$check = SocialTarget::where('status',1)->count();

		if($check > 0){
			return SocialTarget::where('status',1)->get();			
		}
		else{
			return false;
		}
	}

	public static function getById($id){
		if(SocialTarget::where('slug',$input)->count() == 1){

			return SocialTarget::where('slug',$input)->first();

		}
		else{
			
			return false;

		}
	}

	public static function add($input)
	{
		// for validation
		$validation = array(
			'Kategori' 				=> $input['social_target_category_id'],
			'Nama'					=> $input['name'],
			'Tentang_target_sosial' => $input['description'],
			'Kepengurusan' 			=> $input['stewardship'],
			'Kota'					=> $input['city_id'],
			'Alamat'				=> $input['address'],
			'No_telp'				=> $input['phone_number'],
			'Email'					=> $input['email'],
			'Sosial_media'			=> $input['social_media_urls'],
			'creator_fname' 	=> $input['creator_fname'],
			'creator_lname'		=> $input['creator_lname'],
			'creator_email' 	=> $input['creator_email'],
		);

		$rules =  array(
			'Email'				=> 'required|email',
			'creator_fname' 	=> 'required',
			'creator_lname'		=> 'required',
			'creator_email' 	=> 'required|email',
			'Kategori'				=> 'required',
			'Kota'					=> 'required|exists:cities,id',
			'Tentang_target_sosial'	=> 'required|min:20',
			'Alamat'				=> 'required|max:100',
			'No_telp'				=> 'required|max:20',
		);

		// Mencatat pembuat target sosial
		if(Auth::check()){
			$input['user_id'] = Auth::user()->id;
		} else {
			// Check apakah user ada di database
			$check_user = User::where('email',$input['creator_email']);
			if($check_user->count() > 0){
				$input['user_id'] = $check_user->pluck('id');
			} else {
				// Membuat user baru dengan status draft (status:2)
				$post = new User;
				$post->firstname = $input['creator_fname'];
				$post->lastname = $input['creator_lname'];
				$post->email = $input['creator_email'];
	            $post->status = 2;
	            $post->save();
				$input['user_id'] = $post->id;
			}
		}
		$validator = Validator::make($validation, $rules);
		unset($input['creator_fname']);
		unset($input['creator_lname']);
		unset($input['creator_email']);

  	  	if ($validator->fails())
  	  	{
  	 		return array(
  	 			'success' => false,
  	 			'errors' => $validator->errors()->all(),
  	 		);
	    } 
	    else {
	    	//try {

	    		$social_target = new SocialTarget;

	    		foreach($input as $column => $value)
    			{
    				$social_target->$column = $value;
    			}

    			// create slug
    			$social_target->slug = Str::slug($social_target->name);
	    		
	    		$social_target->save();

	    		// digunakan untuk mengambil id user yang belum login
				if(!Auth::check()) {
					Session::put('update_id',$social_target->id);
				}

				// check slug
				if (SocialTarget::checkSlugName($social_target->slug) > 1)
				{
					$social_target->slug = $social_target->slug . '-' . $social_target->id;
					$social_target->save();
				}

	    		return array(
		 			'success' => true,
		 			'data' => $social_target,
		 		);
	   
	    	/*} catch (Exception $e){
				
	    		return array('success' => false);
	    	}*/
	    }
	}

	public static function updateUserId($update_id)
	{
		$social_target = SocialTarget::where('id', '=', $update_id)
    								->where('user_id', '=', 0)
    								->first();

		if ($social_target)
		{
			$social_target->user_id = Auth::user()->id;
			$social_target->save();

			return array(
  	 			'success' => true,
  	 			'data' => $social_target,
  	 		);
		}

		return false;
	}


	public static function StoreSocialTarget($input) {

		unset($input['id']);

		$rules =  array(
			'name'=> 'required',
			'stewardship' => 'required|min:5',
			'description' => 'required|min:5',
			'address' => 'required',
			'phone_number' => 'required',
		 );

		$validator = Validator::make($input, $rules);

  	  	if ($validator->fails()) {
  	 		return $validator->errors()->all();
	    } 
	    else {

	    		$SocialTarget = new SocialTarget;
	    		$SocialTarget->fill($input);
	    		$SocialTarget->save();
	    		
	    		// update 
	    		$slug    =  Str::slug($input['name']);

	    		$checkSlug = SocialTarget::where('slug',$slug)->where('id','!=',$SocialTarget->id)->count();
		            
	            if($checkSlug > 0){
	                $updateInsert['slug'] = $slug."-".$SocialTarget->id;
	            }
	            else{
	                $updateInsert['slug'] = $slug;
	            }

				// $updateInsert['slug'] = SocialAction::checkSlugName(Str::slug($input['name'])) > 0 ? 
				//     strtolower(Str::slug($input['name'])).$SocialTarget->id : 
				//     strtolower(Str::slug($input['name']));
				    
				$photo = Photo::saveAvatar('social_targets', $SocialTarget->id);
				$updateInsert['cover_photo_id'] = $photo['cover_photo_id'];

	    		$update = SocialTarget::find($SocialTarget->id);
				$update->fill($updateInsert);
				$update->save();

				$hasil = array('id' => $SocialTarget->id, 'msg' => 'ok');
	    		return $hasil;
	   
	    }

	}

	public static function getSocialTargetFront(){
		
		$check = SocialTarget::with(array('city', 'category','user'))->where('status',1)->count();

		if($check > 0){
			return SocialTarget::with(array('city', 'category','user'))->where('status',1)->take(5)->orderBy('created_at','DESC')->get();			
		}
		else{
			return false;
		}

	}

	public static function UpdateSocialTarget($input) {

		
	    $id = $input['id'];
	
		$rules =  array(
			'name'=> 'required',
			'stewardship' => 'required|min:5',
			'description' => 'required|min:5',
			'address' => 'required',
			'phone_number' => 'required',
		 );

		$validator = Validator::make($input, $rules);

  	  	if ($validator->fails()) {
  	 		return $validator->errors()->all();
	    } 
	    else {
	    		$getSlug =  SocialTarget::where('id',$id)->first();

	    		$slug    =  Str::slug($input['name']);

	    		// jika input tidak sama dengan slug di database
				if (strcmp($input['name'], $getSlug['name']) != 0) {

		            $checkSlug = SocialTarget::where('slug',$slug)->where('id','!=',$id)->count();
		            
		            if($checkSlug > 0){
		                $input['slug'] = $slug."-".$id;
		            }
		            else{
		                $input['slug'] = $slug;
		            }

		        } 
				
				$photo = Photo::updateAvatar($getSlug['cover_photo_id'],'social_targets',$getSlug->id);
				
				$input['cover_photo_id'] = $photo['cover_photo_id'];
		        $SocialTarget = SocialTarget::find($id);
	    		$SocialTarget->fill($input);
	    		$SocialTarget->save();
	    		return "ok";	   
	    	
	    }

	}
}