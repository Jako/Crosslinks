Crosslinks.combo.Resource = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'resource',
        hiddenName: 'resource',
        displayField: 'pagetitle',
        valueField: 'id',
        fields: ['id', 'pagetitle'],
        pageSize: 10,
        minChars: 1,
        editable: true,
        triggerAction: 'all',
        typeAhead: false,
        forceSelection: true,
        selectOnFocus: false,
        url: Crosslinks.config.connectorUrl,
        mode: 'remote',
        baseParams: {
            action: 'mgr/resource/getlist',
            combo: true
        }
    });
    Crosslinks.combo.Resource.superclass.constructor.call(this, config);
};
Ext.extend(Crosslinks.combo.Resource, MODx.combo.ComboBox);
Ext.reg('crosslinks-combo-resource', Crosslinks.combo.Resource);
