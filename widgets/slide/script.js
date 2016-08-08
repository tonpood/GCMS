/*
 GbtnSlide class
 design by http://www.goragod.com (goragod wiriya)
 31-7-54
 */
var GbtnSlide = GClass.create();
GbtnSlide.prototype = {
	initialize: function (div, options) {
		this.options = {
			className: 'gbtnslide',
			buttonContainerClass: 'button_container_gbtnslide',
			buttonClass: 'button_gbtnslide',
			loadingClass: 'loading_gbtnslide',
			slideTime: 10000,
			target: '_blank',
			showNumber: true,
			loop: true,
			autoResize: false,
			zIndex: 10
		};
		for (var property in options) {
			this.options[property] = options[property];
		}
		this.container = $G(div);
		this.container.addClass(this.options.className);
		size = this.container.getDimensions();
		this.width = size.width;
		this.height = size.height;
		var p = document.createElement('p');
		this.container.appendChild(p);
		p.className = this.options.buttonContainerClass;
		p.style.zIndex = this.container.style.zIndex + this.options.zIndex;
		var button = document.createElement('p');
		p.appendChild(button);
		button.className = this.options.buttonClass;
		button.style.zIndex = this.container.style.zIndex + this.options.zIndex + 1;
		this.button = $G(button);
		var loading = document.createElement('span');
		this.container.appendChild(loading);
		loading.className = this.options.loadingClass;
		loading.style.zIndex = this.container.style.zIndex + this.options.zIndex + 2;
		loading.style.left = '0px';
		loading.style.top = '0px';
		loading.style.width = this.width + 'px';
		loading.style.height = this.height + 'px';
		loading.style.position = 'absolute';
		this.loading = $G(loading);
		var link = document.createElement('a');
		this.container.appendChild(link);
		link.target = this.options.target;
		this.link = $G(link);
		var img = $G(document.createElement('img'));
		this.link.appendChild(img);
		img.style.position = 'absolute';
		img.style.left = '-10000px';
		this.img1 = img;
		var img = $G(document.createElement('img'));
		this.link.appendChild(img);
		img.style.position = 'absolute';
		img.style.left = '-10000px';
		this.img2 = img;
		this.currImg = this.img2;
		this.datas = new Array();
		this.currentId = -1;
	},
	add: function (picture, detail, url) {
		var i = this.datas.length;
		var obj = new Object();
		obj.picture = picture;
		obj.detail = detail;
		obj.url = url;
		this.datas.push(obj);
		var a = $G(document.createElement('a'));
		this.button.appendChild(a);
		a.rel = i;
		if (this.options.showNumber) {
			a.appendChild(document.createTextNode(i + 1));
		}
		var Hinstance = this;
		a.addEvent('click', function () {
			Hinstance.fade.stop();
			window.clearTimeout(Hinstance.SlideTime);
			Hinstance._show(this.rel);
		});
		return this;
	},
	_nextslide: function () {
		var next = this.currentId + 1;
		if (next >= this.datas.length && this.options.loop) {
			next = 0;
		}
		if (next < this.datas.length && $E(this.container.id)) {
			this._show(next);
			if (this.datas.length > 1) {
				var temp = this;
				this.SlideTime = window.setTimeout(function () {
					temp._nextslide.call(temp)
				}, this.options.slideTime);
			}
		}
	},
	playSlideShow: function () {
		this._nextslide();
		return this;
	},
	_show: function (id) {
		try {
			if (this.datas[id]) {
				if (this.fade) {
					this.fade.stop();
				}
				this.currentId = id;
				this.loading.style.display = 'block';
				var temp = this;
				new preload(this.datas[id].picture, function () {
					temp.loading.style.display = 'none';
					temp.currImg = temp.currImg == temp.img2 ? temp.img1 : temp.img2;
					var old = temp.currImg == temp.img2 ? temp.img1 : temp.img2;
					temp._resizeImage(temp.currImg, id);
					temp.currImg.style.zIndex = 1;
					old.style.zIndex = 0;
					temp.fade = new GFade(temp.currImg).play({
						'onComplete': function () {
							old.setStyle('opacity', 0);
							temp.link.title = temp.datas[id].detail;
							temp.link.set('href', temp.datas[id].url.replace('&amp;', '&'));
							temp._setButton(id);
						}
					});
				});
			}
		} catch (e) {
		}
	},
	_resizeImage: function (img, id) {
		img.src = this.datas[id].picture;
		var w = img.width;
		var h = img.height;
		if (this.options.autoResize) {
			if (w >= h) {
				if (w > this.width) {
					var nw = this.width;
					var nh = (this.width * h) / w;
				} else if (h > this.height) {
					var nh = this.height;
					var nw = (this.height * w) / h;
				} else {
					var nh = h;
					var nw = w;
				}
			} else {
				if (h > this.height) {
					var nh = this.height;
					var nw = (this.height * w) / h;
				} else if (w > this.width) {
					var nw = this.width;
					var nh = (this.width * h) / w;
				} else {
					var nh = h;
					var nw = w;
				}
			}
			img.style.width = nw + 'px';
			img.style.height = nh + 'px';
		} else {
			nw = w;
			nh = h;
		}
		img.style.top = ((this.height - nh) / 2) + 'px';
		img.style.left = ((this.width - nw) / 2) + 'px';
	},
	_setButton: function (id) {
		forEach(this.button.getElementsByTagName('a'), function () {
			this.className = this.rel == id ? 'current' : '';
		});
	}
};