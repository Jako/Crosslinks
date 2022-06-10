Crosslinks.grid.Links = function (config) {
    config = config || {};
    this.ident = 'crosslinks-links-' + Ext.id();
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
    Ext.applyIf(config, {
        id: this.ident + '-crosslinks-grid-links',
        url: Crosslinks.config.connectorUrl,
        baseParams: {
            action: 'mgr/link/getlist'
        },
        autosave: true,
        save_action: 'mgr/link/updatefromgrid',
        fields: ['id', 'text', 'resource', 'pagetitle', 'parameter'],
        autoHeight: true,
        paging: true,
        remoteSort: true,
        autoExpandColumn: 'text',
        showActionsColumn: false,
        columns: [{
            header: _('crosslinks.link_text'),
            dataIndex: 'text',
            sortable: true,
            width: 80,
            editor: {
                xtype: 'textfield'
            }
        }, {
            header: _('crosslinks.link_resource'),
            dataIndex: 'resource',
            sortable: true,
            width: 80,
            renderer: function (value, meta, record) {
                return record.data.pagetitle;
            }
        }, {
            header: _('crosslinks.link_parameter'),
            dataIndex: 'parameter',
            sortable: false,
            width: 30,
            renderer: function (v) {
                var iconclass = (v) ? 'icon-check' : 'icon-times';
                var color = (v) ? 'green' : 'red';
                return '<div style="text-align:center"><i class="icon ' + iconclass + ' ' + color + '"></i></div>';
            }
        }, {
            renderer: {
                fn: this.buttonColumnRenderer,
                scope: this
            },
            menuDisabled: true,
            width: 30
        }],
        tbar: [{
            text: _('crosslinks.link_create'),
            cls: 'primary-button',
            handler: this.createLink
        }, '->', {
            xtype: 'textfield',
            id: this.ident + '-crosslinks-filter-search',
            emptyText: _('search') + '…',
            submitValue: false,
            listeners: {
                change: {
                    fn: this.search,
                    scope: this
                },
                render: {
                    fn: function (cmp) {
                        new Ext.KeyMap(cmp.getEl(), {
                            key: Ext.EventObject.ENTER,
                            fn: function () {
                                this.fireEvent('change', this);
                                this.blur();
                                return true;
                            },
                            scope: cmp
                        });
                    },
                    scope: this
                }
            }
        }, {
            xtype: 'button',
            id: this.ident + '-crosslinks-filter-clear',
            cls: 'x-form-filter-clear',
            text: _('filter_clear'),
            listeners: {
                click: {
                    fn: this.clearFilter,
                    scope: this
                }
            }
        }]
    });
    Crosslinks.grid.Links.superclass.constructor.call(this, config)
};
Ext.extend(Crosslinks.grid.Links, MODx.grid.Grid, {
    windows: {},
    getMenu: function () {
        var m = [];
        m.push({
            text: _('crosslinks.link_update'),
            handler: this.updateLink
        });
        m.push('-');
        m.push({
            text: _('crosslinks.link_duplicate'),
            handler: this.duplicateLink
        });
        m.push('-');
        m.push({
            text: _('crosslinks.link_remove'),
            handler: this.removeLink
        });
        this.addContextMenuItem(m);
    },
    createLink: function (btn, e) {
        this.createUpdateLink(btn, e, false);
    },
    updateLink: function (btn, e) {
        this.createUpdateLink(btn, e, true);
    },
    createUpdateLink: function (btn, e, isUpdate) {
        var r;
        if (isUpdate) {
            if (!this.menu.record || !this.menu.record.id) {
                return false;
            }
            r = this.menu.record;
        } else {
            r = {
                parameter: '{}'
            };
        }
        var createUpdateLink = MODx.load({
            xtype: 'crosslinks-window-link-create-update',
            isUpdate: isUpdate,
            title: (isUpdate) ? _('crosslinks.link_update') : _('crosslinks.link_create'),
            record: r,
            listeners: {
                success: {
                    fn: this.refresh,
                    scope: this
                }
            }
        });
        createUpdateLink.fp.getForm().setValues(r);
        createUpdateLink.show(e.target);
    },
    duplicateLink: function (btn, e) {
        if (!this.menu.record) {
            return false;
        }
        var r = Ext.apply({}, this.menu.record);
        r.text = _('crosslinks.duplicate') + ' ' + r.text;
        r.id = null;
        var duplicateLink = MODx.load({
            xtype: 'crosslinks-window-link-create-update',
            isUpdate: false,
            title: _('crosslinks.link_duplicate'),
            record: r,
            listeners: {
                success: {
                    fn: this.refresh,
                    scope: this
                }
            }
        });
        duplicateLink.fp.getForm().setValues(r);
        duplicateLink.show(e.target);
    },
    removeLink: function () {
        if (!this.menu.record) {
            return false;
        }
        MODx.msg.confirm({
            title: _('crosslinks.link_remove'),
            text: _('crosslinks.link_remove_confirm'),
            url: this.config.url,
            params: {
                action: 'mgr/link/remove',
                id: this.menu.record.id
            },
            listeners: {
                success: {
                    fn: this.refresh,
                    scope: this
                }
            }
        });
    },
    search: function (tf) {
        var s = this.getStore();
        s.baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    },
    clearFilter: function () {
        var s = this.getStore();
        s.baseParams.query = '';
        Ext.getCmp(this.ident + '-crosslinks-filter-search').reset();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    },
    buttonColumnRenderer: function () {
        var values = {
            action_buttons: [
                {
                    className: 'update',
                    icon: 'pencil-square-o',
                    text: _('crosslinks.link_update')
                },
                {
                    className: 'duplicate',
                    icon: 'clone',
                    text: _('crosslinks.link_duplicate')
                },
                {
                    className: 'remove',
                    icon: 'trash-o',
                    text: _('crosslinks.link_remove')
                }
            ]
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
                    this.removeLink(record, e);
                    break;
                case 'duplicate':
                    this.duplicateLink(record, e);
                    break;
                case 'update':
                    this.updateLink(record, e);
                    break;
                default:
                    break;
            }
        }
    }
});
Ext.reg('crosslinks-grid-links', Crosslinks.grid.Links);

