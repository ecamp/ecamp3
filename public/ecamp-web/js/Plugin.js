/* move these lines to better location */
window.Ecamp = {};
Ecamp.Web = {};

Ecamp.Web.Plugin = function(config) {
	
	if (!(this instanceof Ecamp.Web.Plugin)) {
        return new Ecamp.Web.Plugin();
    }
	
	this.config = {};
	
	this.eventPluginId = config.eventPluginId;
	this.eventPluginElm = $('#'+this.eventPluginId);
};

Ecamp.Web.Plugin.prototype = {	
};