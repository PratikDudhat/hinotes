<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Validator;
use App\Conversation;
use Illuminate\Support\Facades\DB;
use DateTime;
use App\User;
use App\Message;
use Carbon\Carbon;
use App\Http\Controllers\Api\ResponseController as ResponseController;

class MessageController extends ResponseController
{   
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->guard = "api";
    }
    function send_push($title, $body, $tokens, $user_id, $con_id, $total,$u_name,$u_img) {
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //Custom data to be sent with the push

        $data = array
            (
            'message' => 'here is a message. message',
            'title' => $title,
            'body' => $body,
            'smallIcon' => 'small_icon',
            'some data' => 'Some Data',
            'Another Data' => 'Another Data',
            'click_action' => 'OPEN_ACTIVITY',
            'sound' => 'default',

        );


        $data2=array(
            'user_id'=>$user_id,
            'con_id' => $con_id,
            'total' => $total,
            'name' => $u_name,
            'image' => $u_img
        );

        //This array contains, the token and the notification. The 'to' attribute stores the token.
        $arrayToSend = array(
            'registration_ids' => $tokens,
            'notification' => $data,
            'data' => $data2,
            'priority' => 'high',
        );

        //Generating JSON encoded string form the above array.
        $json = json_encode($arrayToSend);
        //Setup headers:
        $headers = array();
        $headers[] = 'Content-Type: application/json';

        $headers[] = 'Authorization: key= AIzaSyCrWenpawLiljuUw9PRb8VTNMOeMRCsryQ';

        //Setup curl, add headers and post parameters.

        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        //Send the request
        $response = curl_exec($ch);
        // return $response;
        //Close request
        curl_close($ch);
//      return $response;

        // echo $response;

    }

	
	public function get_conversation_id(Request $request)
    {
		$user = auth($this->guard)->user();
		$validator = Validator::make($request->all(), [
            'receiver_id' => 'required',
        ]);
		
        if($validator->fails()){
            $message = $validator->errors()->first();
            $success = [
                'message' => $message,
                'success' => false
            ];
            return $this->sendResponse($success);    
        }
		
        $sender_id = $user->id;
        $receiver_id = $request->receiver_id;
        $check_conversation = Conversation::whereRaw('(sender_id='.$sender_id.' AND receiver_id='.$receiver_id.') OR (sender_id='.$receiver_id.' AND receiver_id='.$sender_id.')')->first();

        if($check_conversation)
		{
            $con_id = $check_conversation->id;   
        }
        else
		{            
            $get_latest_coversation = Conversation::create(['sender_id' => $sender_id, 'receiver_id' => $receiver_id, 'last_message' => "No Message"]);
            $con_id = $get_latest_coversation->id;
        }
        $success = [
                'message' =>"Conversation Created",
                'success' => true,
				'conversation_id' => $con_id
            ];
        return $this->sendResponse($success);
    }

	public function get_message_conversation(Request $request)
    {
		$user = auth($this->guard)->user();
		$validator = Validator::make($request->all(), [
            'conversation_id' => 'required',
        ]);
		
        if($validator->fails()){
            $message = $validator->errors()->first();
            $success = [
                'message' => $message,
                'success' => false
            ];
            return $this->sendResponse($success);    
        }
		$limit = 10;
		$nextpage = (($request->page??1)+1);
        
        $conversation_id = $request->conversation_id;
		//DB::enableQueryLog();
        $conversation = DB::table('messages')->where('conversation_id',$conversation_id)->orderBy('created_at','DESC')->limit($limit)->offset((($request->page??1)-1)*$limit)->get();
		//dd(\DB::getQueryLog());
		$success = [
                'message' => "Messages",
                'success' => true,
                'nextpage' => $nextpage,
                'data' => $conversation,
        ];
        return $this->sendResponse($success);
    }
	
	public function get_conversation_user_list(Request $request)
    {
		$userdata = auth($this->guard)->user();
        $user = $userdata->id;
		if(!$user){
			$message = "Unauthorize";
            $success = [
                'message' => $message,
                'success' => false
            ];
            return $this->sendResponse($success); 
		}
		
        $list_of_conversations_ids = DB::select("SELECT conversation_id FROM `messages` where receiver_id = $user AND is_read = 1 GROUP BY conversation_id ");
        
        $contacts = DB::table('conversations')
        ->join('users', 'conversations.sender_id', '=', 'users.id')
        ->join('users as users_1', 'conversations.receiver_id', '=', 'users_1.id')
        ->select('conversations.id as cnv_id',
        'users.name as s_name','users.id as s_id', 'users.email as s_email', 'users.username as s_username','users.avatar as s_avatar',
        'users_1.name as r_name','users_1.id as r_id', 'users_1.email as r_email','users_1.username as r_username','users_1.avatar as r_avatar',
        'conversations.id as conversation_id', 'conversations.last_message', 'conversations.updated_at as created_at', 'conversations.updated_at')
        ->whereRaw(' (conversations.sender_id='.$user.' OR conversations.receiver_id='.$user.') AND (conversations.status != '.$user.' AND conversations.status != 2) ')
        ->orderBy('conversations.updated_at', 'desc')
        ->get();

        $final = array();
        for($i=0; $i<count($contacts); $i++ ){
            
            $date = Carbon::createFromTimeStamp(strtotime($contacts[$i]->updated_at))->diffForHumans();
            
            $total_messages = Message::where('conversation_id', $contacts[$i]->conversation_id)->where('delete_status','!=',$user)->count();
            
            if($contacts[$i]->s_id == $user){
                $final[$i]['id'] = $contacts[$i]->r_id;
                //$final[$i]['name'] = $contacts[$i]->r_name;
                $final[$i]['email'] = $contacts[$i]->r_email;
                $final[$i]['username'] = $contacts[$i]->r_username;
                $final[$i]['last_message'] = $contacts[$i]->last_message;
                $final[$i]['conversation_id'] = $contacts[$i]->conversation_id;
				$final[$i]['created_at'] = $contacts[$i]->created_at;
                $final[$i]['conversation_updated_at'] = $date;
                $final[$i]['profileUrl'] = ($contacts[$i]->r_avatar!='')?url($contacts[$i]->r_avatar):"";
                $final[$i]['total_messages'] = $total_messages;
                
            }
            else if($contacts[$i]->r_id == $user){
                $final[$i]['id'] = $contacts[$i]->s_id;
                //$final[$i]['name'] = $contacts[$i]->s_name;
                $final[$i]['email'] = $contacts[$i]->s_email;
                $final[$i]['username'] = $contacts[$i]->s_username;
                $final[$i]['last_message'] = $contacts[$i]->last_message;
                $final[$i]['conversation_id'] = $contacts[$i]->conversation_id;
                $final[$i]['created_at'] = $contacts[$i]->created_at;
                $final[$i]['conversation_updated_at'] = $date;
                $final[$i]['profileUrl'] =  ($contacts[$i]->s_avatar!='')?url($contacts[$i]->s_avatar):"";
                $final[$i]['total_messages'] = $total_messages;
            }
            else{

            }
        }
		$success = [
                'message' =>"Conversation List",
                'success' => true,
                'data' => $final
            ];
        return $this->sendResponse($success);
    }
	
	public function delete_message(Request $request,$id)
    {
		$user = auth($this->guard)->user();
        $message_id = $id;
        $check_conversation = DB::table('messages')->where('id',$message_id)->delete();
        $success = [
                'message' =>"Message Deleted",
                'success' => true
            ];
        return $this->sendResponse($success);
    }
	
	public function send_message_media(Request $request)
    {
		$message = [];
        if(request()->has('file')){
			$file = $request->file('file');
			if(request()->has('thumbnail'))
			{
				$thumbnail = $request->file('thumbnail');
				$thumbnail_original_name = $thumbnail->getClientOriginalName();
				$thumbnailname = time(). '.' .$thumbnail->getClientOriginalName();
				$thumbnailname = str_replace(' ', '', $thumbnailname);
				$location = app()->basePath('public/chat_files/thumbnail/');
				$thumbnail->move($location, $thumbnailname);    
				$thumbnailname = 'public/chat_files/thumbnail/'.$thumbnailname;
			}
			else
			{
				$thumbnailname = "";
			}
			
			$file_original_name = $file->getClientOriginalName();
			$filename = time(). '.' .$file->getClientOriginalName();
			$filename = str_replace(' ', '', $filename);
			$location = app()->basePath('public/chat_files/');
			$file->move($location, $filename);
					
			$file_type = $request->file_type;
			//$file_size = $request->file_size;
					
			if(!empty($filename)){
				$filename = url('chat_files/'.$filename);
			}
			if(!empty($thumbnailname)){
				$thumbnailname = url('chat_files/'.$thumbnailname);
			}
			
			$message= [				
				'file'=> $file_original_name,
				'file_url'=> $filename,
				'thumbnail'=> $thumbnailname,
				'file_type'=> $file_type,
				//'file_size'=> $file_size,
			];
		}
		$success = [
                'message' =>"Media Uploaded",
                'success' => true,
                'data' => $message
            ];
        return $this->sendResponse($success);
    }

  public function search_conversation_list(Request $request)
    {
        $user = auth($this->guard)->user();
      
        $limit = 10;
        $nextpage = (($request->page??1)+1);
        
        $conversation_id = $request->get('conversation_id');
        $message = $request->get('message');
        //DB::enableQueryLog();
        $conversation = DB::table('messages');
                            $conversation->where('conversation_id',$conversation_id);
                            if(!empty($message)){
                              $conversation->where('message','like','%'.$message.'%');
                            }
                            $conversation->limit($limit);
                            $conversation->offset((($request->page??1)-1)*$limit);
      $conversationlist   = $conversation->get();
        //dd(\DB::getQueryLog());
        if($conversationlist){
             $success = [
                'message' => "Search Message",
                'success' => true,
                'nextpage' => $nextpage,
                'data' => $conversationlist,
             ];
        }
       else{
             $success = [
                'message' => "Message not found",
                'success' => false
             ];
       }
        return $this->sendResponse($success);
    }
}
