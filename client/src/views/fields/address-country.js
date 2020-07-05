

Espo.define('views/fields/address-country', 'views/fields/varchar', function (Dep) {

    return Dep.extend({

        setupOptions: function () {
            var countryList = this.getConfig().get('addressCountryList') || [];
            if (countryList.length) {
                this.params.options = Espo.Utils.clone(countryList);
            }
        },

    });
});
