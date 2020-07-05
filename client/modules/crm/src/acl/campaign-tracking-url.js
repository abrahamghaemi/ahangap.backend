

Espo.define('crm:acl/campaign-tracking-url', 'acl', function (Dep) {

    return Dep.extend({

        checkIsOwner: function (model) {
            if (model.has('campaignId')) {
                return true;
            }
            return false;
        },

        checkInTeam: function (model) {
            if (model.has('campaignId')) {
                return true;
            }
            return false;
        }
    });

});

