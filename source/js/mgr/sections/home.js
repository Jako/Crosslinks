Crosslinks.page.Home = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        buttons: [{
            text: _('help_ex'),
            handler: MODx.loadHelpPane
        }],
        formpanel: 'crosslinks-panel-home',
        components: [{
            xtype: 'crosslinks-panel-home'
        }]
    });
    Crosslinks.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(Crosslinks.page.Home, MODx.Component);
Ext.reg('crosslinks-page-home', Crosslinks.page.Home);
