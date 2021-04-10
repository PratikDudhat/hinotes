/**
* Real Time chatting app
* @author Piyush Tejani
*/
'use strict';

const path = require('path');
const helper = require('./helper');

class Socket{

    constructor(socket){
        this.io = socket;
    }
    
    socketEvents(){

        this.io.on('connection', (socket) => {
            console.log('user connected');

            socket.on('room', function(room)
            {
                socket.join(room);
                console.log("Room joined == " + room);
                var data = '';
                //this.io.sockets.in(room).emit('room_join',data);
				//this.io.sockets.in(room).emit('room_join',data);
            });
           
            /**
            * send the messages to the user
            */
            socket.on('send-message', async (data) => {
                console.log('send-message');
                const sqlResult = await helper.insertMessages({
                    sender_id: data.sender_id,
                    receiver_id: data.receiver_id,
                    message: data.message,
                    file: data.file,
                    thumbnail: data.thumbnail,
                    file_url: data.file_url,
                    file_type: data.file_type,
                    file_size: data.file_size,
                    is_read: 0,
                    conversation_id: data.conversation_id,
                    delete_status: 0,
                });
				//console.log(sqlResult);
                data.id = sqlResult.insertId; 
                this.io.sockets.in(data.conversation_id).emit('new_message',data);
                
                //helper.sendMessageNotification(data);

                /*  if (data.message === '') {
                        
                        this.io.to(socket.id).emit(`add-message-response`,`Message cant be empty`); 

                    }else if(data.fromUserId === ''){
                        
                        this.io.to(socket.id).emit(`add-message-response`,`Unexpected error, Login again.`); 

                    }else if(data.toUserId === ''){
                        
                        this.io.to(socket.id).emit(`add-message-response`,`Select a user to chat.`); 

                    }else{                    
                        let toSocketId = data.toSocketId;
                        const sqlResult = await helper.insertMessages({
                            conversation_id: data.conversation_id,
                            user_id: data.user_id,
                            message: data.message
                        });

                        this.io.to(toSocketId).emit(`add-message-response`, data); 
                    }               
                */
            });
			socket.on('get_chat_message', async (data) => {				
			
				var offset = data.offset * 15;	
				const sqlResult = await helper.getMessages({
                    conversation_id: data.conversation_id,
                    offset: offset,
                });
				
				var response = {};
				if ((sqlResult.length > 0))
				{	
					 response.data = sqlResult;
					 response.offset = data.offset++;
					 //updateIsRead(data.conversation_id,data.user_id);
					 this.io.sockets.in(data.conversation_id).emit('list_message',response);
				}
				else
				{
					response.data = [];
					response.offset = data.offset; 
					this.io.sockets.in(data.conversation_id).emit('list_message',response);
				}
			});
	
            socket.on('disconnect',async ()=>{
                console.log('disconnect')
            });

        });

    }
    
    socketConfig(){
        console.log('socketConfig')
        /*this.io.use( async (socket, next) => {
            let userId = socket.request._query['userId'];
            let userSocketId = socket.id;          
            const response = await helper.addSocketId( userId, userSocketId);
            if(response &&  response !== null){
                next();
            }else{
                console.error(`Socket connection failed, for  user Id ${userId}.`);
            }
        });*/

        this.socketEvents();
    }
}
module.exports = Socket;