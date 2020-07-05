

Espo.define('crm:views/mass-email/record/edit-small', ['views/record/edit-small', 'crm:views/mass-email/record/edit'], function (Dep, Edit) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);
            Edit.prototype.initFieldsControl.call(this);
        }
    });
});
