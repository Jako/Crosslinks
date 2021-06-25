Crosslinks.grid.JsonGrid = function (config) {
    config = config || {};
    this.ident = config.ident || 'crosslinks-mecitem' + Ext.id();
    this.buttonColumnTpl = new Ext.XTemplate('<tpl for=".">'
        + '<tpl if="action_buttons !== null">'
        + '<ul class="action-buttons">'
        + '<tpl for="action_buttons">'
        + '<li><i class="icon {className} icon-{icon}" title="{text}"></i></li>'
        + '</tpl>'
        + '</ul>'
        + '</tpl>'
        + '</tpl>', {
        compiled: true
    });
    this.hiddenField = new Ext.form.TextArea({
        name: config.hiddenName || config.name,
        hidden: true
    });
    Ext.applyIf(config, {
        id: this.ident + '-json-grid',
        fields: ['id', 'key', 'value', 'rank'],
        autoHeight: true,
        store: new Ext.data.JsonStore({
            fields: ['id', 'key', 'value', 'rank'],
            data: Ext.util.JSON.decode(config.value)
        }),
        enableDragDrop: true,
        ddGroup: this.ident + '-json-grid-dd',
        autoExpandColumn: 'value',
        labelStyle: 'position: absolute',
        style: 'padding-top: 10px',
        columns: [{
            header: _('crosslinks.jsongrid_key'),
            dataIndex: 'key',
            editable: true,
            editor: {
                xtype: 'textfield',
                allowBlank: false,
                listeners: {
                    keydown: {
                        fn: this.saveValue,
                        scope: this
                    }
                }
            },
            width: 80
        }, {
            header: _('crosslinks.jsongrid_value'),
            dataIndex: 'value',
            editable: true,
            editor: {
                xtype: 'textfield',
                allowBlank: false,
                listeners: {
                    keydown: {
                        fn: this.saveValue,
                        scope: this
                    }
                }
            },
            width: 80
        }, {
            renderer: {
                fn: this.buttonColumnRenderer,
                scope: this
            },
            menuDisabled: true,
            width: 30,
            align: 'right'
        }, {
            dataIndex: 'id',
            hidden: true
        }, {
            dataIndex: 'rank',
            hidden: true
        }],
        tbar: ['->', {
            text: '<i class="icon icon-plus"></i> ' + _('add'),
            cls: 'primary-button',
            handler: this.addEntry,
            scope: this
        }],
        listeners: {
            render: {
                fn: this.renderListener,
                scope: this
            }
        }
    });
    Crosslinks.grid.JsonGrid.superclass.constructor.call(this, config)
};
Ext.extend(Crosslinks.grid.JsonGrid, MODx.grid.LocalGrid, {
    windows: {},
    getMenu: function () {
        var m = [];
        m.push({
            text: _('remove'),
            handler: this.removeEntry
        });
        return m;
    },
    addEntry: function () {
        var ds = this.getStore();
        var r = new ds.recordType({
            key: '',
            value: ''
        });
        this.getStore().insert(0, r);
        this.getView().refresh();
        this.getSelectionModel().selectRow(0);
    },
    removeEntry: function () {
        Ext.Msg.confirm(_('remove') || '', _('confirm_remove') || '', function (e) {
            if (e === 'yes') {
                var ds = this.getStore();
                var rows = this.getSelectionModel().getSelections();
                if (!rows.length) {
                    return false;
                }
                for (var i = 0; i < rows.length; i++) {
                    var id = rows[i].id;
                    var index = ds.findBy(function (record, id) {
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
    buttonColumnRenderer: function () {
        var values = {
            action_buttons: [{
                className: 'remove',
                icon: 'trash-o',
                text: _('remove')
            }]
        };
        return this.buttonColumnTpl.apply(values);
    },
    onClick: function (e) {
        var t = e.getTarget();
        var elm = t.className.split(' ')[0];
        if (elm === 'icon') {
            var act = t.className.split(' ')[1];
            var record = this.getSelectionModel().getSelected();
            this.menu.record = record.data;
            switch (act) {
                case 'remove':
                    this.removeEntry(record, e);
                    break;
                default:
                    break;
            }
        }
    },
    saveValue: function () {
        var value = [];
        Ext.each(this.getStore().getRange(), function (record) {
            if (record.data.key) {
                var element = {};
                element[record.data.key] = record.data.value;
                value.push(element);
            }
        });
        this.hiddenField.setValue(Ext.util.JSON.encode(value));
    }
});
Ext.reg('crosslinks-json-grid', Crosslinks.grid.JsonGrid);

Crosslinks.combo.JsonGrid = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        store: new Ext.data.JsonStore({
            fields: ['name', 'targetwidth', 'targetheight', 'targetRatio'],
            data: config.data
        }),
        mode: 'local',
        displayField: 'name',
        valueField: 'name',
        submitValue: false,
        triggerAction: 'all',
        listeners: {
            select: {
                fn: this.selectConfig,
                scope: this
            }
        }
    });
    Crosslinks.combo.JsonGrid.superclass.constructor.call(this, config);
};
Ext.extend(Crosslinks.combo.JsonGrid, MODx.combo.ComboBox, {
    selectConfig: function (c, v) {
        Ext.getCmp('inopt_targetWidth' + this.config.tvId).setValue(v.data.targetwidth);
        Ext.getCmp('inopt_targetHeight' + this.config.tvId).setValue(v.data.targetheight);
        Ext.getCmp('inopt_targetRatio' + this.config.tvId).setValue(v.data.targetRatio);
    }
});
Ext.reg('crosslinks-json-combo', Crosslinks.combo.JsonGrid);
