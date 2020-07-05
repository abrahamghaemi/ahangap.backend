

Espo.define('crm:views/knowledge-base-article/fields/status', 'views/fields/enum', function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);
            var publishDateWasSet = false;
            this.on('change', function () {
                if (this.model.get('status') === 'Published') {
                    if (!this.model.get('publishDate')) {
                        publishDateWasSet = true;
                        this.model.set('publishDate', this.getDateTime().getToday());
                    }
                } else {
                    if (publishDateWasSet) {
                        this.model.set('publishDate', null);
                    }
                }
            }, this);
        }

    });

});
