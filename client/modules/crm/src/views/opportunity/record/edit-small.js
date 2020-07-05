

Espo.define('crm:views/opportunity/record/edit-small', ['views/record/edit-small', 'crm:views/opportunity/record/edit'], function (Dep, Edit) {

    return Dep.extend({

        populateDefaults: function () {
            Dep.prototype.populateDefaults.call(this);
            Edit.prototype.populateDefaults.call(this);
        }
    });
});

