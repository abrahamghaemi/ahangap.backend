

Espo.define('views/admin/system-requirements/index', 'view', function (Dep) {

    return Dep.extend({

        template: 'admin/system-requirements/index',

        data: function () {
            return {
                phpRequirementList: this.requirementList.php,
                databaseRequirementList: this.requirementList.database,
                permissionRequirementList: this.requirementList.permission
            };
        },

        setup: function () {
            this.requirementList = [];
            this.ajaxGetRequest('Admin/action/systemRequirementList').then(function (requirementList) {
                this.requirementList = requirementList;
                if (this.isRendered() || this.isBeingRendered()) {
                    this.reRender();
                }
            }.bind(this));
        },
    });
});
