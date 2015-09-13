(function($){
    function NS(n, p){
        this.NsName = (p instanceof NS) ? p.NsName + '.' + n : n;

        var jq = $(this);
        this.on =       $.proxy(jq.on, jq);
        this.one =      $.proxy(jq.one, jq);
        this.off =      $.proxy(jq.off, jq);
        this.trigger =  $.proxy(jq.trigger, jq);
    }
    function CreateNamespace(s){
        var c, r = this, p = s.split('.');
        while(c = p.shift()){
            r = (r[c] = (r[c] instanceof NS) ? r[c] : new NS(c, r));
        }
        return r;
    }
    NS.prototype.CNS = CreateNamespace;
    window.CNS = CreateNamespace;
}(jQuery));
