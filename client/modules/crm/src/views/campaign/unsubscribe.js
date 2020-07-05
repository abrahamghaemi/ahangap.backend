

Espo.define('crm:views/campaign/unsubscribe', 'view', function (Dep) {

    return Dep.extend({

        template: 'crm:campaign/unsubscribe',

        data: function () {
            var data = {
                actionData: this.options.actionData
            };
            return data;
        }

    });
});
