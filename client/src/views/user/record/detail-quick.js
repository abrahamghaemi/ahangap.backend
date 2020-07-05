

Espo.define('views/user/record/detail-quick', ['views/record/detail-small', 'views/user/record/detail'], function (Dep, Detail) {

    return Dep.extend({

        sideView: 'views/user/record/detail-quick-side',

        bottomView: null,

        editModeEnabled: false,

        setup: function () {
            Dep.prototype.setup.call(this);
            Detail.prototype.setupNonAdminFieldsAccess.call(this);
            Detail.prototype.setupFieldAppearance.call(this);
        },

        controlFieldAppearance: function () {
            Detail.prototype.controlFieldAppearance.call(this);
        }

    });

});
