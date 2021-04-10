<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\ResponseController as ResponseController;
use Validator;
use Image;
use App\Media;
class MediaController extends ResponseController
{
  
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->guard = "api";
    }
  
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function PhotosPost(Request $request)
    {
        $this->validate($request, [
            'photos.*' => 'required|mimes:jpeg,png,jpg,gif,svg',
            'videos.*' => 'required|file|mimes:mp4,mov,avi',
        ]);
		$media = [];
        if($files = $request->file('photos')) {
       	    // Define upload path
			$photocount = Media::where('media_type',1)->where('user_id',auth($this->guard)->user()->id)->count();
			$uploadphotos  = count($files);
			$limit = $uploadphotos + $photocount;
			if($limit >= 5 ){
				$message = "Maximum 4 photos upload";
				$success = [
					'message' => $message,
					'success' => false
				];
				return $this->sendResponse($success);    
			}
            $destinationPath = public_path('/user_media/'); // upload path
            foreach($files as $img) {
                $input['media_name'] = time().rand().'.'.$img->getClientOriginalExtension();
                
                $thumbdestinationPath = public_path('/thumbnail/');
                $imgth = Image::make($img->getRealPath());
                $imgth->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($thumbdestinationPath.'/'.$input['media_name']);
                
				// Upload Orginal Image           
	            
	            $img->move($destinationPath, $input['media_name']);
	        	// Save In Database
				$input['user_id'] = auth($this->guard)->user()->id;
                $input['media_type'] = 1;
                $input['media_path'] = '/public/user_media/'.$input['media_name'];
                $media[] = Media::create($input);
			}
        }
        
        if($vfiles = $request->file('videos')) {
			
			$videocount = Media::where('media_type',2)->where('user_id',auth($this->guard)->user()->id)->count();
       	    $uploadvideos  = count($vfiles);
			$limit = $uploadvideos + $videocount;
			if($limit >= 5 ){
				$message = "Maximum 4 video upload";
				$success = [
					'message' => $message,
					'success' => false
				];
				return $this->sendResponse($success);    
			}
       	    // Define upload path
            $destinationPath = public_path('/user_media/'); // upload path
            foreach($vfiles as $vimg) {
                $input['media_name'] = time().rand().'.'.$vimg->getClientOriginalExtension();
                
				// Upload Orginal Image           
	            $vimg->move($destinationPath, $input['media_name']);
	        	// Save In Database
				$input['user_id'] = auth($this->guard)->user()->id;
                $input['media_type'] = 2;
                $input['media_path'] = '/public/user_media/'.$input['media_name'];
                $media[] = Media::create($input);
			}
        }
        
        $success = [
            'message' => 'Photos updated',
            'success' => true,
            'data' => $media,
        ];
        return $this->sendResponse($success);
    }
	
	public function destroy($id)
    {
		$user = auth($this->guard)->user();	
        if(Media::where('id',$id)->where('user_id',$user->id)->delete()){
			$success = [
				'message' => 'Photo Deleted',
				'success' => true,
			];
		} else{
			$success = [
				'message' => 'Photo already deleted',
				'success' => false
			];
		}
        return $this->sendResponse($success);
    }  
   
}