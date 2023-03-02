define([
    'Amasty_Feed/js/code_mirror/lib/codemirror',
    'Amasty_Feed/js/code_mirror/addon/mode/simple',
    'prototype'
], function (CodeMirror) {
    class xmlEditorMirror {
        constructor(feedId) {
            CodeMirror.defineSimpleMode(`amasty_feed${feedId}`, {
                start: [
                    {regex: /"(?:[^\\]|\\.)*?"/, token: "atom"},
                    {
                        regex: /(?:attribute|format|optional|parent|modify)\b/,
                        token: "string"
                    },
                    {regex: /attribute|custom_field|text|images/, token: "atom"},
                    {regex: /\<!\[CDATA\[/, token: "amcdata", next: "amcdata"},
                    {regex: /\</, token: "amtag", next: "amtag"},

                    {regex: /[\{|%]/, token: "def"},
                    {regex: /[\}|%]/, token: "def"},

                ],
                amtag: [
                    {regex: /.*?>/, token: "amtag", next: "start"},
                    {regex: /.*/, token: "amtag"}
                ],
                amcdata: [
                    {regex: /.*?]]>/, token: "amcdata", next: "start"},
                    {regex: /.*/, token: "amcdata"}
                ]
            });
            this.editor = null;
            this.header =  null;
            this.footer =  null;
            this.selectedRow =  {};
            this.updateMode =  false;
            this.navigator =  {};
            this.modifyTemplate =  null;
            this.modifyConfig =  null;
            this.modifyArgs =  null;
            this.modifyCount =  0;
            this.buttons =  {
                 insert: null,
                 update: null
            };
            this.updateBtn = null;
            this.feedId = feedId;
        };

         clearSelectedRow() {
            this.updateMode = false;
            this.selectedRow = {
                tag: null,
                attribute: null,
                format: null,
                optional: null,
                parent: null
            };

            var modifyContainer = $(this.tableId + "_modify_container")

            if (modifyContainer) {
                modifyContainer.innerHTML = '';
            }
        };

        refresh () {
            if (this.editor) {
                this.editor.refresh();
                this.editor.save();
            }

            if (this.header) {
                this.header.refresh();
                this.header.save();
            }

            if (this.footer) {
                this.footer.refresh();
                this.footer.save();
            }
        };

        init (modifyTemplate, modifyConfig, modifyArgs, tableId, xmlTableId, isMerged) {
            this.modifyTemplate = modifyTemplate;
            this.modifyConfig = modifyConfig;
            this.modifyArgs = modifyArgs;
            this.tableId = tableId;
            this.xmlTable = xmlTableId;
            this.isMerged = !!isMerged;
            let textArea = $(this.tableId);
            let xmlHeader = $('feed_xml_header');
            let xmlFooter = $('feed_xml_footer');

            if (textArea) {
                this.editor = CodeMirror.fromTextArea(textArea, {
                    mode: `amasty_feed${this.feedId}`,
                    alignCDATA: true,
                    lineNumbers: false,
                    viewportMargin: Infinity
                });

                this.editor.setSize(null, 400);
            }

            if (xmlHeader) {
                this.header = CodeMirror.fromTextArea(xmlHeader, {
                    mode: `amasty_feed${this.feedId}`,
                    alignCDATA: true,
                    lineNumbers: false,
                    viewportMargin: Infinity
                });

                this.header.setSize(null, 100);
            }

            if (xmlFooter) {
                this.footer = CodeMirror.fromTextArea(xmlFooter, {
                    mode: `amasty_feed${this.feedId}`,
                    alignCDATA: true,
                    lineNumbers: false,
                    viewportMargin: Infinity
                });

                this.footer.setSize(null, 100);
            }

            this.editor.on("cursorActivity", this.cursorActivity.bind(this));

            this.clearSelectedRow();
            this.initNavigator();
            this.initButtons();
            this.updateNavigator();

            setInterval(this.refresh.bind(this), 100);
        };

        initNavigator () {
            var container = document.querySelector(`[id="${this.xmlTable}"]`);

            this.navigator = {
                tag: container.down(`[id="${this.tableId}_tag"]`),
                attribute: container.down(`[id="${this.tableId}_attribute"]`),
                format: container.down(`[id="${this.tableId}_format"]`),
                optional: container.down(`[id="${this.tableId}_optional"]`),
                parent: container.down(`[id="${this.tableId}_parent"]`)
            }
        };

        initButtons () {
            var container = document.querySelector(`[id="${this.xmlTable}"]`);

            this.buttons.insert = container.down("#insert_button");
            this.buttons.update = container.down("#update_button");

            this.buttons.insert && this.buttons.insert.observe('click', this.inserRow.bind(this));
            this.buttons.update && this.buttons.update.observe('click', this.updateRow.bind(this));
        };

        getXMLRowFormat () {
            var ret = "";

            switch (this.navigator.insert_type.value) {
                case "images":
                    ret = this.navigator.insert_image_format.value;
                    break;
                default:
                    ret = this.navigator.insert_format.value;
                    break;
            }

            return ret;
        };

        getRow () {
            if (this.isMerged) {
                var tpl = `{:attribute${(this.navigator.parent && this.navigator.parent.value === 'yes') ? '|parent' : ''}}`;
            } else {
                var tpl = '{attribute=":attribute" format=":format" parent=":parent" optional=":optional" modify=":modify"}';
            }

            var modifyArr = [];
            for (var idx = 0; idx < this.modifyCount; idx++) {
                var modify = $('field_row_' + idx + '_modify');

                if (modify) {
                    var modifyValue = modify.value;
                    var args = [];

                    if (this.modifyArgs[modifyValue]) {
                        args = this.modifyArgs[modifyValue];
                    }

                    var modifyString = modify.value;

                    if (args.length > 0) {
                        modifyString += ':';
                        var values = [];

                        for (var argIdx = 0; argIdx < args.length; argIdx++) {
                            var arg = $('field_row_' + idx + '_arg' + argIdx);
                            if (arg) {
                                values.push(arg.value);
                            }
                        }
                        modifyString += values.join("^");
                    }
                    modifyArr.push(modifyString);
                }
            }

            var repl = {
                ':tag': this.navigator.tag && this.navigator.tag.value,
                ':attribute': this.navigator.attribute && this.navigator.attribute.value,
                ':format': this.navigator.format && this.navigator.format.value,
                ':optional': this.navigator.optional && this.navigator.optional.value,
                ':parent': this.navigator.parent && this.navigator.parent.value,
                ':modify': modifyArr.join("|")
            };

            $H(repl).each(function (item) {
                tpl = tpl.replace(eval('/' + item.key + '/g'), item.value);
            });

            if (this.navigator.tag && this.navigator.tag.value) {
                tpl = "<" + this.navigator.tag.value + ">" + tpl + "</" + this.navigator.tag.value + ">";
            }

            return tpl;
        };

        updateRow () {
            var originLine = this.editor.getLine(this.editor.getCursor().line);

            var line = this.getRow();

            this.editor.replaceRange(line, {
                line: this.editor.getCursor().line,
                ch: 0
            }, {
                line: this.editor.getCursor().line,
                ch: originLine.length
            });
        };

        inserRow () {
            if (this.isMerged) {
                this.editor.replaceSelection(this.getRow());
            } else {
                this.editor.replaceSelection(this.getRow() + '\n');
            }
        };

        cursorActivity () {
            this.clearSelectedRow();

            var line = this.editor.getLine(this.editor.getCursor().line);
            var tagMatch = line.match(/<([^>]+)>(.*?)<\/\1>/);

            if (tagMatch && tagMatch.length == 3) {
                this.selectedRow.tag = tagMatch[1];
                this.updateMode = true;
            }

            var varsRe = /(attribute|format|optional|parent)="(.*?)"/g;
            var varsArr;

            while ((varsArr = varsRe.exec(line)) != null) {
                if (varsArr && varsArr.length === 3) {
                    if (this.selectedRow[varsArr[1]] !== undefined) {
                        this.selectedRow[varsArr[1]] = varsArr[2];
                    }
                    this.updateMode = true;
                }
            }

            this.restoreModify(line);
            this.updateNavigator();
        };

        restoreModify (line) {
            var varsRe = /(modify)="(.*?)"/g;
            var varsArr = varsRe.exec(line);

            if (varsArr && varsArr.length == 3) {
                var modificators = varsArr[2].split("|");
                for (var idx in modificators) {
                    var modificator = modificators[idx];
                    if (typeof(modificator) != 'function') {
                        var modificatorArr = modificator.split(/:(.+)?/, 2);

                        var modify = modificatorArr[0];

                        if ($(this.modifyConfig).indexOf(modify) != -1) {
                            var rowIndex = this.modifyItem();
                            var select = $('field_row_' + rowIndex + '_modify');
                            if (select) {
                                select.value = modify;
                                this.changeModifier(select);
                            }

                            var args = [];

                            if (this.modifyArgs[select.getValue()]) {
                                args = this.modifyArgs[select.getValue()];
                            }

                            if (args.length > 0 && modificatorArr[1]) {
                                var values = modificatorArr[1].split("^");

                                for (var idx = 0; idx < args.length; idx++) {
                                    var id = select.id.replace("_modify", "_arg" + idx);
                                    var input = $(id);
                                    if (input && values[idx]) {
                                        input.value = values[idx];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        };

        updateNavigator () {
            if (!this.isMerged) {
                this.setValue(this.navigator.tag, this.selectedRow.tag);
                this.setValue(this.navigator.format, this.selectedRow.format);
                this.setValue(this.navigator.optional, this.selectedRow.optional);
            }

            this.setValue(this.navigator.attribute, this.selectedRow.attribute);
            this.setValue(this.navigator.parent, this.selectedRow.parent);

            if (this.updateMode) {
                this.buttons.update && this.buttons.update.removeClassName('hidden');
                this.buttons.insert && this.buttons.insert.addClassName('hidden');
            } else {
                this.buttons.update && this.buttons.update.addClassName('hidden');
                this.buttons.insert && this.buttons.insert.removeClassName('hidden');
            }
        };

        setValue (input, value) {
            if (value !== null) {
                input.setValue(value)
            }
        };

        modifyItem (a) {
            var container = document.querySelector(`[id="${this.tableId}_modify_container"]`);
            var data = {
                index: this.modifyCount++
            };
            if (container) {
                Element.insert(container, {
                    bottom: this.modifyTemplate({
                        data: data
                    })
                });
            }
            return data.index;
        };

        changeModifier (select) {
            var td = select.up('td');

            var args = [];

            if (this.modifyArgs[select.getValue()]) {
                args = this.modifyArgs[select.getValue()];
            }

            td.select('input').each(function (input) {
                input.hide();
            });

            for (var idx = 0; idx < args.length; idx++) {
                var id = select.id.replace("_modify", "_arg" + idx);
                var input = td.down("#" + id);
                if (input) {
                    input.show();
                    input.setAttribute('placeholder', args[idx]);
                }
            }
        };

        deleteItem (event) {
            var tr = Event.findElement(event, 'tr');
            if (tr) {
                Element.select(tr, ['input', 'select']).each(function (element) {
                    element.remove();
                });
                Element.remove(tr);
            }
            return false;
        }
    };

    return xmlEditorMirror;
});
