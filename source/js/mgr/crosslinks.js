var crosslinks = function (config) {
    config = config || {};
    Ext.applyIf(config, {});
    crosslinks.superclass.constructor.call(this, config);
    return this;
};
Ext.extend(crosslinks, Ext.Component, {
    page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, util: {}
});
Ext.reg('crosslinks', crosslinks);

Crosslinks = new crosslinks();

MODx.config.help_url = 'https://jako.github.io/Crosslinks/usage/';
