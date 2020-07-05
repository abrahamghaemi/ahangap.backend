

Espo.define('crm:views/event-confirmation/confirmation', 'view', function (Dep) {

    return Dep.extend({

        template: 'crm:event-confirmation/confirmation',

        data: function () {
            var data = {
                actionData: this.options.actionData
            };
            return data;
        }

    });
});
