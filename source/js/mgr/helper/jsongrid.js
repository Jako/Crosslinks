Crosslinks.grid.JsonGrid = function (config) {
    config = config || {};
    this.ident = 'crosslinks-jsongrid-' + Ext.id();
    this.hiddenField = new Ext.form.TextArea({
        name: config.hiddenName || config.name,
        hidden: true
    });
    this.fieldConfig = config.fieldConfig || [{name: 'key'}, {name: 'value'}];
    this.fieldConfig.push({name: 'id', hidden: true});
    this.fieldColumns = [];
    this.fieldNames = [];
    Ext.each(this.fieldConfig, function (el) {
        this.fieldNames.push(el.name);
        this.fieldColumns.push({
            header: el.header || _(el.name),
            dataIndex: el.name,
            editable: true,
            menuDisabled: true,
            hidden: el.hidden || false,
            editor: {
                xtype: el.xtype || 'textfield',
                allowBlank: el.allowBlank || true,
                enableKeyEvents: true,
                fieldname: el.name,
                listeners: {
                    change: {
                        fn: this.saveValue,
                        scope: this
                    },
                    keyup: {
                        fn: function (sb) {
                            var record = this.getSelectionModel().getSelected();
                            if (record) {
                                record.set(sb.fieldname, sb.el.dom.value);
                                this.saveValue();
                            }
                        },
                        scope: this
                    }
                }
            },
            renderer: function (value, metadata) {
                metadata.css += 'x-editable-column ';
                return value;
            },
            width: el.width || 100
        });
    }, this);
    if (Crosslinks.config.modxversion === "2") {
        this.fieldColumns.push({
            width: 50,
            menuDisabled: true,
            renderer: this.actionsColumnRenderer.bind(this)
        });
    }
    Ext.applyIf(config, {
        id: this.ident,
        fields: this.fieldNames,
        autoHeight: true,
        store: new Ext.data.JsonStore({
            fields: this.fieldNames,
            data: this.loadValue(config.value)
        }),
        enableDragDrop: true,
        ddGroup: this.ident + '-json-grid-dd',
        labelStyle: 'position: absolute',
        columns: this.fieldColumns,
        disableContextMenuAction: true,
        tbar: ['->', {
            text: '<i class="icon icon-plus"></i> ' + _('add'),
            cls: 'primary-button',
            handler: this.addElement,
            scope: this
        }],
        listeners: {
            render: {
                fn: this.renderListener,
                scope: this
            }
        }
    });
    Crosslinks.grid.JsonGrid.superclass.constructor.call(this, config);
};
Ext.extend(Crosslinks.grid.JsonGrid, MODx.grid.LocalGrid, {
    getMenu: function () {
        var m = [];
        m.push({
            text: _('remove'),
            handler: this.removeElement
        });
        return m;
    },
    getActions: function () {
        return [{
            action: 'removeElement',
            icon: 'trash-o',
            text: _('remove')
        }]
    },
    addElement: function () {
        var ds = this.getStore();
        var row = {};
        Ext.each(this.fieldNames, function (fieldname) {
            row[fieldname] = '';
        });
        row['id'] = this.getStore().getCount();
        this.getStore().insert(this.getStore().getCount(), new ds.recordType(row));
        this.getView().refresh();
        this.getSelectionModel().selectRow(0);
    },
    removeElement: function () {
        Ext.Msg.confirm(_('remove') || '', _('confirm_remove') || '', function (e) {
            if (e === 'yes') {
                var ds = this.getStore();
                var rows = this.getSelectionModel().getSelections();
                if (!rows.length) {
                    return false;
                }
                for (var i = 0; i < rows.length; i++) {
                    var id = rows[i].id;
                    var index = ds.findBy(function (record) {
                        if (record.id === id) {
                            return true;
                        }
                    });
                    ds.removeAt(index);
                }
                this.getView().refresh();
                this.saveValue();
            }
        }, this);
    },
    renderListener: function (grid) {
        new Ext.dd.DropTarget(grid.container, {
            copy: false,
            ddGroup: this.ident + '-json-grid-dd',
            notifyDrop: function (dd, e, data) {
                var ds = grid.store;
                var sm = grid.getSelectionModel();
                var rows = sm.getSelections();

                var dragData = dd.getDragData(e);
                if (dragData) {
                    var cindex = dragData.rowIndex;
                    if (typeof (cindex) !== "undefined") {
                        for (var i = 0; i < rows.length; i++) {
                            ds.remove(ds.getById(rows[i].id));
                        }
                        ds.insert(cindex, data.selections);
                        sm.clearSelections();
                    }
                }
                grid.getView().refresh();
                grid.saveValue();
            }
        });
        this.add(this.hiddenField);
        this.saveValue();
    },
    loadValue: function (value) {
        value = Ext.util.JSON.decode(value);
        if (value && Array.isArray(value)) {
            Ext.each(value, function (record, idx) {
                value[idx]['id'] = idx;
            });
        } else {
            value = [];
        }
        return value;
    },
    saveValue: function () {
        var value = [];
        Ext.each(this.getStore().getRange(), function (record) {
            var row = {};
            Ext.each(this.fieldNames, function (fieldname) {
                if (fieldname !== 'id') {
                    row[fieldname] = record.data[fieldname];
                }
            });
            value.push(row);
        }, this);
        this.hiddenField.setValue(Ext.util.JSON.encode(value));
    },
    _getActionsColumnTpl: function () {
        return new Ext.XTemplate('<tpl for=".">'
            + '<tpl if="actions !== null">'
            + '<ul class="x-grid-buttons">'
            + '<tpl for="actions">'
            + '<li><i class="x-grid-action icon icon-{icon:htmlEncode}" title="{text:htmlEncode}" data-action="{action:htmlEncode}"></i></li>'
            + '</tpl>'
            + '</ul>'
            + '</tpl>'
            + '</tpl>', {
            compiled: true
        });
    },
    actionsColumnRenderer: function (value, metaData, record, rowIndex, colIndex, store) {
        return this._getActionsColumnTpl().apply({
            actions: this.getActions()
        });
    },
    onClick: function (e) {
        var target = e.getTarget();
        if (!target.classList.contains('x-grid-action')) return;
        if (!target.dataset.action) return;

        var actionHandler = 'action' + target.dataset.action.charAt(0).toUpperCase() + target.dataset.action.slice(1);
        if (!this[actionHandler] || (typeof this[actionHandler] !== 'function')) {
            actionHandler = target.dataset.action;
            if (!this[actionHandler] || (typeof this[actionHandler] !== 'function')) {
                return;
            }
        }

        var record = this.getSelectionModel().getSelected();
        var recordIndex = this.store.indexOf(record);
        this.menu.record = record.data;

        this[actionHandler](record, recordIndex, e);
    },
});
Ext.reg('crosslinks-json-grid', Crosslinks.grid.JsonGrid);
