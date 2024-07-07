/**
 * JavaScript Client Detection
 * (C) viazenetti GmbH (Christian Ludwig)
 */
(function (window) {
	{
		var unknown = '-';

		// screen
		var screenSize = '';
		if (screen.width) {
			width = (screen.width) ? screen.width : '';
			height = (screen.height) ? screen.height : '';
			screenSize += '' + width + " x " + height;
		}

		// browser
		var nVer = navigator.appVersion;
		var nAgt = navigator.userAgent;
		var browser = navigator.appName;
		var version = '' + parseFloat(navigator.appVersion);
		var majorVersion = parseInt(navigator.appVersion, 10);
		var nameOffset, verOffset, ix;

		// Opera
		if ((verOffset = nAgt.indexOf('Opera')) !== -1) {
			browser = 'Opera';
			version = nAgt.substring(verOffset + 6);
			if ((verOffset = nAgt.indexOf('Version')) !== -1) {
				version = nAgt.substring(verOffset + 8);
			}
		}
		// Opera Next
		if ((verOffset = nAgt.indexOf('OPR')) !== -1) {
			browser = 'Opera';
			version = nAgt.substring(verOffset + 4);
		}
		// USbrowser
		else if ((verOffset = nAgt.indexOf('UCBrowser')) !== -1) {
			browser = 'UCBrowser';
			version = nAgt.substring(verOffset + 6);
			if ((verOffset = nAgt.indexOf('Version')) !== -1) {
				version = nAgt.substring(verOffset + 8);
			}
		}
		// MSIE
		else if ((verOffset = nAgt.indexOf('MSIE')) !== -1) {
			browser = 'Microsoft Internet Explorer';
			version = nAgt.substring(verOffset + 5);
		}
		// MSE
		else if ((verOffset = nAgt.indexOf('Edge')) !== -1) {
			browser = 'Edge';
			version = nAgt.substring(verOffset + 7);
		}
		// Chrome
		else if ((verOffset = nAgt.indexOf('Chrome')) !== -1) {
			browser = 'Chrome';
			version = nAgt.substring(verOffset + 7);
		}
		// Safari
		else if ((verOffset = nAgt.indexOf('Safari')) !== -1) {
			browser = 'Safari';
			version = nAgt.substring(verOffset + 7);
			if ((verOffset = nAgt.indexOf('Version')) !== -1) {
				version = nAgt.substring(verOffset + 8);
			}
		}
		// Firefox
		else if ((verOffset = nAgt.indexOf('Firefox')) !== -1) {
			browser = 'Firefox';
			version = nAgt.substring(verOffset + 8);
		}
		// MSIE 11+
		else if (nAgt.indexOf('Trident/') !== -1) {
			browser = 'Microsoft Internet Explorer';
			version = nAgt.substring(nAgt.indexOf('rv:') + 3);
		}
		// Other browsers
		else if ((nameOffset = nAgt.lastIndexOf(' ') + 1) < (verOffset = nAgt.lastIndexOf('/'))) {
			browser = nAgt.substring(nameOffset, verOffset);
			version = nAgt.substring(verOffset + 1);
			if (browser.toLowerCase() === browser.toUpperCase()) {
				browser = navigator.appName;
			}
		}
		// trim the version string
		if ((ix = version.indexOf(';')) !== -1) version = version.substring(0, ix);
		if ((ix = version.indexOf(' ')) !== -1) version = version.substring(0, ix);
		if ((ix = version.indexOf(')')) !== -1) version = version.substring(0, ix);

		majorVersion = parseInt('' + version, 10);
		if (isNaN(majorVersion)) {
			version = '' + parseFloat(navigator.appVersion);
			majorVersion = parseInt(navigator.appVersion, 10);
		}

		// mobile version
		var mobile = /Mobile|mini|Fennec|Android|iP(ad|od|hone)/.test(nVer);

		// cookie
		var cookieEnabled = !!(navigator.cookieEnabled);

		if (typeof navigator.cookieEnabled === 'undefined' && !cookieEnabled) {
			document.cookie = 'testcookie';
			cookieEnabled = (document.cookie.indexOf('testcookie') !== -1);
		}

		// system
		var os = unknown;
		var clientStrings = [
			{s: 'Windows 10', r: /(Windows 10.0|Windows NT 10.0)/},
			{s: 'Windows 8.1', r: /(Windows 8.1|Windows NT 6.3)/},
			{s: 'Windows 8', r: /(Windows 8|Windows NT 6.2)/},
			{s: 'Windows 7', r: /(Windows 7|Windows NT 6.1)/},
			{s: 'Windows Vista', r: /Windows NT 6.0/},
			{s: 'Windows Server 2003', r: /Windows NT 5.2/},
			{s: 'Windows XP', r: /(Windows NT 5.1|Windows XP)/},
			{s: 'Windows 2000', r: /(Windows NT 5.0|Windows 2000)/},
			{s: 'Windows ME', r: /(Win 9x 4.90|Windows ME)/},
			{s: 'Windows 98', r: /(Windows 98|Win98)/},
			{s: 'Windows 95', r: /(Windows 95|Win95|Windows_95)/},
			{s: 'Windows NT 4.0', r: /(Windows NT 4.0|WinNT4.0|WinNT|Windows NT)/},
			{s: 'Windows CE', r: /Windows CE/},
			{s: 'Windows 3.11', r: /Win16/},
			{s: 'Android', r: /Android/},
			{s: 'Open BSD', r: /OpenBSD/},
			{s: 'Sun OS', r: /SunOS/},
			{s: 'Linux', r: /(Linux|X11)/},
			{s: 'iOS', r: /(iPhone|iPad|iPod)/},
			{s: 'Mac OS X', r: /Mac OS X/},
			{s: 'Mac OS', r: /(MacPPC|MacIntel|Mac_PowerPC|Macintosh)/},
			{s: 'QNX', r: /QNX/},
			{s: 'UNIX', r: /UNIX/},
			{s: 'BeOS', r: /BeOS/},
			{s: 'OS/2', r: /OS\/2/},
			{s: 'Search Bot', r: /(nuhk|Googlebot|Yammybot|Openbot|Slurp|MSNBot|Ask Jeeves\/Teoma|ia_archiver)/}
		];
		for (var id in clientStrings) {
			var cs = clientStrings[id];
			if (cs.r.test(nAgt)) {
				os = cs.s;
				break;
			}
		}



		if (/Windows/.test(os)) {
			os = 'Windows';
		}

		var flashVersion = 'no check';
		if (typeof swfobject !== 'undefined') {
			var fv = swfobject.getFlashPlayerVersion();
			if (fv.major > 0) {
				flashVersion = fv.major + '.' + fv.minor + ' r' + fv.release;
			} else {
				flashVersion = unknown;
			}
		}
	}

	window.jscd = {
		screen             : screenSize,
		browser            : browser,
		browserVersion     : version,
		browserMajorVersion: majorVersion,
		mobile             : mobile,
		os                 : os,
		cookies            : cookieEnabled,
		flashVersion       : flashVersion
	};
}(window));

