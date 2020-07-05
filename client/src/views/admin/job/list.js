

Espo.define('views/admin/job/list', 'views/list', function (Dep) {

    return Dep.extend({

        createButton: false,

        setup: function () {
            Dep.prototype.setup.call(this);

            if (!this.getHelper().getAppParam('isRestrictedMode') || this.getUser().isSuperAdmin()) {
                this.addMenuItem('buttons', {
                    link: '#Admin/jobsSettings',
                    html: this.translate('Settings', 'labels', 'Admin')
                });
            }
        },

        getHeader: function () {
            return '<a href="#Admin">' + this.translate('Administration') + "</a> » " + this.getLanguage().translate('Jobs', 'labels', 'Admin');
        },

        updatePageTitle: function () {
            this.setPageTitle(this.getLanguage().translate('Jobs', 'labels', 'Admin'));
        }

    });
});
