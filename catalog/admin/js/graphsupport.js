// general graphs setting
var graphpadding = 22;
var height = 170;
var panelWidth = 230;
var barSpacing = 2;

Array.prototype.max = function() {
var max = this[0];
var len = this.length;
for (var i = 1; i < len; i++) if (this[i] > max) max = this[i];
return max;
};


