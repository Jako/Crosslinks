var crosslinks = function (config) {
    config = config || {};
    crosslinks.superclass.constructor.call(this, config);
};
Ext.extend(crosslinks, Ext.Component, {
    initComponent: function () {
        this.stores = {};
        this.ajax = new Ext.data.Connection({
            disableCaching: true,
        });
    }, page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, util: {}, form: {}
});
Ext.reg('crosslinks', crosslinks);

Crosslinks = new crosslinks();

MODx.config.help_url = 'https://jako.github.io/Crosslinks/usage/';
