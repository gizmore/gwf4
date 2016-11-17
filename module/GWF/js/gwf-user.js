var GWF_User = function(json) {
	
	this.JSON = json;
	
	this.authenticated = function() { return this.id() > 0; };
	
	this.id = function(id) { if(id) this.JSON.user_id = id; return this.JSON.user_id; };
};
