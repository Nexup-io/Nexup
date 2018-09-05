/**
 * @copyright (C) FIT-Media.com, {@link http://fit-media.com}
 * Date: 12/24/2015, Time: 4:12 PM
 *
 * @author Dmitrij "m00nk" Sheremetjev <m00nk1975@gmail.com>
 * @package
 */

(function($)
{
	// параметры
	var settings = {
		// х�?ндлеры
		onStartDrag: null, // function(table, index, mode), mode = ['horiz', 'vert']
		onDrop: null       // function(startIndex, dropIndex, mode), mode = ['horiz', 'vert']
	};

	// передвигаемый объект
	var movableObject = {
		active: false,
		srcTable: null,
		fakeObj: null,
		lastX: 0,
		lastY: 0,
		startIndex: 0,
		lastIndex: 0,
		arrow: null
	};

	$.fn.tabledragdrop = function(callerSettings)
	{
		settings = $.extend(settings, callerSettings || {});

		this.each(function()
		{
			var table = $(this);
			if(table.is('table'))
			{ // наш клиент
				table.find('thead th:not([class*=noDrag] .icon-more-h.move_col)').bind('mousedown', colOnMouseDown);
				table.find('tbody th:not([class*=noDrag]) .icon-more').bind('mousedown', rowOnMouseDown);
			}
		});

		//-----------------------------------------
		function colOnMouseDown(ev)
		{
			if(!movableObject.active)
			{
				movableObject.lastX = ev.pageX;
				movableObject.lastY = ev.pageY;

				var trg = $(ev.target);
				if(!trg.is('th')) trg = $(trg.parents('th')[0]);

				movableObject.startIndex = movableObject.lastIndex = trg.prop('cellIndex');
				movableObject.srcTable = $(trg.parents('table')[0]);
				_createVertTable();
				movableObject.fakeObj.css('width', trg.outerWidth() + 'px');
				movableObject.fakeObj.appendTo($('body'));

				// вызов �?обыти�?
				if(settings.onStartDrag) settings.onStartDrag(movableObject.srcTable, movableObject.lastIndex, 'horiz');

				disableSelection($(document).css('cursor', 'move')
					.bind('mousemove', colOnMouseMove)
					.one('mouseup', colOnMouseUp));
			}
		}

		function colOnMouseUp(ev)
		{
			enableSelection($(document).css({cursor: 'auto'}).unbind('mousemove'));

			//-----------------------------------------
			// размораживаем �?толбец
			movableObject.srcTable.find('.drag_freeze').removeClass('drag_freeze');

			// отключаем от�?леживание перемещени�?
			movableObject.active = false;

			// убиваем плавающую таблицу
			movableObject.fakeObj.remove();
			movableObject.fakeObj = null;

			// убиваем �?трелку
			movableObject.arrow.remove();
			movableObject.arrow = null;

			//-----------------------------------------
			// вызов �?обыти�?
			if(settings.onDrop
				&& movableObject.startIndex != movableObject.lastIndex
				&& movableObject.startIndex != movableObject.lastIndex + 1)
				settings.onDrop(movableObject.startIndex, movableObject.lastIndex, 'horiz');

			//-----------------------------------------
			// перено�?им �?толбец в новую позицию
			if(movableObject.startIndex != movableObject.lastIndex && movableObject.startIndex != movableObject.lastIndex + 1)
			{ // и�?ходный и целевой �?толбцы не �?овпадают - нужно двигать
				movableObject.srcTable.find('tr').each(function()
				{
					$(this).find('td').add($(this).find('th')).each(function()
					{
						if(this.cellIndex == movableObject.startIndex)
						{
							var s = $(this);
							s.siblings().each(function()
							{
								if(this.cellIndex == movableObject.lastIndex)
								{ // перено�?им
									s.insertAfter($(this));
									return false;
								}
							});
							return false;
						}

					});
				});
			}
		}

		function colOnMouseMove(ev)
		{
			if(movableObject.active)
			{
				var dx = movableObject.lastX - ev.pageX;
				movableObject.lastX = ev.pageX;

				var p = movableObject.fakeObj.position();
				var x = p.left - dx;

				movableObject.fakeObj.css('left', x + 'px');

				//-----------------------------------------
				// определ�?ем над каким �?толбцом нави�?ла "угроза" и ри�?уем �?трелочку

				var cells = $(movableObject.srcTable.find('tbody tr')[0]).find('td');

				cells.each(function(i)
				{
					var cell = $(this);
					var cp = cell.position();
					var x1 = cp.left;
					var x2 = x1 + cell.outerWidth();
					if(i == 0 && x1 >= x)
					{
						movableObject.arrow.css('left', x1 - 8 + 'px');
						movableObject.lastIndex = this.cellIndex - 1;
						return false;
					}
					if(x1 <= x && x <= x2)
					{
						movableObject.arrow.css('left', x2 - 8 + 'px');
						movableObject.lastIndex = this.cellIndex;
						return false;
					}
				});
			}
		}

		/*** ROWS ***/
		function rowOnMouseDown(ev)
		{
		
			if(!movableObject.active)
			{
				movableObject.lastX = ev.pageX;
				movableObject.lastY = ev.pageY;

				var trg = $($(ev.target).parents('tr')[0]);
				movableObject.startIndex = movableObject.lastIndex = trg.prop('rowIndex');
				movableObject.srcTable = $(trg.parents('table')[0]);
				_createHorizTable();
				movableObject.fakeObj.css('width', trg.outerWidth() + 'px');
				movableObject.fakeObj.appendTo($('body'));

				// вызов �?обыти�?
				if(settings.onStartDrag) settings.onStartDrag(movableObject.srcTable, movableObject.lastIndex, 'vert');

				disableSelection($(document).css('cursor', 'move')
					.bind('mousemove', rowOnMouseMove)
					.one('mouseup', rowOnMouseUp));
			}
		}

		function rowOnMouseUp(ev)
		{
			enableSelection($(document).css({cursor: 'auto'}).unbind('mousemove'));

			//-----------------------------------------
			// размораживаем �?толбец
			movableObject.srcTable.find('.drag_freeze').removeClass('drag_freeze');

			// отключаем от�?леживание перемещени�?
			movableObject.active = false;

			// убиваем плавающую таблицу
			movableObject.fakeObj.remove();
			movableObject.fakeObj = null;

			// убиваем �?трелку
			movableObject.arrow.remove();
			movableObject.arrow = null;

			//-----------------------------------------
			// вызов �?обыти�?
			if(settings.onDrop
				&& movableObject.startIndex != movableObject.lastIndex
				&& movableObject.startIndex != movableObject.lastIndex + 1)
				settings.onDrop(movableObject.startIndex, movableObject.lastIndex, 'vert');

			//-----------------------------------------
			// перено�?им �?троку в новую позицию
			if(movableObject.startIndex != movableObject.lastIndex && movableObject.startIndex != movableObject.lastIndex + 1)
			{ // и�?ходна�? и целева�? �?трока не �?овпадают - нужно двигать
				var s = movableObject.srcTable.find('tr:eq(' + movableObject.startIndex + ')');
				var d = movableObject.srcTable.find('tr:eq(' + movableObject.lastIndex + ')');
				if($(d.parents('tbody')[0]).is('tbody'))
					s.insertAfter(d);
				else
					s.insertBefore($(movableObject.srcTable.find('tbody tr')[0]));
			}
		}

		function rowOnMouseMove(ev)
		{

			if(movableObject.active)
			{
				var dy = movableObject.lastY - ev.pageY;
				movableObject.lastY = ev.pageY;

				var p = movableObject.fakeObj.position();
				var y = p.top - dy;

				movableObject.fakeObj.css('top', y + 'px');

				//-----------------------------------------
				// определ�?ем над какой �?трокой нави�?ла "угроза" и ри�?уем �?трелочку
				movableObject.srcTable.find('tbody tr').each(function(i)
				{
					var row = $(this);

					var rp = row.offset();
					var y1 = rp.top;
					var y2 = y1 + row.outerHeight();
					if(i == 0 && y1 >= y)
					{
						movableObject.arrow.css('top', y1 - 8 + 'px');
						movableObject.lastIndex = this.rowIndex - 1;
						return false;
					}
					if(y1 <= y && y <= y2)
					{
						movableObject.arrow.css('top', y2 - 8 + 'px');
						movableObject.lastIndex = this.rowIndex;
						return false;
					}
				});

			}
		}

		/*** HELPERS ***/

		function _createHorizTable()
		{
			movableObject.fakeObj = $('<table class="ghost" ' + _getElementAttributes(movableObject.srcTable) + '></table>')
				.css({'opacity': 0.85, 'margin': 0, 'display': 'block'});
			var tr = $('<tr>').appendTo(movableObject.fakeObj);
			movableObject.arrow = $('<div class="arrow_right"></div>').appendTo($('body'));
			movableObject.active = true;

			var flag = true;

			movableObject.srcTable.find('tr:eq(' + movableObject.startIndex + ')').children().each(function()
			{
				var c = $(this);
				if(flag)
				{ // позиционируем новую таблицу
					var p = c.offset();
					movableObject.fakeObj.css({'left': p.left + 20 + 'px', 'top': p.top + 'px'});
					movableObject.arrow.css({'left': p.left - 16 + 'px', 'top': p.top - 8 + 'px'});
					flag = false;
				}

				if(c.is('th'))
					$('<th>').html(c.html()).appendTo(tr);
				else
					$('<td>').html(c.html()).appendTo(tr);
				c.addClass('drag_freeze');
			});
		}

		function _createVertTable()
		{
			movableObject.fakeObj = $('<table class="ghost" ' + _getElementAttributes(movableObject.srcTable) + '></table>')
				.css({'opacity': 0.85, 'margin': 0, 'display': 'block'});
			movableObject.arrow = $('<div class="arrow_down"></div>').appendTo($('body'));
			movableObject.active = true;

			var flag = true;

			movableObject.srcTable.find('th').add(movableObject.srcTable.find('td')).each(function()
			{
				if(this.cellIndex == movableObject.startIndex)
				{
					var c = $(this);
					if(flag)
					{ // позиционируем новую таблицу
						var p = c.offset();
						movableObject.fakeObj.css({'left': p.left + 'px', 'top': p.top + 10 + 'px'});
						movableObject.arrow.css({'left': p.left - 8 + 'px', 'top': p.top - 16 + 'px'});
						flag = false;
					}

					var tr = $('<tr>').appendTo(movableObject.fakeObj);
					if(c.is('th'))
						$('<th>').html(c.html()).appendTo(tr);
					else
						$('<td>').html(c.html()).appendTo(tr);
					c.addClass('drag_freeze');
				}
			});
		}

		function _getElementAttributes(element)
		{
			var attrsString = '',
				attrs = element[0].attributes;
			for(var i = 0, length = attrs.length; i < length; i++)
			{
				attrsString += attrs[i].nodeName + '="' + attrs[i].nodeValue + '"';
			}
			return attrsString;
		}

		function disableSelection(obj)
		{
			var e = "onselectstart" in document.createElement("div") ? "selectstart" : "mousedown";
			return function()
			{
				return obj.bind(e + ".ui-disableSelection", function(e){e.preventDefault()})
			}
		}

		function enableSelection(obj)
		{
			return obj.unbind(".ui-disableSelection")
		}
	}
})(jQuery);