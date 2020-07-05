
Espo.define('models/user', 'model', function (Dep) {

    return Dep.extend({

        name: 'User',

        isAdmin: function () {
            return this.get('type') == 'admin' || this.isSuperAdmin();
        },

        isPortal: function () {
            return this.get('type') == 'portal';
        },

        isApi: function () {
            return this.get('type') == 'api';
        },

        isRegular: function () {
            return this.get('type') == 'regular';
        },

        isSystem: function () {
            return this.get('type') == 'system';
        },

        isSuperAdmin: function () {
            return this.get('type') == 'super-admin';
        }
    });
});
