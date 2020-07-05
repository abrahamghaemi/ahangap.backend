
Espo.define('collections/note', 'collection', function (Dep) {

    return Dep.extend({

        parse: function (response, params) {
            var total = this.total;
            var list = Dep.prototype.parse.call(this, response, params);

            if (params.data && params.data.after) {
                if (total >= 0 && response.total >= 0) {
                    this.total = total + response.total;
                } else {
                    this.total = total;
                }
            }
            return list;
        },

        fetchNew: function (options) {
            var options = options || {};
            options.data = options.data || {};

            if (this.length) {
                options.data.after = this.models[0].get('createdAt');
                options.remove = false;
                options.at = 0;
                options.maxSize = null;
            }

            this.fetch(options);
        },

    });

});
