

Espo.define('views/fields/address-city', 'views/fields/varchar', function (Dep) {

    return Dep.extend({

        setupOptions: function () {
            var cityList = this.getConfig().get('addressCityList') || [];
            if (cityList.length) {
                this.params.options = Espo.Utils.clone(cityList);
            }
        },

    });
});
