

Espo.define('crm:views/document/fields/file', 'views/fields/file', function (Dep) {

    return Dep.extend({

        getValueForDisplay: function () {
            if (this.mode == 'list') {
                var name = this.model.get(this.nameName);
                var type = this.model.get(this.typeName) || this.defaultType;
                var id = this.model.get(this.idName);

                if (!id) {
                    return false;
                }

                return '<a title="'+name+'" href="'+this.getBasePath()+'?entryPoint=download&id=' + id + '" target="_BLANK"><span class="fas fa-paperclip small"></span></a>';
            } else {
                return Dep.prototype.getValueForDisplay.call(this);
            }
        },

    });

});
