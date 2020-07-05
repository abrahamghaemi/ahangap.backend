

define('crm:controllers/calendar', 'controller', function (Dep) {

    return Dep.extend({

        checkAccess: function () {
            if (this.getAcl().check('Calendar')) {
                return true;
            }
            return false;
        },

        actionShow: function (options) {
            this.actionIndex(options);
        },

        actionIndex: function (options) {
            this.handleCheckAccess();

            this.main('crm:views/calendar/calendar-page', {
                date: options.date,
                mode: options.mode,
                userId: options.userId,
                userName: options.userName
            });
        },
    });
});
