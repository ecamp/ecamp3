(function(){
    CNS('ecamp.core.util');

    function SortedDictionary(sortFn, updateByIdxFn){
        function KeyValue(key, value){
            this.Key = key;
            this.Value = value;
        }

        var _sortFn = sortFn;
        var _updateByIdxFn = updateByIdxFn;
        var _map = {};
        var _list = [];

        this.Keys = [];
        this.Values = [];


        this.Exists = function(key){
            return (key in _map);
        };

        this.Get = function(key){
            if(this.Exists(key)) {
                return _map[key].Value;
            }
            return undefined;
        };

        this.Add = function(key, value){
            if(key in _map){
                // Update:
                _map[key].Value = value;

            } else {
                // Insert:
                var keyValue = new KeyValue(key, value);
                _map[key] = keyValue;
                _list.push(keyValue);
            }
            this.Sort();
        };

        this.Remove = function(key){
            if(key in _map){
                var idx = _list.indexOf(_map[key]);

                delete _map[key];
                _list.splice(idx, 1);
                this.Keys.splice(idx, 1);
                this.Values.splice(idx, 1);

                this.UpdateByIdx();
            }
        };

        this.Count = function(conditionFn){
            if(conditionFn){
                return this.Values.filter(conditionFn).length
            }
            return _list.length;
        };

        this.Clear = function(){
            _map = {};
            _list = [];
            this.Values = [];
            this.Keys = [];
        };

        this.Sort = function(){
            if(_sortFn){
                _list.sort(function(a, b){
                    return _sortFn(a.Value, b.Value);
                });
            } else {
                _list.sort();
            }

            this.Keys.splice(0, this.Keys.length);
            this.Values.splice(0, this.Values.length);

            for(var idx = 0; idx < _list.length; idx++){
                var item = _list[idx];

                this.Keys[idx] = item.Key;
                this.Values[idx] = item.Value;
            }

            this.UpdateByIdx();
        };

        this.Filter = function(filterFn){
            for(var idx = _list.length - 1; idx >= 0; idx--){
                var item = _list[idx];

                if(! filterFn(item.Value)){
                    delete _map[item.Key];
                    _list.splice(idx, 1);
                    this.Keys.splice(idx, 1);
                    this.Values.splice(idx, 1);
                }
            }

            this.UpdateByIdx();
        };

        this.UpdateByIdx = function(){
            if(_updateByIdxFn){
                var context = {};
                for(var idx = 0; idx < _list.length; idx++){
                    var item = _list[idx];
                    _updateByIdxFn(item.Value, idx, context);
                }
            }
        };

        this.CreateAdapter = function(keyFn, insertFn, updateFn){
            var sortFn = this.Sort.bind(this);

            return function(list){
                var keys = [];
                for(var idx = 0; idx < list.length; idx++){
                    var key = keyFn(list[idx]);
                    keys.push(key);

                    if(key in _map){
                        // Update:
                        _map[key].Value = updateFn(_map[key].Value, list[idx]);
                        console.log("updated: " + key);
                    } else {
                        // Insert:
                        var keyValue = new KeyValue(key, insertFn(list[idx]));
                        _map[key] = keyValue;
                        _list.push(keyValue);
                        console.log("inserted: " + key);
                    }
                }

                for(var idx = _list.length - 1; idx >= 0; idx--){
                    var item = _list[idx];

                    if(keys.indexOf(item.Key) < 0){
                        delete _map[item.Key];
                        _list.splice(idx, 1);

                        console.log("deleted: " + item.Key);
                    }
                }

                sortFn();
            };
        }
    }

    ecamp.core.util.SortedDictionary = SortedDictionary;

}());
