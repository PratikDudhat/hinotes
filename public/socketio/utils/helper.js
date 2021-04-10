/**
* Real Time chatting app
* @author Shashank Tiwari
*/

'user strict';
const DB = require('./db');

class Helper{

	currentDateTime()
    {
          //format current data in YYYY-mm-dd
          var currentDate = new Date();
          var yyyy = currentDate.getFullYear().toString();
          var mm = (currentDate.getMonth() + 1).toString(); // getMonth() is zero-based
          var dd = currentDate.getDate().toString();
          var hh = currentDate.getHours().toString();
          hh = hh > 9 ? hh : '0' + hh;
          var min = currentDate.getMinutes().toString();
          min = min > 9 ? min : '0' + min;
          var ss = currentDate.getSeconds().toString();
          ss = ss > 9 ? ss : '0' + ss;
          var new_date = new Date(yyyy, mm, dd, hh, min, ss).toTimeString();
          //var final_time = new_date.replace('GMT', '');
          var final_time = new_date.replace(' (EDT)', '').replace('GMT', '').replace('(UTC)', '');
          return yyyy + '-' + (mm[1] ? mm : "0" + mm[0]) + '-' + (dd[1] ? dd : "0" + dd[0]) + ' ' + hh + ':' + min + ':' + ss;
    }

	constructor(app){
		this.db = DB;
	}

	/*async userNameCheck (username){
		return await this.db.query(`SELECT count(username) as count FROM user WHERE LOWER(username) = ?`, `${username}`);
	}

	async registerUser(params){
		try {
			return await this.db.query("INSERT INTO user (`username`,`password`,`online`) VALUES (?,?,?)", [params['username'],params['password'],'Y']);
		} catch (error) {
			console.error(error);
			return null;
		}
	}

	async loginUser(params){
		try {
			return await this.db.query(`SELECT id FROM user WHERE LOWER(username) = ? AND password = ?`, [params.username,params.password]);
		} catch (error) {
			return null;
		}
	}

	async userSessionCheck(userId){
		try {
			const result = await this.db.query(`SELECT online,username FROM user WHERE id = ? AND online = ?`, [userId,'Y']);
			if(result !== null){
				return result[0]['username'];
			}else{
				return null;
			}
		} catch (error) {
			return null;
		}
	}

	async addSocketId(userId, userSocketId){
		try {
			return await this.db.query(`UPDATE user SET socketid = ?, online= ? WHERE id = ?`, [userSocketId,'Y',userId]);
		} catch (error) {
			console.log(error);
			return null;
		}
	}

	async isUserLoggedOut(userSocketId){
		try {
			return await this.db.query(`SELECT online FROM user WHERE socketid = ?`, [userSocketId]);
		} catch (error) {
			return null;
		}
	}

	async logoutUser(userSocketId){
		return await this.db.query(`UPDATE user SET socketid = ?, online= ? WHERE socketid = ?`, ['','N',userSocketId]);
	}

	getChatList(userId, userSocketId){
		try {
			return Promise.all([
				this.db.query(`SELECT id,username,online,socketid FROM user WHERE id = ?`, [userId]),
				this.db.query(`SELECT id,username,online,socketid FROM user WHERE online = ? and socketid != ?`, ['Y',userSocketId])
			]).then( (response) => {
				return {
					userinfo : response[0].length > 0 ? response[0][0] : response[0],
					chatlist : response[1]
				};
			}).catch( (error) => {
				console.warn(error);
				return (null);
			});
		} catch (error) {
			console.warn(error);
			return null;
		}
	}*/

	async insertMessages(params){
		var serverDate = this.currentDateTime();
		try {
			return await this.db.query(
				"INSERT INTO messages (`sender_id`,`receiver_id`,`message`,`file`,`thumbnail`,`file_url`,`file_type`,`file_size`,`conversation_id`,`is_read`,`delete_status`,`created_at`,`updated_at`) values (?,?,?,?,?,?,?,?,?,?,?,?,?)",
				[params.sender_id, params.receiver_id, params.message,params.file, params.thumbnail, params.file_url,params.file_type, params.file_size, params.conversation_id, params.is_read, params.delete_status,serverDate,serverDate]
			);
		} catch (error) {
			console.warn(error);
			return null;
		}		
	}
	
	async getMessages(params){
		try {
			return await this.db.query(
				`SELECT *,DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at,DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at FROM messages WHERE 
					conversation_id = ? ORDER BY id ASC LIMIT ?, 15				
				`,
				[params.conversation_id, params.offset]
			);
		} catch (error) {
			console.warn(error);
			return null;
		}
	}
}
module.exports = new Helper();