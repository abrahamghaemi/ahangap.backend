

Espo.define('crm:views/campaign/subscribe-again', 'view', function (Dep) {

    return Dep.extend({

        template: 'crm:campaign/subscribe-again',

        data: function () {
            var data = {
                actionData: this.options.actionData
            };
            return data;
        }

    });
});
