var GWF_User = function(json) {
	
	this.JSON = json;
	
	this.authenticated = function() { return this.id() > 0; };
	
	this.id = function(id) { if(id) this.JSON.user_id = id; return this.JSON.user_id; };
	this.name = function(name) { if(name) this.JSON.user_name = name; return this.JSON.user_name; };
	this.secret = function() { return GWF_CONFIG.wss_secret; };
};
