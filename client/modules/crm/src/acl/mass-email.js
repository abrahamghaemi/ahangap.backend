

Espo.define('crm:acl/mass-email', 'acl', function (Dep) {

    return Dep.extend({

        checkIsOwner: function (model) {
            if (model.has('campaignId')) {
                return true;
            } else {
                return Dep.prototype.checkIsOwner.call(this, model);
            }
        },

        checkInTeam: function (model) {
            if (model.has('campaignId')) {
                return true;
            } else {
                return Dep.prototype.checkInTeam.call(this, model);
            }
        }
    });

});

