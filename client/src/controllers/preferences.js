

define('controllers/preferences', ['controllers/record', 'models/preferences'], function (Dep, Preferences) {

    return Dep.extend({

        defaultAction: 'own',

        getModel: function (callback) {
            var model = new Preferences();
            model.settings = this.getConfig();
            model.defs = this.getMetadata().get('entityDefs.Preferences');
            if (callback) {
                callback.call(this, model);
            }
            return new Promise(function (resolve) {
                resolve(model);
            });
        },

        checkAccess: function (action) {
            return true;
        },

        actionOwn: function () {
            this.actionEdit({
                id: this.getUser().id
            });
        },

        actionList: function () {}
    });
});
