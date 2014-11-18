/**
 * Created by pirmin on 30.10.14.
 */

(function(ngApp) {
    ngApp.factory('PicassoTimeline', [function(){

        function PicassoTimeline(picassoElement, cfg){
            var _picassoElement = picassoElement;
            var _height = picassoElement.eventInstanceHeight;
            var _startMin;
            var _endMin;
            var _slots = null;
            var _oldConfig = null;

            if(cfg){
                _height = cfg.height || _height;
                _startMin = cfg.startMin || _startMin;
                _endMin = cfg.endMin || _endMin;
            }


            Object.defineProperty(this, 'height', {
                get: function(){ return _height; },
                set: function(value){
                    if(_height != value){ _height = value; UpdateTimeline(); }
                }
            });

            Object.defineProperty(this, 'startMin', {
                get: function(){ return _startMin; },
                set: function(value){
                    if(_startMin != value) { _startMin = value; UpdateTimeline(); }
                }
            });
            Object.defineProperty(this, 'endMin', {
                get: function(){ return _endMin; },
                set: function(value) {
                    if (_endMin != value) { _endMin = value; UpdateTimeline(); }
                }
            });
            Object.defineProperty(this, 'durationMin', {
                get: function(){ return _endMin - _startMin; }
            });

            Object.defineProperty(this, 'start', {
                get: function(){ return new Date(0, 0, 1, 0, _startMin, 0); }
            });
            Object.defineProperty(this, 'end', {
                get: function(){ return new Date(0, 0, 1, 0, _endMin, 0); }
            });

            Object.defineProperty(this, 'slots', {
                get: function(){
                    if(_slots == null){
                        UpdateTimeline();
                    }
                    return _slots;
                }
            });

            Object.defineProperty(this, 'UpdateTimeline', { value: UpdateTimeline });
            Object.defineProperty(this, 'BeginUpdate', { value: BeginUpdate });
            Object.defineProperty(this, 'EndUpdate', { value: EndUpdate });

            function BeginUpdate(){
                _oldConfig = {
                    height: _height,
                    startMin: _startMin,
                    endMin: _endMin
                };
            }

            function EndUpdate(){
                if(_oldConfig != null) {
                    if (_oldConfig.height != _height
                        || _oldConfig.startMin != _startMin
                        || _oldConfig.endMin != _endMin
                    ) {
                        _oldConfig = null;
                        UpdateTimeline();
                    }
                }
                _oldConfig = null;
            }


            function UpdateTimeline(){
                if(_oldConfig == null) {
                    //_height = _picassoElement.eventInstanceHeight;
                    _slots = [];

                    var start = 60 * Math.floor(_startMin / 60);
                    var end = 60 * Math.ceil(_endMin / 60);

                    for (var min = start; min < end; min += 60) {
                        var slotStart = Math.max(_startMin, min);
                        var slotEnd = Math.min(_endMin, min + 60);
                        var slotDuration = slotEnd - slotStart;
                        var oddEvenClass = (Math.floor(min / 60) % 2 == 1) ? 'odd' : 'even';
                        var fontSize = GetFontSize(slotDuration);

                        _slots.push({
                            style: {
                                top: (100 * GetOffset(slotStart)) + '%',
                                height: (100 * GetLength(slotDuration)) + '%',
                                fontSize: fontSize
                            },
                            class: 'picasso-timeline-slot-' + oddEvenClass,
                            start: new Date(0, 0, 1, 0, slotStart, 0),
                            end: new Date(0, 0, 1, 0, slotEnd, 0),
                            showTime: !!(fontSize)
                        });
                    }
                }
            }

            function GetFontSize(min){
                var height = GetLength(min) * _height;

                var fontSize = height - 6;
                fontSize = (fontSize <= 14) ? fontSize : 14;
                fontSize = (fontSize >=  8) ? fontSize : 0;

                return fontSize > 0 ? (fontSize + 'px') : null;
            }

            /** @returns {number} */
            function GetOffset(min){
                return GetLength(min - _startMin);
            }

            /** @returns {number} */
            function GetLength(min){
                return min / (_endMin - _startMin);
            }

        }

        return PicassoTimeline;

    }]);

})(window.ecamp.ngApp);