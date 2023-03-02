/**
 * Copyright Â© 2016 Codazon. All rights reserved.
 * See COPYING.txt for license details.
 */
define(['jquery', 'domReady'], function($, domReady) {
	$.widget('codazon.megamenu', {
		options: {
			type: 'horizontal',
			fixedLeftParent: '.cdz-fix-left',
			dropdownEffect: 'normal',
			rtlClass: 'rtl-layout',
			verClass: 'cdz-vertical-menu',
			horClass: 'cdz-horizontal-menu',
			parent: '.parent',
			subMenu: '.groupmenu-drop, .cat-tree .groupmenu-nondrop',
			triggerClass: 'dropdown-toggle',
			stopClickOnPC: true,
			delay: 100,
			contPadding: 0,
			responsive: {
				768: 'mobile',
			}
		},
		_create: function() {
			var self = this,
				$body = $('body'),
				conf = this.options;
			this.options.trigger = '.' + conf.triggerClass;
			var $menu = self.element;
			if ($menu.hasClass(conf.horClass)) {
				if ($body.hasClass(conf.rtlClass)) {
					self._alignMenuRight(conf);
				} else {
					self._alignMenuLeft(conf);
				}
				self._assignControls()._listen();
			} else {
				self._alignMenuTop();	
			}
			self._currentMode = self._getMode();
			self._rebuildHtmlStructure();
			self._setupMenu();
			self._responsive();
			if (conf.type != 1) {
				self._dropdownEffect();
			}
			var preloadImagesandTitles = this.preloadImagesandTitles;
			domReady(function () {
                preloadImagesandTitles($menu);
            });
		},
		_assignControls: function () {
            this.controls = {
                toggleBtn: $('[data-action="toggle-nav"]'),
                swipeArea: $('.nav-sections'),
				imageLinks: $('[data-cdz-image]')
            };

            return this;
        },
		_listen: function () {
            var controls = this.controls;
            var toggle = this.toggle;
            var showImage = this.showImage;
            var preloadImages = this.preloadImages;
			controls.toggleBtn.unbind();
			controls.swipeArea.unbind();
            this._on(controls.toggleBtn, {'click': toggle});
            this._on(controls.swipeArea, {'swipeleft': toggle});
            this._on(controls.imageLinks, {'hover': showImage});
        },
		toggle: function () {
            if ($('html').hasClass('nav-open')) {           
                setTimeout(function () {
                   $('html').removeClass('nav-open');
                    $('html').removeClass('nav-before-open');
                }, 500);
            } else {
                $('html').addClass('nav-before-open');
                setTimeout(function () {
                    $('html').addClass('nav-open');
                }, 42);
            }
        },
		showImage: function(event) {
			$a = $(event.currentTarget);
			$li = $a.parent();
			$ul = $li.parents('ul').first();
            $ul.find('.imageContainer img').attr('src', $a.data('cdz-image'));
		},
        preloadImagesandTitles: function($menu) {
			$menu.each(function() {
				var $that = $(this),
                    $depth0 = $that.find('ul > li > ul'),
					$depth1 = $that.find('ul > li > ul > li > ul');
				//Depth 0
				$depth0.each(function() {
                    parentTitle = $(this).siblings('a').text();
					$(this).prepend('<h2>' + parentTitle + '</h2>');
				});
				//Depth 1
                $depth1.each(function() {
					var $ul = $(this),
						$firstImageli = $ul.find('[data-cdz-image]').first(),
						parentTitle = $ul.siblings('a').text();
					//Handle Titles
					if ($ul.find('> h2').length == 0)
                    	$ul.prepend('<h2>' + parentTitle + '</h2>');
                    //Handle Images
                    $imageContainer = $('<div class="imageContainer" />')
                        .css({
                            'position'	: 'absolute',
                            'width'		: 400,
                            'height'	: '100%',
                            'top'		: 0,
                            'right'		: '-400px',
                            'padding'	: '20px',
                            'background': 'white'
                        })
                        .append(
                            '<img width="100%" ' + ($firstImageli.data('cdz-image') ? 'src="' + $firstImageli.data('cdz-image') + '"' : '') + '/>'
                        );
                    $imageContainer.appendTo($ul);
				});
			});
		},
		_currentMode: false,
		_dropdownEffect: function() {
			var self = this;
			var conf = this.options,
				effect = conf.dropdownEffect;
			switch (effect) {
				case 'translate':
				case 'slide':
				case 'fade':
				default:
					self._attachEffect(effect);
					break;
			}
		},
		_attachEffect: function(type) {
			var self = this,
				conf = this.options;
			var timeout = false;
			$('.level-top', self.element).each(function() {
				var $leveltop = $(this);
				var $drop = $leveltop.children('.groupmenu-drop');
				if (type != 'translate') {
					$drop.hide();
				}
				$drop.addClass('slidedown');
				$leveltop.on('mouseover', function() {
					if (timeout) clearTimeout(timeout);
					timeout = setTimeout(function() {
						if (self._currentMode == 'desktop') {
							if (type == 'slide') {
								$drop.stop().slideDown(400, function() {
									$leveltop.addClass('open');
								});
							} else if (type == 'fade') {
								$drop.stop().fadeIn(600, function() {
									$leveltop.addClass('open');
								});
							} else if (type == 'normal') {
								$drop.show();
								$leveltop.addClass('open');
							}
							$leveltop.trigger('animated_in');
						}
					}, conf.delay);
				});
				$leveltop.on('mouseleave', function() {
					if (timeout) clearTimeout(timeout);
					if (self._currentMode == 'desktop') {
						if (type == 'slide') {
							$drop.stop().slideUp(200, function() {
								$leveltop.removeClass('open');
								$leveltop.trigger('animated_out');
							});
						} else if (type == 'fade') {
							$drop.stop().fadeOut(200, function() {
								$leveltop.removeClass('open');
								$leveltop.trigger('animated_out');
							});
						} else if (type == 'normal') {
							$drop.hide();
							$leveltop.removeClass('open');
							$leveltop.trigger('animated_out');
						}
					}
				});
			});
		},
		_desktopMenu: function(conf) {
			var $menu = this.element;
			var $subMenu = $(conf.subMenu, $menu);
			$subMenu.css('display', '');
			$subMenu.removeClass('open');
			$(conf.parent, $menu).removeClass('open');
			$(conf.trigger, $menu).remove();
		},
		_mobileMenu: function(conf) {
			var $menu = this.element;
			$(conf.subMenu, $menu).hide();
			$(conf.parent, $menu).each(function() {
				var $li = $(this);
				$li.children(conf.subMenu).each(function() {
					var $subMenu = $(this);
					var $toggle = $('<span class="' + conf.triggerClass + '" />');
					$toggle.insertBefore($subMenu);
					$toggle.on('click.showsubmenu', function() {
						$li.toggleClass('open');
						$subMenu.toggleClass('open');
						$subMenu.slideToggle(300);
					});
				});
			});
		},
		_getAdapt: function() {
			var responsive = this.options.responsive,
				$win = $(window),
				winWidth = $win.width(),
				minWidth = 0;
			for (adapt in responsive) {
				if ((minWidth <= winWidth) && (winWidth < adapt)) {
					return adapt;
				}
				minWidth = adapt;
			}
			return false;
		},
		_getMode: function() {
			responsive = this.options.responsive;
			$win = $(window);
			var winWidth = $win.width();
			var minWidth = 0;
			var adapt = this._getAdapt();
			if (adapt !== false) {
				return responsive[adapt];
			} else {
				return 'desktop';
			}
		},
		_setupMenu: function() {
			var mode = this._getMode();
			if (mode == 'mobile') {
				this._mobileMenu(this.options);
			} else {
				this._desktopMenu(this.options);
			}
		},
		_responsive: function() {
			var self = this;
			$(window).on('resize', function() {
				var mode = self._getMode();
				if (self._currentMode != mode) {
					self._currentMode = mode;
					self._setupMenu();
				}
			});
		},
		_rebuildHtmlStructure: function() {
			var self = this;
			$('.need-unwrap', self.element).each(function() {
				var $this = $(this);
				$this.children('.groupmenu-drop').removeClass('groupmenu-drop').addClass('groupmenu-nondrop');
				var $parent = $(this).parent();
				var $newDiv = $('<div />');
				$newDiv.appendTo($parent);
				$newDiv.attr('class', $this.attr('class'));
				$newDiv.attr('style', $this.attr('style'));
				$this.children().appendTo($newDiv);
				$this.remove();
			});
			$('.no-dropdown', self.element).each(function() {
				var $noDropdown = $(this);
				$('.need-unwrap', $noDropdown).first().unwrap();
				$('.need-unwrap', $noDropdown).removeClass('need-unwrap');
				$noDropdown.children('.groupmenu-drop').removeClass('groupmenu-drop').addClass('groupmenu-nondrop');
			});
		},
		_getEventIn: function(conf) {
			if (this.options.type == 'translate') {
				return 'mouseover mouseenter';
			} else {
				return 'animated_in';
			}
		},
		_getEventOut: function(conf) {
			if (this.options.type == 'translate') {
				return 'mouseleave';
			} else {
				return 'animated_out';
			}
		},
		_alignMenuLeft: function(conf) {
			var self = this;
			function handlerIn($li){
				var $ddMenu = $li.children('.groupmenu-drop');
				if ($li.hasClass('parent')) $ddMenu.css('left', 0);
				var $menuCont = $li.parents(conf.fixedLeftParent).first();
				$menuCont = $menuCont.length ? $menuCont : self.element.parent();
				if ($menuCont.length) {
					var menuHeight = $(".groupmenu").height();
					var preHeaderHeight = $(".pre-header-wrapper").height();
					var menuTop = $(".groupmenu").offset().top;
					var finalTop = menuHeight + menuTop - preHeaderHeight;
					$ddMenu.css('top', finalTop);
					// var left = $menuCont.offset().left + $menuCont.outerWidth() - ($ddMenu.offset().left + $ddMenu.outerWidth());
					// var leftPos = $ddMenu.offset().left + left - $menuCont.offset().left;
					// if (leftPos < 0) left = left - leftPos;
					// if (left < 0) {
					// 	$ddMenu.css('left', left - conf.contPadding + 'px');
					// }
				}
			}
			function handlerOut($li){
				$li.children('.groupmenu-drop').css('left', '');
			}
			var $li = $(' > .groupmenu > .level-top > .groupmenu-drop', self.element).parent();
			$li.each(function(){
				handlerIn($(this));
			});
			if (this.options.type == 'translate') {				
				$li.hover(function() {
					handlerIn($(this));
				}, function() {
					handlerIn($(this));
				});
			} else {
				var eventIn = this._getEventIn(),
				eventOut = this._getEventOut();
				$li.on(eventIn,function(){
					handlerIn($(this));
				}).on(eventOut, function() {
					handlerIn($(this));
				});
			}
		},
		_alignMenuRight: function(conf) {
			var self = this;
			var eventIn = this._getEventIn(),
				eventOut = this._getEventOut();
			function handlerIn($li){
				var $ddMenu = $li.children('.groupmenu-drop');
				if ($li.hasClass('parent')) $ddMenu.css('right', 0);
				var $menuCont = $li.parents(conf.fixedLeftParent).first();
				$menuCont = $menuCont.length ? $menuCont : self.element.parent();
				if ($menuCont.length) { 
					var winWidth = $(window).width();
					var contOffsetRight = winWidth - ($menuCont.offset().left + $menuCont.outerWidth(true));
					var ddOffsetRight = winWidth - ($ddMenu.offset().left + $ddMenu.outerWidth(true));
					var right = contOffsetRight + $menuCont.outerWidth() - (ddOffsetRight + $ddMenu.outerWidth());
					var rightPos = ddOffsetRight + right - contOffsetRight;
					if (rightPos < 0) right = right - rightPos;
					if (right < 0) {
						$ddMenu.css('right', right - conf.contPadding + 'px');
					}
				}
			}
			function handlerOut($li){
				$li.children('.groupmenu-drop').css('right', '');
			}
			var $li = $(' > .groupmenu > .level-top > .groupmenu-drop', self.element).parent();
			$li.each(function(){
				handlerIn($(this));
			});
			if (this.options.type == 'translate') {
				$li.hover(function() {
					handlerIn($(this));
				}, function() {
					handlerIn($(this));
				});
			} else {
				var eventIn = this._getEventIn(),
				eventOut = this._getEventOut();
				$li.on(eventIn,function(){
					handlerIn($(this));
				}).on(eventOut, function() {
					handlerIn($(this));
				});
			}
		},
		_alignMenuTop: function(conf) {
			var self = this;
			var $win = $(window);
			$(' > .groupmenu > .level-top > .groupmenu-drop', self.element).parent().hover(
			function(){
				var $li = $(this);
				var $ddMenu = $(this).children('.groupmenu-drop');
				$ddMenu.css({top:''});
				var winHeight = $win.height(),
				winTop = $(window).scrollTop();
				ddTop = $ddMenu.offset().top, ddHeight = $ddMenu.outerHeight(),
				overflow = (ddTop - winTop + ddHeight) > winHeight;
				if( overflow || $li.hasClass('fixtop')){
					//var newTop = parseInt($ddMenu.css('top')) - (ddTop - winTop + ddHeight - winHeight);
					var newTop = parseInt($ddMenu.css('top')) - (ddTop - self.element.children('.groupmenu').offset().top);
					$ddMenu.css({top: newTop});
				}
			},
			function(){});
		}
	});
	return $.codazon.megamenu;
});