/**
 * dialogFx.js v1.0.0
 * http://www.codrops.com
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright 2014, Codrops
 * http://www.codrops.com
 */

(function (window) {

	'use strict';

	var support = {animations: Modernizr.cssanimations},
		animEndEventNames = {
			'WebkitAnimation': 'webkitAnimationEnd',
			'OAnimation'     : 'oAnimationEnd',
			'msAnimation'    : 'MSAnimationEnd',
			'animation'      : 'animationend'
		},
		animEndEventName = animEndEventNames[Modernizr.prefixed('animation')],
		onEndAnimation = function (el, callback) {
			var onEndCallbackFn = function (ev) {
				if (support.animations) {
					if (ev.target !== this) return;
					this.removeEventListener(animEndEventName, onEndCallbackFn);
				}
				if (callback && typeof callback === 'function') {
					callback.call();
				}
			};
			if (support.animations) {
				el.addEventListener(animEndEventName, onEndCallbackFn);
			} else {
				onEndCallbackFn();
			}
		};

	function extend(a, b) {
		for (var key in b) {
			if (b.hasOwnProperty(key)) {
				a[key] = b[key];
			}
		}
		return a;
	}

	function DialogFx(el, options) {
		this.el = el;
		this.options = extend({}, this.options);
		extend(this.options, options);
		this.isOpen = false;
		this._initEvents();
	}

	DialogFx.prototype.options = {
		// callbacks
		onOpenDialog       : function () {
			return false;
		},
		onCloseDialog      : function () {
			return false;
		},
		onOpenAnimationEnd : function () {
			return false;
		},
		onCloseAnimationEnd: function () {
			return false;
		}
	};

	DialogFx.prototype._initEvents = function () {
		var self = this;

		// esc key closes dialog
		document.addEventListener('keydown', function (ev) {
			var keyCode = ev.keyCode || ev.which;
			if (keyCode === 27 && self.isOpen) {
				self.toggle();
			}
		});

		this.el.querySelector('.dialog__overlay').addEventListener('click', this.toggle.bind(this));
	};

	DialogFx.prototype.toggle = function () {
		var self = this;
		if (this.isOpen) {
			jQuery(this.el).removeClass('dialog--open');
			jQuery(self.el).addClass('dialog--close');

			onEndAnimation(this.el.querySelector('.dialog__content'), function () {
				jQuery(self.el).removeClass('dialog--close');
				self.options.onCloseAnimationEnd(self);
			});

			// callback on close
			this.options.onCloseDialog(this);
		} else {
			jQuery(this.el).addClass('dialog--open');

			// callback on open
			this.options.onOpenDialog(this);

			onEndAnimation(this.el.querySelector('.dialog__content'), function () {
				jQuery(self.el).removeClass('dialog--close');
				self.options.onOpenAnimationEnd(self);
			});
		}
		this.isOpen = !this.isOpen;
	};

	// add to global namespace
	window.DialogFx = DialogFx;

})(window);


