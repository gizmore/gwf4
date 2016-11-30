var GWF_User = function(json) {
	
	this.JSON = json;
	this.GUEST_NAME = json.user_name;
	this.HAS_GUEST_NAME = false;
	
	this.authenticated = function() { return this.id() > 0; };
	this.guest = function() { return !this.authenticated(); };
	
	this.id = function(id) { if(id) this.JSON.user_id = id; return this.JSON.user_id; };
	this.secret = function() { return GWF_CONFIG.wss_secret; };
	this.name = function(name) { if(name) this.JSON.user_name = name; return this.JSON.user_name; };
	
	this.guestName = function(name) { if(name) { this.GUEST_NAME = name; this.HAS_GUEST_NAME = true; } return this.GUEST_NAME; };
	this.hasGuestName = function() { return this.HAS_GUEST_NAME; };

	this.displayName = function() { return this.guest() ? this.guestName() : this.name(); };
	
};