Crosslinks.window.CreateUpdateLink = function (config) {
    config = config || {};
    this.ident = 'crosslinks-link-create-update-' + Ext.id();
    Ext.applyIf(config, {
        id: this.ident,
        url: Crosslinks.config.connectorUrl,
        action: (config.isUpdate) ? 'mgr/link/update' : 'mgr/link/create',
        width: 400,
        autoHeight: true,
        closeAction: 'close',
        cls: 'modx-window crosslinks-window',
        fields: [{
            xtype: 'textfield',
            fieldLabel: _('crosslinks.link_text'),
            name: 'text',
            id: this.ident + '-text',
            anchor: '100%',
            allowBlank: false
        }, {
            xtype: 'crosslinks-combo-resource',
            fieldLabel: _('crosslinks.link_resource'),
            name: 'resource',
            id: this.ident + '-resource',
            anchor: '100%'
        }, {
            xtype: 'crosslinks-json-grid',
            style: 'padding-top: 10px',
            fieldLabel: _('crosslinks.link_parameter'),
            fieldConfig: [{
                name: 'key',
                width: 50,
                allowBlank: false,
                header: _('crosslinks.jsongrid_key')
            }, {
                name: 'value',
                width: 50,
                allowBlank: false,
                header: _('crosslinks.jsongrid_value')
            }],
            name: 'parameter',
            id: this.ident + '-parameter',
            cls: 'modx-grid modx-grid-small',
            anchor: '100%'
        }, {
            xtype: 'hidden',
            name: 'id'
        }]
    });
    Crosslinks.window.CreateUpdateLink.superclass.constructor.call(this, config);
};
Ext.extend(Crosslinks.window.CreateUpdateLink, MODx.Window);
Ext.reg('crosslinks-window-link-create-update', Crosslinks.window.CreateUpdateLink);
