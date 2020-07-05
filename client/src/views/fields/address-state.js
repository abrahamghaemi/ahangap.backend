

Espo.define('views/fields/address-state', 'views/fields/varchar', function (Dep) {

    return Dep.extend({

        setupOptions: function () {
            var stateList = this.getConfig().get('addressStateList') || [];
            if (stateList.length) {
                this.params.options = Espo.Utils.clone(stateList);
            }
        },

    });
});
