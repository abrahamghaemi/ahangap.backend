

Espo.define('crm:views/mass-email/fields/email-template', 'views/fields/link', function (Dep) {

    return Dep.extend({

        getCreateAttributes: function () {
            return {
                oneOff: true
            }
        },

    });

});
