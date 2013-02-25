
DyEditor.plugin('pagebreak', function(K) {
	var self = this, name = 'pagebreak';
	self.clickToolbar(name, function() {
		var cmd = self.cmd, range = cmd.range;
		self.focus();
		range.enlarge(true);
		cmd.split(true);
		var tail = self.newlineTag == 'br' || K.WEBKIT ? '' : '<p id="__dyeditor_tail_tag__"></p>';
		self.insertHtml('<hr style="page-break-after: always;" class="ke-pagebreak" />' + tail);
		if (tail !== '') {
			var p = K('#__dyeditor_tail_tag__', self.edit.doc);
			range.selectNodeContents(p[0]);
			p.removeAttr('id');
			cmd.select();
		}
	});
});
