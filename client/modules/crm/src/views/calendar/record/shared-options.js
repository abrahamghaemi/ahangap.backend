

Espo.define('crm:views/calendar/record/shared-options', 'views/record/base', function (Dep) {

    return Dep.extend({

        template: 'crm:calendar/record/shared-options',

        setup: function () {
            Dep.prototype.setup.call(this);
            this.createField('users', 'crm:views/calendar/fields/users');
        },

    });
});

