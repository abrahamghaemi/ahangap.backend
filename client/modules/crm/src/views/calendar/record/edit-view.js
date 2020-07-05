

Espo.define('crm:views/calendar/record/edit-view', 'views/record/base', function (Dep) {

    return Dep.extend({

        template: 'crm:calendar/record/edit-view',

        setup: function () {
            Dep.prototype.setup.call(this);

            this.createField('mode', 'views/fields/enum', {
                options: ['basicWeek', 'month', 'basicDay'],
                translation: 'DashletOptions.options.mode'
            }, null, null, {
                labelText: this.translate('mode', 'fields', 'DashletOptions')
            });

            this.createField('name', 'views/fields/varchar', {
                required: true
            }, null, null, {
                labelText: this.translate('name', 'fields')
            });

            this.createField('teams', 'crm:views/calendar/fields/teams', {
                required: true
            }, null, null, {
                labelText: this.translate('teams', 'fields'),
                foreignScope: 'Team'
            });
        }

    });
});
