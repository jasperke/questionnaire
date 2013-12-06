if (!this.CyberChart) {
	this.CyberChart = {};
}

CyberChart = function (el, data, options) {
	//this._element = el;
	this._element = (typeof(el) == 'string') ? $('#' + el) : $(el);
	this._data = data;
	this.options = {
		width: 1000, // UI列印時用250x210, UI螢幕顯示用 *1.4, 此處繪製時用 *4 (畫高解析,印時才不會鋸齒)
		height: 840,
		xTitle: '',
		yTitle: '',
		xLength: 760, // X軸長
		yLength: 500, // Y軸長
		yScale: [0, 5, 10, 15, 20, 25, 30] // 未特別指定者,Y軸於此刻度處畫橫線及label
	};
	$.extend(this.options, options);
	//setTimeout($.proxy(this._prepare,this),0); /* IE使用explorercanvas, 以動態產生canvas, 同thread立即執行getContext()會有錯 */
	this._prepare();
};
CyberChart.prefixZero = function (n) {
	return n >= 10 ? n : '0' + n;
};
CyberChart.prototype = {
	_prepare: function () {
		this.set({xLength: this.get('width') - 190 - 50, yLength: this.get('height') - 100 - 240});

		var jCanvas = $("<canvas/>").attr({width: this.get('width'), height: this.get('height')}).appendTo(this._element);
		// 預設以width*height顯示, 若需縮小顯示, 可自行於html中css, 設定canvas的寬高

		//	if(!jCanvas[0].getContext){ // 不考慮IE舊版
		//		G_vmlCanvasManager.initElement(jCanvas[0]);
		//	}
		this._context2d = jCanvas.get(0).getContext('2d');

		if (!this._context2d.setLineDash) {
			this._context2d.setLineDash = $.proxy(function (ary) {
				this._context2d.mozDash = ary;
				this._context2d.webkitLineDash = ary; // safari無效??
			}, this);
		}

		this._drawBase();
		this._drawXTitle();
		this._drawYTitle();
		this._drawData();
	},
	_setLineDashOffset: function (n) {
		this._context2d.lineDashOffset = n;
		this._context2d.mozDashOffset = n;
		this._context2d.webkitLineDashOffset = n;
	},
	set: function (obj) {
		for (var p in obj) {
			if (obj.hasOwnProperty(p))
				this.options[p] = obj[p];
		}
	},
	get: function (p) {
		return this.options[p];
	},
	_drawBase: function () {
		this._context2d.save();

		// 白色背景
		this._context2d.fillStyle = '#ffffff';
		this._context2d.fillRect(0, 0, this.get('width'), this.get('height'));

		// 文字樣式
		this._context2d.fillStyle = '#000000';
		this._context2d.font = '34px sans-serif';

		this._context2d.translate(190.5, 100.5); // 原點移至Y軸最高點處

		this._context2d.strokeStyle = '#000000';
		this._context2d.lineWidth = 2;

		// X軸 (下面畫Y軸刻度線時會一併畫出, 所以不必畫了)
		// this._context2d.moveTo(-20, 500);
		// this._context2d.lineTo(760, 500);

		var xLength = this.get('xLength'),
			yLength = this.get('yLength'),
			yScale = this.get('yScale'),
			factor = yLength / (yScale[yScale.length - 1] - yScale[0]),
			y,
			i,
			xDistance = Math.floor(xLength / 5); // X軸刻度間距

		// Y軸
		this._context2d.beginPath();
		this._context2d.moveTo(0, 0);
		this._context2d.lineTo(0, yLength + 20);
		this._context2d.stroke();

		// Y軸上刻度線
		for (i = 0; i < yScale.length; i++) { // i=0即X軸
			y = Math.round(yLength - (yScale[i] - yScale[0]) * factor) + 0.5;
			this._context2d.beginPath();

			this._context2d.save(); // Y軸用到虛線, 之後須恢復直線

			this._context2d.moveTo(-20, y); // 往左凸出20為Y軸刻度標線
			this._context2d.lineTo(xLength, y);
			this._context2d.fillText(yScale[i], -70, y + 10);

			if (i > 0) {
				this._context2d.setLineDash([5, 5]);
			}
			this._context2d.stroke();

			this._context2d.restore();
		}

		// X軸刻度間距 750/5=150

		this._context2d.beginPath();
		i = xDistance;
		while (i <= xLength) {
			this._context2d.moveTo(i, yLength);
			this._context2d.lineTo(i, yLength + 20); // 往下凸出20為X軸刻度標線
			i += xDistance;
		}
		this._context2d.stroke();

		this._context2d.restore();
	},
	_drawData: function () {

		this._context2d.save();

		this._context2d.translate(190.5, 100.5); // 原點移至Y軸最高點處

		var xLength = this.get('xLength'),
			yLength = this.get('yLength'),
			yScale = this.get('yScale'),
			data = this._data, // [{label:'2013-12-12',value:20},{},...]
			factor = yLength / (yScale[yScale.length - 1] - yScale[0]),
			i = 0,
			xDistance = Math.floor(xLength / 5), // X軸刻度間距
			barWidth = Math.round(xDistance / 4 * 2),
			barSpace = Math.round(xDistance / 4 * 1),
			x, y,
			datePart,
			fontDim;

		for (i = 0; i < data.length; i++) {
			if (data[i].value === '')
				continue;

			x = barSpace + i * xDistance + 0.5;
			y = Math.round(yLength - (data[i].value - yScale[0]) * factor) - 0.5;

			// bar顏色
			this._context2d.fillStyle = "rgba(180,180,180,0.7)";
			this._context2d.lineWidth = 5;
			this._context2d.fillRect(x, y, barWidth, data[i].value * factor);
			this._context2d.strokeRect(x, y, barWidth, data[i].value * factor);

			// time label
			this._context2d.fillStyle = '#000000';
			this._context2d.font = '34px sans-serif';
			datePart = data[i].label.split('-');
			fontDim = this._context2d.measureText(datePart[1] + '/' + datePart[2]);

			// this._context2d.fillText(datePart[1] + '/' + datePart[2], x - 6, yLength + 60);
			// this._context2d.fillText(datePart[0], x - 2, yLength + 100);

			this._context2d.fillText(datePart[1] + '/' + datePart[2], Math.round(i * xDistance + (xDistance - fontDim.width) / 2), yLength + 60);
			fontDim = this._context2d.measureText(datePart[0]);
			this._context2d.fillText(datePart[0], Math.round(i * xDistance + (xDistance - fontDim.width) / 2), yLength + 100);
		}

		this._context2d.restore();
	},
/*	_drawBrokenLine: function () {
		var now = (new Date()).getTime() / 1000,
			i, x;
		while ((now - this.startSecond) > this.clientDiffSecond) { // 避免電腦忙碌, 未能每秒執行1次, 導致startSecond未反應真正時間
			this.startSecond++;
		}
		for (i = 0; i < this._data.length; i++) {
			if (this._data[i][0] < this.startSecond) {
				this._data.shift();
				i--;
			} else {
				break;
			}
		}
		this.startSecond++;

		this._context2d.save();
		this._context2d.setTransform(1, 0, 0, -1, 0, this.get('height') - 20); // 座標轉換成以左下角為原點,x軸正向往右,y軸正向往上

		this._context2d.strokeStyle = this.get('color');
		this._context2d.lineWidth = 1;
		this._context2d.beginPath();

		x = 30;
		for (i = 0; i < this._data.length; i++) {
			if (this._data[i][1] === null) {
				x -= this.get('offset');
				continue;
			} else {
				this._context2d.moveTo(x, this._data[i][1]);
				i++;
				break;
			}
		}
		this._xScale = [];

		for (; i < this._data.length; i++) {
			this._context2d.lineTo(x -= this.get('offset'), this._data[i][1]);
			if (this._data[i][0] % 60 === 0)
				this._xScale.push([x, this._data[i][0]]);
		}
		this._context2d.stroke();
		this._context2d.restore();
	},*/
	_drawYTitle: function () {
		this._context2d.save();
		this._context2d.fillStyle = '#000000';
		this._context2d.translate(70, Math.round(this.get('yLength') / 5 * 4));
		this._context2d.rotate(-90 * (Math.PI / 180));
		this._context2d.font = '44px sans-serif';
		this._context2d.fillText(this.get('yTitle'), 0, 0);
		this._context2d.restore();
	},
	_drawXTitle: function () {
		this._context2d.save();
		this._context2d.fillStyle = '#000000';
		this._context2d.font = '44px sans-serif';
		this._context2d.fillText(this.get('xTitle'), Math.round(this.get('width') / 7 * 3), this.get('height') - 60);
		this._context2d.restore();
	}
};