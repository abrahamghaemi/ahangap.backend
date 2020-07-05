

Espo.define('views/api-user/list', 'views/list', function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);
        },

        actionCreate: function () {
            var attributes = {
                type: 'api'
            };

            var router = this.getRouter();
            var url = '#' + this.scope + '/create';
            router.dispatch(this.scope, 'create', {
                attributes: attributes
            });
            router.navigate(url, {trigger: false});
        }

    });
});
