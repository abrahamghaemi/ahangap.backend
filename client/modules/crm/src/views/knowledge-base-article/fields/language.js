

Espo.define('crm:views/knowledge-base-article/fields/language', 'views/fields/enum', function (Dep) {

    return Dep.extend({

        setupOptions: function () {
            this.params.options = Espo.Utils.clone(this.getConfig().get('languageList') || []);
            this.params.options.unshift('');
            this.translatedOptions = Espo.Utils.clone(this.getLanguage().translate('language', 'options') || {});
            this.translatedOptions[''] = this.translate('Any', 'labels', 'KnowledgeBaseArticle')
        }

    });

});
