Crosslinks.panel.Home = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        cls: 'container home-panel' + ((Crosslinks.config.debug) ? ' debug' : ''),
        defaults: {
            collapsible: false,
            autoHeight: true
        },
        items: [{
            html: '<h2>' + _('crosslinks.management') + '</h2>' + ((Crosslinks.config.debug) ? '<div class="ribbon top-right"><span>' + _('crosslinks.debug_mode') + '</span></div>' : ''),
            border: false,
            cls: 'modx-page-header'
        }, {
            defaults: {
                autoHeight: true
            },
            border: true,
            items: [{
                xtype: 'crosslinks-panel-overview'
            }]
        }, {
            cls: "treehillstudio_about",
            html: '<img width="133" height="40" src="' + Crosslinks.config.assetsUrl + 'img/mgr/treehill-studio-small.png"' + ' srcset="' + Crosslinks.config.assetsUrl + 'img/mgr/treehill-studio-small@2x.png 2x" alt="Treehill Studio">',
            listeners: {
                afterrender: function (component) {
                    component.getEl().select('img').on('click', function () {
                        var msg = '<span style="display: inline-block; text-align: center"><img src="' + Crosslinks.config.assetsUrl + 'img/mgr/treehill-studio.png" srcset="' + Crosslinks.config.assetsUrl + 'img/mgr/treehill-studio@2x.png 2x" alt="Treehill Studio"><br>' +
                            '© 2018-2019 by <a href="https://treehillstudio.com" target="_blank">treehillstudio.com</a></span>';
                        Ext.Msg.show({
                            title: _('crosslinks') + ' ' + Crosslinks.config.version,
                            msg: msg,
                            buttons: Ext.Msg.OK,
                            cls: 'treehillstudio_window',
                            width: 330
                        });
                    });
                }
            }
        }]
    });
    Crosslinks.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(Crosslinks.panel.Home, MODx.Panel);
Ext.reg('crosslinks-panel-home', Crosslinks.panel.Home);

Crosslinks.panel.HomeTab = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        id: 'crosslinks-panel-' + config.tabtype,
        title: config.title,
        items: [{
            html: '<p>' + config.description + '</p>',
            border: false,
            cls: 'panel-desc'
        }, {
            layout: 'form',
            cls: 'x-form-label-left main-wrapper',
            defaults: {
                autoHeight: true
            },
            border: true,
            items: [{
                id: 'crosslinks-panel-' + config.tabtype + '-grid',
                xtype: 'crosslinks-grid-' + config.tabtype,
                preventRender: true
            }]
        }]
    });
    Crosslinks.panel.HomeTab.superclass.constructor.call(this, config);
};
Ext.extend(Crosslinks.panel.HomeTab, MODx.Panel);
Ext.reg('crosslinks-panel-hometab', Crosslinks.panel.HomeTab);

Crosslinks.panel.Overview = function (config) {
    config = config || {};
    this.ident = 'crosslinks-panel-overview' + Ext.id();
    this.panelOverviewTabs = [{
        xtype: 'crosslinks-panel-hometab',
        title: _('crosslinks.links'),
        description: _('crosslinks.links_desc'),
        tabtype: 'links'
    }];
    if (Crosslinks.config.is_admin) {
        this.panelOverviewTabs.push({
            xtype: 'crosslinks-panel-settings',
            title: _('crosslinks.settings'),
            description: _('crosslinks.settings_desc'),
            tabtype: 'settings'
        })
    }
    Ext.applyIf(config, {
        id: this.ident,
        items: [{
            xtype: 'modx-tabs',
            stateful: true,
            stateId: 'crosslinks-panel-overview',
            stateEvents: ['tabchange'],
            getState: function () {
                return {
                    activeTab: this.items.indexOf(this.getActiveTab())
                };
            },
            autoScroll: true,
            deferredRender: false,
            forceLayout: true,
            defaults: {
                layout: 'form',
                autoHeight: true,
                hideMode: 'offsets'
            },
            items: this.panelOverviewTabs,
            listeners: {
                tabchange: function (o, t) {
                    if (t.tabtype === 'settings') {
                        Ext.getCmp('crosslinks-grid-system-settings').getStore().reload();
                    } else if (t.xtype === 'crosslinks-panel-hometab') {
                        if (Ext.getCmp('crosslinks-panel-' + t.tabtype + '-grid')) {
                            Ext.getCmp('crosslinks-panel-' + t.tabtype + '-grid').getStore().reload();
                        }
                    }
                }
            }
        }]
    });
    Crosslinks.panel.Overview.superclass.constructor.call(this, config);
};
Ext.extend(Crosslinks.panel.Overview, MODx.Panel);
Ext.reg('crosslinks-panel-overview', Crosslinks.panel.Overview);
