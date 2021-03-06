/**
 * List Email
 *
 * @copyright: Copyright (C) 2005-2015, fabrikar.com - All rights reserved.
 * @license:   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
define(['jquery', 'fab/list-plugin', 'fab/fabrik'], function (jQuery, FbListPlugin, Fabrik) {
	var FbListEmail = new Class({

		Extends: FbListPlugin,

		initialize: function (options) {
			this.parent(options);
		},

		buttonAction: function () {
			var url = 'index.php?option=com_fabrik&controller=list.email&task=popupwin&tmpl=component&ajax=1&id=' +
				this.listid + '&renderOrder=' + this.options.renderOrder;
			this.listform.getElements('input[name^=ids]').each(function (id) {
				if (id.get('value') !== false && id.checked !== false) {
					url += '&ids[]=' + id.get('value');
				}
			});
			if (this.listform.getElement('input[name=checkAll]').checked) {
				url += '&checkAll=1';
			}
			else {
				url += '&checkAll=0';
			}
			var id = 'email-list-plugin';
			this.windowopts = {
				'id'         : id,
				title        : 'Email',
				loadMethod   : 'iframe',
				contentURL   : url,
				width        : 520,
				height       : 470,
				evalScripts  : true,
				'minimizable': false,
				'collapsible': true
			};
			Fabrik.getWindow(this.windowopts);
		}

	});

	return FbListEmail;
});
