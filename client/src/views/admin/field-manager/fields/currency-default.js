

Espo.define('views/admin/field-manager/fields/currency-default', 'views/fields/enum', function (Dep) {

    return Dep.extend({

        fetchEmptyValueAsNull: true,

        setupOptions: function () {
            this.params.options = [''];

            (this.getConfig().get('currencyList') || []).forEach(function (item) {
                this.params.options.push(item);
            }, this);
        },

    });

});
