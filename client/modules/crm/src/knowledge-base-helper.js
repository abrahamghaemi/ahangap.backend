

Espo.define('crm:knowledge-base-helper', 'ajax', function (Ajax) {

    var KnowledgeBaseHelper = function (language) {
        this.language = language;
    }

    _.extend(KnowledgeBaseHelper.prototype, {

        getLanguage: function () {
            return this.language;
        },

        getAttributesForEmail: function (model, attributes, callback) {
            attributes = attributes || {};
            attributes.body = model.get('body');
            if (attributes.name) {
                attributes.name = attributes.name + ' ';
            } else {
                attributes.name = '';
            }
            attributes.name += this.getLanguage().translate('KnowledgeBaseArticle', 'scopeNames') + ': ' + model.get('name');

            Ajax.postRequest('KnowledgeBaseArticle/action/getCopiedAttachments', {
                id: model.id
            }).then(function (data) {
                attributes.attachmentsIds = data.ids;
                attributes.attachmentsNames = data.names;
                attributes.isHtml = true;

                callback(attributes);
            }.bind(this));
        }
    });

    return KnowledgeBaseHelper;

});