/**
 * Mistape
 */
(function ($) {

	// return if no args passed from backend
	if (!window.decoMistape) {
		return;
	}

	var getPointerEvent = function (event) {
		return event.originalEvent.targetTouches ? event.originalEvent.targetTouches[0] : event;
	};

	var $touchArea = $('body'),
		touchStarted = false, // detect if a touch event is sarted
		touchPopupShow = false, // detect if a touch event is sarted
		currX = 0,
		currY = 0,
		cachedX = 0,
		cachedY = 0;
	var timeoutTouch = '';

	$touchArea.on('touchstart', function (e) {
		var pointer = getPointerEvent(e);
		cachedX = currX = pointer.pageX;
		cachedY = currY = pointer.pageY;

		if (touchPopupShow === true) {
			touchPopupShow = false;
			var report = decoMistape.getSelectionData();
			if (report) {
				decoMistape.showDialog(report);
			}
		}

		// }, 1000);
	});
	$touchArea.on('touchend touchcancel', function (e) {
		var pointer = getPointerEvent(e);
		touchPopupShow = true;
		currX = pointer?.pageX;
		currY = pointer?.pageY;
	});

	$touchArea.on('touchmove', function (e) {
		// e.preventDefault();
		var pointer = getPointerEvent(e);
		currX = pointer?.pageX;
		currY = pointer?.pageY;
		if (touchStarted) {
			// here you are swiping
			// alert('Swiping');
		}

	});



	window.decoMistape = $.extend(window.decoMistape, {

		onReady: function () {
			decoMistape.initDialogFx();

			var $dialog = $(decoMistape.dlg.el);

			$(document).on('click', '.mistape_action', function () {
				if ($(this).is('[data-action=send]')) {
					var data;
					if (!$dialog.data('dry-run') && (data = $dialog.data('report'))) {
						if ($dialog.data('mode') === 'comment') {
							data.comment = $dialog.find('#mistape_comment').val();
							$('#mistape_comment').val('');
						}
						data.post_id = $(this).data('id');
						decoMistape.reportSpellError(data);
					}
					decoMistape.animateLetter();
				} else if ($(this).is('[data-dialog-close]')) {
					decoMistape.dlg.toggle();
				}
			});

			$(document).keyup(function (ev) {
				if (ev.keyCode === 13 && ev.ctrlKey && ev.target.nodeName.toLowerCase() !== 'textarea' && $('#mistape_dialog.dialog--open').length === 0) {
					var report = decoMistape.getSelectionData();
					if (report) {
						decoMistape.showDialog(report);
					}
				}
			});
		},

		initDialogFx: function () {
			decoMistape.dlg = new DialogFx(document.getElementById('mistape_dialog'), {
				onOpenDialog       : function (dialog) {
					$(dialog.el).css('display', 'flex');
				},
				onCloseAnimationEnd: function (dialog) {
					$(dialog.el).css('display', 'none');
					decoMistape.resetDialog();
				}
			});
		},

		animateLetter: function () {
			var dialog = $(decoMistape.dlg.el),
				content = dialog.find('.dialog__content'),
				letterTop = dialog.find('.mistape-letter-top'),
				letterFront = dialog.find('.mistape-letter-front'),
				letterBack = dialog.find('.mistape-letter-back'),
				dialogWrap = dialog.find('.dialog-wrap');

			content.addClass('show-letter');

			setTimeout(function () {
				var y = (letterTop.offset().top - letterFront.offset().top) + letterTop.outerHeight();
				letterTop.css({
					'bottom' : Math.floor(y),
					'opacity': 1
				});
				jQuery('.mistape-letter-back-top').hide();
				if (content.hasClass('with-comment')) {
					dialogWrap.css('transform', 'scaleY(0.5) scaleX(0.28)');
				} else {
					dialogWrap.css('transform', 'scaleY(0.5) scaleX(0.4)');
				}
				setTimeout(function () {
					if (content.hasClass('with-comment')) {
						dialogWrap.css('transform', 'translateY(12%) scaleY(0.5) scaleX(0.4)');
					} else {
						dialogWrap.css('transform', 'translateY(28%) scaleY(0.5) scaleX(0.45)');
					}
					setTimeout(function () {
						letterTop.css('z-index', '9');
						letterTop.addClass('close');
						setTimeout(function () {
							dialogWrap.css({
								'visibility': 'hidden',
								'opacity'   : '0'
							});
							letterFront.css('animation', 'send-letter1 0.7s');
							letterBack.css('animation', 'send-letter1 0.7s');
							letterTop.css('animation', 'send-letter2 0.7s');
							setTimeout(function () {
								decoMistape.dlg.toggle();
							}, 400)
						}, 400)
					}, 400)
				}, 300)
			}, 400);
		},

		showDialog: function (report) {
			if (report.hasOwnProperty('selection') && report.hasOwnProperty('context')) {
				var $dialog = $(decoMistape.dlg.el);

				if ($dialog.data('mode') === 'notify') {
					decoMistape.reportSpellError(report);
					decoMistape.dlg.toggle();
				} else {
					$dialog.data('report', report);
					$dialog.find('#mistape_reported_text').html(report.preview_text);
					decoMistape.dlg.toggle();
				}
			}
		},

		resetDialog: function () {
			var $dialog = $(decoMistape.dlg.el);

			if ($dialog.data('mode') != 'notify') {
				$dialog.find('#mistape_confirm_dialog').css('display', '');
				$dialog.find('#mistape_success_dialog').remove();
			}

			// letter
			$dialog.find('.dialog__content').removeClass('show-letter');
			$dialog.find('.mistape-letter-top, .mistape-letter-front, .mistape-letter-back, .dialog-wrap, .mistape-letter-back-top').removeAttr('style');
			$dialog.find('.mistape-letter-top').removeClass('close');
		},

		reportSpellError: function (data) {
			data.action = 'mistape_report_error';
			$.ajax({
				type    : 'post',
				dataType: 'json',
				url     : decoMistape.ajaxurl,
				data    : data
			})
		},

		getSelectionData: function () {
			// Check for existence of window.getSelection()
			if (!window.getSelection) {
				return false;
			}

			var parentEl, sel, selChars, selWord, textToHighlight, maxContextLength = 140;

			var stringifyContent = function (string) {
				return typeof string === 'string' ? string.replace(/\s*(?:(?:\r\n)+|\r+|\n+)\t*/gm, '\r\n').replace(/\s{2,}/gm, ' ') : '';
			};

			var isSubstrUnique = function (substr, context) {
				if (typeof context === 'undefined') {
					context = decoMistape.contextBuffer;
				}
				if (typeof substr === 'undefined') {
					substr = decoMistape.selBuffer;
				}
				var split = context.split(substr);
				var count = split.length - 1;
				return count === 1;

			};

			var getExactSelPos = function (selection, context) {
				// if there is only one match, that's it
				if (isSubstrUnique(selWithContext, context)) {
					return context.indexOf(selWithContext);
				}
				// check if we can get the occurrence match from selection offsets
				if (!backwards) {
					// check anchor element
					if (context.substring(sel.anchorOffset, sel.anchorOffset + selection.length) == selection) {
						return sel.anchorOffset;
					}
					// check anchor parent element
					var parentElOffset = sel.anchorOffset;
					var prevEl = sel.anchorNode.previousSibling;
					while (prevEl !== null) {
						parentElOffset += prevEl.textContent.length;
						prevEl = prevEl.previousSibling;
					}
					if (context.substring(parentElOffset, parentElOffset + selection.length) == selection) {
						return parentElOffset;
					}
				}
				if (backwards && context.substring(sel.focusOffset, sel.focusOffset + selection.length) == selection) {
					return sel.anchorOffset;
				}
				return -1;
			};

			var getExtendedSelection = function (limit, nodeExtensions) {

				limit = parseInt(limit) || 40;
				nodeExtensions = nodeExtensions || {left: '', right: ''};
				var i = 0, selContent, selEndNode = sel.focusNode, selEndOffset = sel.focusOffset;

				while (i <= limit) {

					if ((selContent = stringifyContent(sel.toString().trim())).length >= maxContextLength || isSubstrUnique(selContent, context)) {
						return selContent;
					}

					// only even iteration
					if (i % 2 == 0 && sel.anchorOffset > 0 || nodeExtensions.left.length && i < limit / 2) {
						// reset
						if (backwards) {
							sel.collapseToEnd();
						} else {
							sel.collapseToStart();
						}
						sel.modify("move", direction[1], "character");
						sel.extend(selEndNode, selEndOffset);
					} else if (sel.focusOffset < sel.focusNode.length || nodeExtensions.right.length && i < limit / 2) {
						sel.modify('extend', direction[0], 'character');
						if (sel.focusOffset === 1) {
							selEndNode = sel.focusNode;
							selEndOffset = sel.focusOffset;
						}
					} else if (i % 2 === 0) {
						break;
					}

					i++;
				}

				return stringifyContent(sel.toString().trim());
			};

			var getExtendedContext = function (context, element, method) {
				var contentPrepend = '', contentAppend = '', e = element, i;
				method = method || 'textContent';

				for (i = 0; i < 20; i++) {
					if (contentPrepend || (e = e.previousSibling) === null) {
						break;
					}

					if ((contentPrepend = stringifyContent(e[method].trim())).length) {
						context = contentPrepend + context;

					}
				}

				// reset element
				e = element;

				for (i = 0; i < 20; i++) {
					if (contentAppend || (e = e.nextSibling) === null) {
						break;
					}
					if ((contentAppend = stringifyContent(e[method]).trim()).length) {
						context += contentAppend;
					} else if (context.slice(-1) !== ' ') {
						context += ' ';
					}
				}

				return {
					contents  : context,
					extensions: {
						left : contentPrepend,
						right: contentAppend
					}
				};
			};

			// check that getSelection() has a modify() method. IE has both selection APIs but no modify() method.
			// this works on modern browsers following standards
			if ((sel = window.getSelection()).modify) {
				// check if there is any text selected
				if (!sel.isCollapsed) {

					/**
					 * So the first step is to get selection extended to the boundaries of words
					 *
					 * e.g. if the sentence is "What a wonderful life!" and selection is "rful li",
					 * we get "wonderful life" stored in selWord variable
					 */

					selChars = sel.toString();

					// return early if no selection to work with or if its length exceeds the limit
					if (!selChars || selChars.length > maxContextLength) {
						return;
					}

					// here we get the nearest parent node which is common for the whole selection
					if (sel.rangeCount) {
						parentEl = sel.getRangeAt(0).commonAncestorContainer.parentNode;
						while (parentEl.textContent == sel.toString()) {
							parentEl = parentEl.parentNode;
						}
					}

					// Detect if selection was made backwards
					// further logic depends on it
					var range = document.createRange();
					range.setStart(sel.anchorNode, sel.anchorOffset);
					range.setEnd(sel.focusNode, sel.focusOffset);
					var backwards = range.collapsed;
					range = null;

					// save initial selection to restore in the end
					var initialSel = {
						focusNode   : sel.focusNode,
						focusOffset : sel.focusOffset,
						anchorNode  : sel.anchorNode,
						anchorOffset: sel.anchorOffset
					};

					// modify() works on the focus of the selection (not virtually) so we manipulate it
					var endNode = sel.focusNode, endOffset = sel.focusOffset;

					// determine second char of selection and the one before last
					// they will be our starting point for word boundaries detection
					var direction, secondChar, oneBeforeLastChar;
					if (backwards) {
						direction = ['backward', 'forward'];
						secondChar = selChars.charAt(selChars.length - 1);
						oneBeforeLastChar = selChars.charAt(0);
					} else {
						direction = ['forward', 'backward'];
						secondChar = selChars.charAt(0);
						oneBeforeLastChar = selChars.charAt(selChars.length - 1);
					}

					// collapse the cursor to the first char
					sel.collapse(sel.anchorNode, sel.anchorOffset);
					// move it one char forward
					sel.modify("move", direction[0], "character");

					// if the second character was a letter or digit, move cursor another step further
					// this way we are certain that we are in the middle of the word
					if (null === secondChar.match(/'[\w\d]'/)) {
						sel.modify("move", direction[0], "character");
					}

					// and now we can determine the beginning position of the word
					sel.modify("move", direction[1], "word");

					// then extend the selection up to the initial point
					// thus assure that selection starts with the beginning of the word
					sel.extend(endNode, endOffset);

					// do the same trick with the ending--extending it precisely up to the end of the word
					sel.modify("extend", direction[1], "character");
					if (null === oneBeforeLastChar.match(/'[\w\d]'/)) {
						sel.modify("extend", direction[1], "character");
					}
					sel.modify("extend", direction[0], "word");
					if (!backwards && sel.focusOffset === 1) {
						sel.modify("extend", 'backward', "character");
					}

					// since different browser extend by "word" differently and some of them extend beyond the word
					// covering spaces and punctuation, we need to collapse the selection back so it ends with the word
					var i = 0, lengthBefore, lengthAfter;
					while (i < 5 && (sel.toString().slice(-1).match(/[\s\n\t]/) || '').length) {
						lengthBefore = sel.toString().length;
						if (backwards) {
							endNode = sel.anchorOffset == 0 ? sel.anchorNode.previousSibling : sel.anchorNode;
							endOffset = sel.anchorOffset == 0 ? sel.anchorNode.previousSibling.length : sel.anchorOffset;
							sel.modify('move', 'backward', 'character');
							sel.extend(endNode, endOffset);
							backwards = false;
							direction = ['forward', 'backward'];
						} else {
							sel.modify('extend', 'backward', 'character');
						}
						lengthAfter = sel.toString().length;

						// workaround for WebKit quirk: undo last iteration
						if (lengthBefore - lengthAfter > 1) {
							sel.modify('extend', 'forward', 'character');
							break;
						}
					}

					// finally, we've got a modified selection which is bound to words
					// save it to highlight it later
					selWord = stringifyContent(sel.toString().trim());
				}
			}
			// this one is for IE11
			else if (sel = window.getSelection()) {
				var startOffset, startNode, endNode;
				selChars = sel.toString();
				range = document.createRange();
				if (range.collapsed) {
					startNode = sel.focusNode;
					endNode = sel.anchorNode;
					startOffset = sel.focusOffset;
					endOffset = sel.anchorOffset;
				} else {
					startNode = sel.anchorNode;
					endNode = sel.focusNode;
					startOffset = sel.anchorOffset;
					endOffset = sel.focusOffset;
				}

				while (startOffset && !startNode.textContent.slice(startOffset - 1, startOffset).match(/[\s\n\t]/)) {
					startOffset--;
				}
				while (endOffset < endNode.length && !endNode.textContent.slice(endOffset, endOffset + 1).match(/[\s\n\t]/)) {
					endOffset++;
				}

				// here we get the nearest parent node which is common for the whole selection
				if (sel.rangeCount) {
					parentEl = sel.getRangeAt(0).commonAncestorContainer.parentNode;
					while (parentEl.textContent == sel.toString()) {
						parentEl = parentEl.parentNode;
					}
				}

				selWord = stringifyContent(sel.toString().trim());

				// this logic is for IE<10
				// } else if ((sel = document.selection) && sel.type != "Control") {
				//     var textRange = sel.createRange();
				//
				//      if (!textRange || textRange.text.length > maxContextLength) {
				//      return;
				//      }
				//
				//      if (textRange.text) {
				//      selChars = textRange.text;
				//      textRange.expand("word");
				//      // Move the end back to not include the word's trailing space(s), if necessary
				//      while (/\s$/.test(textRange.text)) {
				//      textRange.moveEnd("character", -1);
				//      }
				//      selWord = textRange.text;
				//      parentEl = textRange.parentNode;
				//      }

			}


			if (typeof parentEl == 'undefined') {
				return;
			}

			var selToFindInContext,
				contextsToCheck = { // different browsers implement different methods, we try them by turn
					textContent: parentEl.textContent,
					innerText  : parentEl.innerText
				};

			textToHighlight = selWord;

			for (var method in contextsToCheck) {
				if (contextsToCheck.hasOwnProperty(method) && typeof contextsToCheck[method] != 'undefined') {

					// start with counting selected word occurrences in context
					var scope = {selection: 'word', context: 'initial'};
					var context = stringifyContent(contextsToCheck[method].trim());
					var selWithContext = stringifyContent(sel.toString().trim());
					decoMistape.contextBuffer = context;
					decoMistape.selBuffer = selWithContext;
					var selPos; // this is what we are going to find
					var selExactMatch = false;


					if ((selPos = getExactSelPos(selWithContext, context)) != -1) {
						selExactMatch = true;
						selToFindInContext = selWithContext;
						break;
					}

					// if there is more than one occurrence, extend the selection
					selWithContext = getExtendedSelection(40);
					scope.selection = 'word extended';

					if ((selPos = getExactSelPos(selWithContext, context)) != -1) {
						selExactMatch = true;
						selToFindInContext = selWithContext;
						break;
					}

					// if still have duplicates, extend the context and selection, and try again
					var initialContext = context;
					var extContext = getExtendedContext(context, parentEl, method);
					context = extContext.contents;
					selWithContext = getExtendedSelection(40, extContext.extensions);
					scope.context = 'extended';

					if ((selPos = getExactSelPos(selWithContext, context)) != -1) {
						selExactMatch = true;
						selToFindInContext = selWithContext;
						break;
					}

					// skip to next context getting method and start over, or exit
					if (!selWithContext) {
						continue;
					}

					if (isSubstrUnique(selWord, selWithContext) || selWord == selChars.trim()) {
						context = selWithContext;
						selWithContext = selWord;
						textToHighlight = selWord;
						scope.selection = 'word';
						scope.context = 'extended';
					} else {
						context = selWord;
						selWithContext = selChars.trim();
						textToHighlight = selChars.trim();
						scope.selection = 'initial';
						scope.context = 'word';
					}

					selPos = context.indexOf(selWithContext);

					if (selPos !== -1) {
						selToFindInContext = selWithContext;
					} else if ((selPos = context.indexOf(selWord)) !== -1) {
						selToFindInContext = selWord;
					} else if ((selPos = context.indexOf(selChars)) !== -1) {
						selToFindInContext = selChars;
					} else {
						continue;
					}
					break;
				}
			}

			if (selToFindInContext) {
				sel.removeAllRanges();
			} else {
				decoMistape.restoreInitSelection(sel, initialSel);
				return;
			}

			if (scope.context === 'extended') {
				context = extContext.extensions.left + initialContext + ' ' + extContext.extensions.right;
			}

			var contExcerptStartPos, contExcerptEndPos, selPosInContext, highlightedChars, previewText;
			maxContextLength = Math.min(context.length, maxContextLength);

			var truncatedContext = context;

			if (context.length > maxContextLength) {

				if (selPos + selToFindInContext.length / 2 < maxContextLength / 2) {
					selPosInContext = 'beginning';
					contExcerptStartPos = 0;
					contExcerptEndPos = Math.max(selPos + selToFindInContext.length, context.indexOf(' ', maxContextLength - 10));
				} else if (selPos + selToFindInContext.length / 2 > context.length - maxContextLength / 2) {
					selPosInContext = 'end';
					contExcerptStartPos = Math.min(selPos, context.indexOf(' ', context.length - maxContextLength + 10));
					contExcerptEndPos = context.length;
				} else {
					selPosInContext = 'middle';
					var centerPos = selPos + Math.round(selToFindInContext.length / 2);
					contExcerptStartPos = Math.min(selPos, context.indexOf(' ', centerPos - maxContextLength / 2 - 10));
					contExcerptEndPos = Math.max(selPos + selToFindInContext.length, context.indexOf(' ', centerPos + maxContextLength / 2 - 10));
				}

				truncatedContext = context.substring(contExcerptStartPos, contExcerptEndPos).trim();

				if (selPosInContext !== 'beginning' && context.charAt(contExcerptStartPos - 1) !== '.') {
					truncatedContext = '... ' + truncatedContext;
				}
				if (selPosInContext !== 'end' && context.charAt(contExcerptStartPos + contExcerptEndPos - 1) !== '.') {
					truncatedContext = truncatedContext + ' ...';
				}
			}

			if (isSubstrUnique(selChars, textToHighlight)) {
				highlightedChars = textToHighlight.replace(selChars, '<span class="mistape_mistake_inner">' + selChars + '</span>')
			} else {
				highlightedChars = '<strong class="mistape_mistake_inner">' + textToHighlight + '</strong>';
			}

			var selWithContextHighlighted = selToFindInContext.replace(textToHighlight, '<span class="mistape_mistake_outer">' + highlightedChars + '</span>');

			if (selExactMatch && truncatedContext === context) {
				previewText = truncatedContext.substring(0, selPos) + selWithContextHighlighted + truncatedContext.substring(selPos + selWithContext.length) || selWithContextHighlighted;
			} else {
				previewText = truncatedContext.replace(selWithContext, selWithContextHighlighted) || selWithContextHighlighted;
			}

			return {
				selection      : selChars,
				word           : selWord,
				replace_context: selToFindInContext,
				context        : truncatedContext,
				preview_text   : previewText,
				// post_id: decoMistape.getPostId()
			};
		},

		restoreInitSelection: function (sel, initialSel) {
			sel.collapse(initialSel.anchorNode, initialSel.anchorOffset);
			sel.extend(initialSel.focusNode, initialSel.focusOffset);
		}
	});

	$(document).ready(decoMistape.onReady);

})(jQuery);
