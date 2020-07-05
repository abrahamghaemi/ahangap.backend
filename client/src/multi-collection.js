

define('multi-collection', ['collection'], function (Collection) {

    var MultiCollection = Collection.extend({

        /**
         * @prop {Object} seeds Hash off model classes.
         */
        seeds: null,

        initialize: function (models, options) {
            options = options || {};

            this.data = {};

            Backbone.Collection.prototype.initialize.call(this);
        },

        parse: function (resp, options) {
            this.total = resp.total;
            return resp.list.map(function (attributes) {
                var a = _.clone(attributes);
                delete a['_scope'];
                return new this.seeds[attributes._scope](a, options);
            }.bind(this));
        },

    });

    return MultiCollection;
});
