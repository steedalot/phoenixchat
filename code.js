const phoenixchat = {
	data() {
		return {
			messages: [],
            chatroomBox: "",
            chatroom: "6775",
            date: "1641040000",
            userBox: "",
            messageBox: "",


		}
	},
    created() {
        setInterval(this.getMessages, 10000);
    },
	mounted() {
		// this.keys.random = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
	},
	methods: {

        getMessages: function() {
            this.server_get(this.chatroom, this.date, this.messages_handler);
        },

        changeTime: function(message) {
            var date = new Date(parseInt(message.date) * 1000);
            var weekdays = new Array("Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag", "Sonntag");
            message.realDate = weekdays[date.getDay()] + ", " + date.toLocaleDateString() + ", " + date.toLocaleTimeString() + " Uhr";
        },

        findRoom: function() {
            this.chatroom = this.chatroomBox;
            this.chatroomBox = "";
            this.getMessages(this.chatroom, this.date, this.messages_handler);
        },

        addMessage: function() {
            this.server_post(this.chatroom, this.messageBox, this.userBox, this.addMessage_handler)
        },

        addMessage_handler: function(result) {
            if (result) {
                console.log("Neue Nachricht abgesetzt!")
                this.messageBox = ""
            }
        },

        messages_handler: function(result) {
            if (result) {
                this.messages = result;
                this.messages.forEach(this.changeTime);
            }
            else {
                console.log("Die Nachrichten konnte nicht verarbeitet werden.");
            }
            //mal sehen
        },

        server_post: function(room, message, user, callback) {
            fetch("index.php", data= {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    "action": "add",
                    "chat": room,
                    "message": message,
                    "user": user
                })
            }).then((response) => {
                if (response.ok) {
                    callback(true)
                }
                else {
                    callback(false)
                }
            });
        },

        server_get: function(room, date, callback) {
            fetch("index.php", data = {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    "action": "get",
                    "chat": room,
                    "date": date
                })
            }).then((response) => {
                if (response.ok) {
                    response.json().then(data => {
                        callback(data);
                    });
                }
                else {
                    console.log("Fehler beim Abrufen der Daten...");
                }
            });
            
        }
	}
}

Vue.createApp(phoenixchat).mount('#body')