

Espo.define('views/fields/currency-list', 'views/fields/enum', function (Dep) {

    return Dep.extend({

        setupOptions: function () {
            this.params.options = [];

            (this.getConfig().get('currencyList') || []).forEach(function (item) {
                this.params.options.push(item);
            }, this);
        },

    });
});
