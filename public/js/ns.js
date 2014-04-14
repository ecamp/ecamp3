(function(){
    function NS(n, p){
        this.NsName = (p instanceof NS) ? p.NsName + '.' + n : n;
    }
    function CNS(s){
        var c, r = this, p = s.split('.');
        while(c = p.shift()){
            r = (r[c] = (r[c] instanceof NS) ? r[c] : new NS(c, r));
        }
        return r;
    }
    NS.prototype.cns = window.cns = function(s){ CNS.call(this, s); }
}());
