var GWF_User = function(json) {
	
	this.JSON = json;
	
	this.authenticated = function() { return this.id() > 0; };
	this.guest = function() { return !this.authenticated(); };
	
	this.id = function(id) { if(id) this.JSON.user_id = id; return this.JSON.user_id; };
	this.secret = function() { return GWF_CONFIG.wss_secret; };
	this.name = function(name) { if(name) this.JSON.user_name = name; return this.JSON.user_name; };
	this.gender = function(gender) { if(gender) this.JSON.user_gender = gender; return this.JSON.user_gender; };
	this.guestName = function(name) { if(name) this.JSON.user_guest_name = name; return this.JSON.user_guest_name; };
	this.hasGuestName = function() { return !!this.JSON.user_guest_name; };

	this.displayName = function() { return this.hasGuestName() ? this.guestName() : this.name(); };
};
