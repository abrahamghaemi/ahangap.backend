

Espo.define('crm:views/dashlets/options/chart', 'views/dashlets/options/base', function (Dep) {

    return Dep.extend({

        setupBeforeFinal: function () {
            this.listenTo(this.model, 'change:dateFilter', this.controlDateFilter);
            this.controlDateFilter();
        },

        controlDateFilter: function () {
            if (this.model.get('dateFilter') === 'between') {
                this.showField('dateFrom');
                this.showField('dateTo');
            } else {
                this.hideField('dateFrom');
                this.hideField('dateTo');
            }
        }

    });
});
