jQuery(document).ready(function ($) {

	"use strict"

	var l10n = mailster_mce_button.l10n,
		tags = mailster_mce_button.tags,
		designs = mailster_mce_button.designs;

	tinymce.PluginManager.add('mailster_mce_button', function (editor, url) {
		editor.addButton('mailster_mce_button', {
			title: l10n.title,
			type: 'menubutton',
			icon: 'icon mailster-tags-icon',
			menu: $.map(tags, function (group, id) {
				return {
					text: group.name,
					menu: $.map(group.tags, function (name, id) {
						return {
							text: name,
							onclick: function () {
								editor.insertContent('{' + id + '}');
							}
						};

					})
				};
			})
		});
	});
});