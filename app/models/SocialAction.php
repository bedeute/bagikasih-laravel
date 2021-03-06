<?php

class SocialAction extends BaseModel {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'social_actions';

	protected $guarded = array('id');  // Important

	/**
	* checkSlugName
	*
	* @return (int) num
	*/
	
	public static function checkSlugName($str){
		return SocialAction::where('slug',$str)->count();
	}
	public function socialTarget()
	{
		return $this->belongsTo('SocialTarget', 'social_target_id');
	}

	public function category()
	{
		return $this->belongsTo('SocialActionCategory', 'social_action_category_id');
	}

	public function user()
	{
		return $this->belongsTo('User');
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

	// public function socialActionEvent(){
	// 	return $this->belongsTo('SocialActionEvent','id','social_action_id');
	// }

	public function SocialActionCategory(){
		return $this->belongsTo('SocialActionCategory');
	}


	// public function socialTarget(){
	// 	return $this->belongsTo('SocialTarget');
	// }
	
	public static function getById($input){
		if(SocialAction::with('socialTarget','user')->where('slug',$input)->count() == 1){
			return SocialAction::with('socialTarget','user')->where('slug',$input)->first();
		} else {
			return false;

		}
	}

	// input aksi sosial
	public static function StoreSocialAction($input){

				
	    unset($input['id']);

		$rules =  array(
			'name' => 'required',
			'description' => 'required|min:5',
			'stewardship' => 'required|min:5',
			'creator_fname' => 'required',
			'creator_lname' => 'required',
			'creator_email' => 'required|email',
			'bank_account_description' => 'required|min:5',
			'currency' => 'required',
			'total_donation_target' => 'required',
			'expired_at' => 'required',
		);
		// Mencatat pembuat aksi sosial
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
		if(!empty($input['expired_at'])){
			$expired_at           = preg_split("/([\/: ])/", $input['expired_at']);
			$input['expired_at']  = mktime((int) $expired_at[3], 
			    	(int) $expired_at[4],0,(int) $expired_at[0],(int) $expired_at[1],(int) $expired_at[2]);
		}
		else {
			$input['expired_at'] = '';
		}

		$validator = Validator::make($input, $rules);
		unset($input['creator_fname']);
		unset($input['creator_lname']);
		unset($input['creator_email']);

  	  	if ($validator->fails()){
  	 		return $validator->errors()->all();
 
	    } 
	    else {

	    		$updateInsert = array();

	    		$SocialAction = new SocialAction;
	    		$SocialAction->fill($input);
	    		$SocialAction->save();

	    		// update 
				$slug    =  Str::slug($input['name']);

	    		$checkSlug = SocialAction::where('slug',$slug)->where('id','!=',$SocialAction->id)->count();
		            
	            if($checkSlug > 0){
	                $updateInsert['slug'] = $slug."-".$SocialAction->id;
	            }
	            else{
	                $updateInsert['slug'] = $slug;
	            }
				// $updateInsert['slug'] = SocialAction::checkSlugName(Str::slug($input['name'])) > 0 ? 
				//     strtolower(Str::slug($input['name'])).$SocialAction->id : 
				//     strtolower(Str::slug($input['name']));
				    
				$photo = Photo::saveAvatar('social_actions', $SocialAction->id, $input['user_id']);
				$updateInsert['cover_photo_id'] = $photo['cover_photo_id'];

	    		$update = SocialAction::find($SocialAction->id);
				$update->fill($updateInsert);
				$update->save();

				$hasil = array('id' => $SocialAction->id, 'msg' => 'ok');

				return $hasil;
	    }
	}


	// update aksi sosial
	public static function StoreSocialActionFront($input){
		
	    unset($input['id']);

		$started_at = '';
		
		if(!empty($input['expired_at'])){
			$started_at  = preg_split("/([\/: ])/", $input['expired_at']);
		    $input['expired_at']  = mktime((int) $started_at[3], 
		    	(int) $started_at[4],0,(int) $started_at[0],(int) $started_at[1],(int) $started_at[2]);
		}
		else {
			$input['expired_at'] = '';
		}
	    
		$rules =  array(
			'name' => 'required',
			'description' => 'required|min:5',
			'stewardship' => 'required|min:5',
			'creator_fname' => 'required',
			'creator_lname' => 'required',
			'creator_email' => 'required|email',
			'bank_account_description' => 'required|min:5',
			'currency' => 'required',
			'total_donation_target' => 'required',
			'expired_at' => 'required',
		);
		// Mencatat pembuat aksi sosial
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
		
		unset($input['default_photo_id']);
		unset($input['cover_photo_id']);
		
		$validator = Validator::make($input, $rules);
		unset($input['creator_fname']);
		unset($input['creator_lname']);
		unset($input['creator_email']);

  	  	if ($validator->fails()){
  	 		return $validator->errors()->all();
	    } 
	    else {
	    		$SocialAction = new SocialAction;
	    		$SocialAction->fill($input);
	    		$SocialAction->save();
	    		// update 

				$photo = Photo::saveAvatar('social_actions', $SocialAction->id, $input['user_id']);
	    	
	    		$update = SocialAction::find($SocialAction->id);
			
				$update->fill(array(
				    'slug' => SocialAction::checkSlugName(Str::slug($input['name'])) > 0 ? 
				    strtolower(Str::slug($input['name'])).$SocialAction->id : 
				    strtolower(Str::slug($input['name'])),
				    'cover_photo_id' => $photo['cover_photo_id'],
				    'default_photo_id' => $photo['default_photo_id'],
				));
				$update->save();

				return "ok";
	    }
	}

	public static function getSocialActionFront(){

		$check = SocialAction::with('socialTarget','user','city')->where('status',1)->count();

		if($check > 0){
			return SocialAction::with('socialTarget','user','city')->where('status',1)->take(10)->orderBy('created_at','DESC')->get();			
		}
		else {
			return false;
		}
	}

	public static function UpdateSocialAction($input){

		$rules =  array(
			'name' => 'required',
			'description' => 'required|min:5',
			'stewardship' => 'required|min:5',
			'bank_account_description' => 'required|min:5',
			'currency' => 'required',
			'total_donation_target' => 'required',
			'expired_at' => 'required',
		 );
		
		$validator = Validator::make($input, $rules);

  	  	if ($validator->fails()){
  	 		return $validator->errors()->all();
	    } 
	    else {
	    	

	    	$id = $input['id'];
	    	
	    	unset($input['id']);
    		
    		if(!empty($input['expired_at'])){
				$started_at  = preg_split("/([\/: ])/", $input['expired_at']);
			    $input['expired_at']  = mktime((int) $started_at[3], 
			    	(int) $started_at[4],0,(int) $started_at[0],(int) $started_at[1],(int) $started_at[2]);
			}
			else{
				$input['expired_at'] = '';
			}

    		$getSlug =  SocialAction::where('id',$id)->first();

    		$slug    =  Str::slug($input['name']);

    		// jika input tidak sama dengan slug di database
			if (strcmp($input['name'], $getSlug['name']) != 0) {

	            $checkSlug = SocialAction::where('slug',$slug)->where('id','!=',$id)->count();
	            
	            if($checkSlug > 0){
	                $input['slug'] = $slug."-".$id;
	            }
	            else{
	                $input['slug'] = $slug;
	            }

	        } 
			

			$photo = Photo::updateAvatar($getSlug['cover_photo_id'],'social_actions',$getSlug->id);

			$input['cover_photo_id'] = $photo['cover_photo_id'];
	        $SocialAction = SocialAction::find($id);
    		$SocialAction->fill($input);
    		$SocialAction->save();
			

    		return "ok";
	  
	    }
	}

}